<?php

class Silverorange_Sniffs_WhiteSpace_OperatorSpacingSniff
	extends Squiz_Sniffs_Whitespace_OperatorSpacingSniff
{
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		$tokens = $phpcsFile->getTokens();

	   // Skip default values in function declarations.
	   if ($tokens[$stackPtr]['code'] === T_EQUAL
		   || $tokens[$stackPtr]['code'] === T_MINUS
	   ) {
		   if (isset($tokens[$stackPtr]['nested_parenthesis']) === true) {
			   $parenthesis = array_keys($tokens[$stackPtr]['nested_parenthesis']);
			   $bracket     = array_pop($parenthesis);
			   if (isset($tokens[$bracket]['parenthesis_owner']) === true) {
				   $function = $tokens[$bracket]['parenthesis_owner'];
				   if ($tokens[$function]['code'] === T_FUNCTION
					   || $tokens[$function]['code'] === T_CLOSURE
				   ) {
					   return;
				   }
			   }
		   }
	   }

	   if ($tokens[$stackPtr]['code'] === T_EQUAL) {
		   // Skip for '=&' case.
		   if (isset($tokens[($stackPtr + 1)]) === true
			   && $tokens[($stackPtr + 1)]['code'] === T_BITWISE_AND
		   ) {
			   return;
		   }
	   }

	   // Skip short ternary such as: "$foo = $bar ?: true;".
	   if (($tokens[$stackPtr]['code'] === T_INLINE_THEN
		   && $tokens[($stackPtr + 1)]['code'] === T_INLINE_ELSE)
		   || ($tokens[($stackPtr - 1)]['code'] === T_INLINE_THEN
		   && $tokens[$stackPtr]['code'] === T_INLINE_ELSE)
	   ) {
			   return;
	   }

	   if ($tokens[$stackPtr]['code'] === T_BITWISE_AND) {
		   // If it's not a reference, then we expect one space either side of the
		   // bitwise operator.
		   if ($phpcsFile->isReference($stackPtr) === true) {
			   return;
		   }

		   // Check there is one space before the & operator.
		   if ($tokens[($stackPtr - 1)]['code'] !== T_WHITESPACE) {
			   $error = 'Expected 1 space before "&" operator; 0 found';
			   $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceBeforeAmp');
			   if ($fix === true) {
				   $phpcsFile->fixer->addContentBefore($stackPtr, ' ');
			   }

			   $phpcsFile->recordMetric($stackPtr, 'Space before operator', 0);
		   } else {
			   if ($tokens[($stackPtr - 2)]['line'] !== $tokens[$stackPtr]['line']) {
				   $found = 'newline';
			   } else {
				   $found = $tokens[($stackPtr - 1)]['length'];
			   }

			   $phpcsFile->recordMetric($stackPtr, 'Space before operator', $found);
			   if ($found !== 1
				   && ($found !== 'newline' || $this->ignoreNewlines === false)
			   ) {
				   $error = 'Expected 1 space before "&" operator; %s found';
				   $data  = array($found);
				   $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingBeforeAmp', $data);
				   if ($fix === true) {
					   $phpcsFile->fixer->replaceToken(($stackPtr - 1), ' ');
				   }
			   }
		   }//end if

		   // Check there is one space after the & operator.
		   if ($tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE) {
			   $error = 'Expected 1 space after "&" operator; 0 found';
			   $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceAfterAmp');
			   if ($fix === true) {
				   $phpcsFile->fixer->addContent($stackPtr, ' ');
			   }

			   $phpcsFile->recordMetric($stackPtr, 'Space after operator', 0);
		   } else {
			   if ($tokens[($stackPtr + 2)]['line'] !== $tokens[$stackPtr]['line']) {
				   $found = 'newline';
			   } else {
				   $found = $tokens[($stackPtr + 1)]['length'];
			   }

			   $phpcsFile->recordMetric($stackPtr, 'Space after operator', $found);
			   if ($found !== 1
				   && ($found !== 'newline' || $this->ignoreNewlines === false)
			   ) {
				   $error = 'Expected 1 space after "&" operator; %s found';
				   $data  = array($found);
				   $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingAfterAmp', $data);
				   if ($fix === true) {
					   $phpcsFile->fixer->replaceToken(($stackPtr + 1), ' ');
				   }
			   }
		   }//end if

		   return;
	   }//end if

	   if ($tokens[$stackPtr]['code'] === T_MINUS || $tokens[$stackPtr]['code'] === T_PLUS) {
		   // Check minus spacing, but make sure we aren't just assigning
		   // a minus value or returning one.
		   $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
		   if ($tokens[$prev]['code'] === T_RETURN) {
			   // Just returning a negative value; eg. (return -1).
			   return;
		   }

		   if (isset(PHP_CodeSniffer_Tokens::$operators[$tokens[$prev]['code']]) === true) {
			   // Just trying to operate on a negative value; eg. ($var * -1).
			   return;
		   }

		   if (isset(PHP_CodeSniffer_Tokens::$comparisonTokens[$tokens[$prev]['code']]) === true) {
			   // Just trying to compare a negative value; eg. ($var === -1).
			   return;
		   }

		   if (isset(PHP_CodeSniffer_Tokens::$booleanOperators[$tokens[$prev]['code']]) === true) {
			   // Just trying to compare a negative value; eg. ($var || -1 === $b).
			   return;
		   }

		   if (isset(PHP_CodeSniffer_Tokens::$assignmentTokens[$tokens[$prev]['code']]) === true) {
			   // Just trying to assign a negative value; eg. ($var = -1).
			   return;
		   }

		   // A list of tokens that indicate that the token is not
		   // part of an arithmetic operation.
		   $invalidTokens = array(
							 T_COMMA               => true,
							 T_OPEN_PARENTHESIS    => true,
							 T_OPEN_SQUARE_BRACKET => true,
							 T_OPEN_SHORT_ARRAY    => true,
							 T_DOUBLE_ARROW        => true,
							 T_COLON               => true,
							 T_INLINE_THEN         => true,
							 T_INLINE_ELSE         => true,
							 T_CASE                => true,
							);

		   if (isset($invalidTokens[$tokens[$prev]['code']]) === true) {
			   // Just trying to use a negative value; eg. myFunction($var, -2).
			   return;
		   }
	   }//end if

	   $operator = $tokens[$stackPtr]['content'];

	   if ($tokens[$stackPtr]['code'] === T_CONCAT_EQUAL) {
		   if ($tokens[($stackPtr - 1)]['code'] === T_WHITESPACE) {
			   $error = "Expected no space before \"$operator\"";
			   $phpcsFile->addError($error, $stackPtr, 'SpaceBeforeConcatEquals');
			   $phpcsFile->recordMetric($stackPtr, 'No space before concat-equals operator', 0);
		   }
	   } elseif ($tokens[($stackPtr - 1)]['code'] !== T_WHITESPACE) {
		   $error = "Expected 1 space before \"$operator\"; 0 found";
		   $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceBefore');
		   if ($fix === true) {
			   $phpcsFile->fixer->addContentBefore($stackPtr, ' ');
		   }

		   $phpcsFile->recordMetric($stackPtr, 'Space before operator', 0);
	   } else if (isset(PHP_CodeSniffer_Tokens::$assignmentTokens[$tokens[$stackPtr]['code']]) === false) {
		   // Don't throw an error for assignments, because other standards allow
		   // multiple spaces there to align multiple assignments.
		   if ($tokens[($stackPtr - 2)]['line'] !== $tokens[$stackPtr]['line']) {
			   $found = 'newline';
		   } else {
			   $found = $tokens[($stackPtr - 1)]['length'];
		   }

		   $phpcsFile->recordMetric($stackPtr, 'Space before operator', $found);
		   if ($found !== 1
			   && ($found !== 'newline' || $this->ignoreNewlines === false)
		   ) {
			   $error = 'Expected 1 space before "%s"; %s found';
			   $data  = array(
						 $operator,
						 $found,
						);
			   $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingBefore', $data);
			   if ($fix === true) {
				   $phpcsFile->fixer->beginChangeset();
				   if ($found === 'newline') {
					   $i = ($stackPtr - 2);
					   while ($tokens[$i]['code'] === T_WHITESPACE) {
						   $phpcsFile->fixer->replaceToken($i, '');
						   $i--;
					   }
				   }

				   $phpcsFile->fixer->replaceToken(($stackPtr - 1), ' ');
				   $phpcsFile->fixer->endChangeset();
			   }
		   }//end if
	   }//end if

	   if (isset($tokens[($stackPtr + 1)]) === false) {
		   return;
	   }

	   if ($tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE) {
		   $error = "Expected 1 space after \"$operator\"; 0 found";
		   $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceAfter');
		   if ($fix === true) {
			   $phpcsFile->fixer->addContent($stackPtr, ' ');
		   }

		   $phpcsFile->recordMetric($stackPtr, 'Space after operator', 0);
	   } else {
		   if (isset($tokens[($stackPtr + 2)]) === true
			   && $tokens[($stackPtr + 2)]['line'] !== $tokens[$stackPtr]['line']
		   ) {
			   $found = 'newline';
		   } else {
			   $found = $tokens[($stackPtr + 1)]['length'];
		   }

		   $phpcsFile->recordMetric($stackPtr, 'Space after operator', $found);
		   if ($found !== 1
			   && ($found !== 'newline' || $this->ignoreNewlines === false)
		   ) {
			   $error = 'Expected 1 space after "%s"; %s found';
			   $data  = array(
						 $operator,
						 $found,
						);
			   $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingAfter', $data);
			   if ($fix === true) {
				   $phpcsFile->fixer->replaceToken(($stackPtr + 1), ' ');
			   }
		   }
	   }//end if

   }//end process()

}
