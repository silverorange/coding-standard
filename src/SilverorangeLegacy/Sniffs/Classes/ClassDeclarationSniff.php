<?php

namespace SilverorangeLegacy\Sniffs\Classes;

use PHP_CodeSniffer\Standards\PEAR\Sniffs\Classes\ClassDeclarationSniff as PearClassDeclarationSniff;
use PHP_CodeSniffer\Util\Tokens;
use PHP_CodeSniffer\Files\File;

/**
 * Contains code from Squiz\Sniffs\Classes\ClassDeclarationSniff and
 * code from PSR2\Sniffs\Classes\ClassDeclarationSniff
 * Checks the declaration of the class and its inheritance is correct.
 * Customized to allow for an indented new line if the declaration exceeds a character limit
 * Used under the following license:
 *
 *  Copyright (c) 2012, Squiz Pty Ltd (ABN 77 084 670 600)
 *  All rights reserved.
 *
 *  Redistribution and use in source and binary forms, with or without
 *  modification, are permitted provided that the following conditions are met:
 *
 *      * Redistributions of source code must retain the above copyright
 *        notice, this list of conditions and the following disclaimer.
 *      * Redistributions in binary form must reproduce the above copyright
 *        notice, this list of conditions and the following disclaimer in the
 *        documentation and/or other materials provided with the distribution.
 *      * Neither the name of Squiz Pty Ltd nor the
 *        names of its contributors may be used to endorse or promote products
 *        derived from this software without specific prior written permission.
 *
 *  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 *  AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 *  IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 *  ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 *  LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 *  CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 *  SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 *  INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 *  CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 *  ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 *  POSSIBILITY OF SUCH DAMAGE.
 */
class ClassDeclarationSniff extends PEARClassDeclarationSniff
{
    /**
     * The number of spaces code should be indented.
     *
     * @var integer
     */
    public $indent = 4;

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        // We want all the errors from the PEAR standard, plus some of our own.
        parent::process($phpcsFile, $stackPtr);
        // Just in case.
        $tokens = $phpcsFile->getTokens();
        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            return;
        }
        $this->processOpen($phpcsFile, $stackPtr);
        $this->processClose($phpcsFile, $stackPtr);

        // Check that this is the only class or interface in the file.
        $nextClass = $phpcsFile->findNext(array(T_CLASS, T_INTERFACE), ($stackPtr + 1));
        if ($nextClass !== false) {
            // We have another, so an error is thrown.
            $error = 'Only one interface or class is allowed in a file';
            $phpcsFile->addError($error, $nextClass, 'MultipleClasses');
        }
    }

    /**
     * Processes the opening section of a class declaration.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function processOpen(File $phpcsFile, $stackPtr)
    {
        $tokens       = $phpcsFile->getTokens();
        $stackPtrType = strtolower($tokens[$stackPtr]['content']);
        // Check alignment of the keyword and braces.
        if ($tokens[($stackPtr - 1)]['code'] === T_WHITESPACE) {
            $prevContent = $tokens[($stackPtr - 1)]['content'];
            if ($prevContent !== $phpcsFile->eolChar) {
                $blankSpace = substr($prevContent, strpos($prevContent, $phpcsFile->eolChar));
                $spaces     = strlen($blankSpace);
                if (in_array($tokens[($stackPtr - 2)]['code'], array(T_ABSTRACT, T_FINAL)) === true
                    && $spaces !== 1
                ) {
                    $prevContent = strtolower($tokens[($stackPtr - 2)]['content']);
                    $error = 'Expected 1 space between %s and %s keywords; %s found';
                    $data = array(
                        $prevContent,
                        $stackPtrType,
                        $spaces,
                    );
                    $fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceBeforeKeyword', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($stackPtr - 1), ' ');
                    }
                }
            } elseif ($tokens[($stackPtr - 2)]['code'] === T_ABSTRACT
                || $tokens[($stackPtr - 2)]['code'] === T_FINAL
            ) {
                $prevContent = strtolower($tokens[($stackPtr - 2)]['content']);
                $error = 'Expected 1 space between %s and %s keywords; newline found';
                $data = array(
                    $prevContent,
                    $stackPtrType,
                );
                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'NewlineBeforeKeyword', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken(($stackPtr - 1), ' ');
                }
            }//end if
        }//end if
        // We'll need the indent of the class/interface declaration for later.
        $classIndent = 0;
        for ($i = ($stackPtr - 1); $i > 0; $i--) {
            if ($tokens[$i]['line'] === $tokens[$stackPtr]['line']) {
                continue;
            }
            // We changed lines.
            if ($tokens[($i + 1)]['code'] === T_WHITESPACE) {
                $classIndent = strlen($tokens[($i + 1)]['content']);
            }
            break;
        }
        $className = $phpcsFile->findNext(T_STRING, $stackPtr);
        // Spacing of the keyword.
        $gap = $tokens[($stackPtr + 1)]['content'];
        if (strlen($gap) !== 1) {
            $found = strlen($gap);
            $error = 'Expected 1 space between %s keyword and %s name; %s found';
            $data  = array(
                $stackPtrType,
                $stackPtrType,
                $found,
            );
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceAfterKeyword', $data);
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken(($stackPtr + 1), ' ');
            }
        }
        // Check after the class/interface name.
        if ($tokens[($className + 2)]['line'] === $tokens[$className]['line']) {
            $gap = $tokens[($className + 1)]['content'];
            if (strlen($gap) !== 1) {
                $found = strlen($gap);
                $error = 'Expected 1 space after %s name; %s found';
                $data  = array(
                    $stackPtrType,
                    $found,
                );
                $fix = $phpcsFile->addFixableError($error, $className, 'SpaceAfterName', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken(($className + 1), ' ');
                }
            }
        }
        $openingBrace = $tokens[$stackPtr]['scope_opener'];
        // Check positions of the extends and implements keywords.
        foreach (array('extends', 'implements') as $keywordType) {
            $keyword = $phpcsFile->findNext(constant('T_'.strtoupper($keywordType)), ($stackPtr + 1), $openingBrace);
            if ($keyword !== false) {
                if ($tokens[$keyword]['line'] !== $tokens[$stackPtr]['line']) {
                    $error = 'The '.$keywordType.' keyword must be on the same line as the %s name';
                    $data  = array($stackPtrType);
                    $fix   = $phpcsFile->addFixableError($error, $keyword, ucfirst($keywordType).'Line', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();
                        for ($i = ($stackPtr + 1); $i < $keyword; $i++) {
                            if ($tokens[$i]['line'] !== $tokens[($i + 1)]['line']) {
                                $phpcsFile->fixer->substrToken($i, 0, (strlen($phpcsFile->eolChar) * -1));
                            }
                        }
                        $phpcsFile->fixer->addContentBefore($keyword, ' ');
                        $phpcsFile->fixer->endChangeset();
                    }
                } else {
                    // Check the whitespace before. Whitespace after is checked
                    // later by looking at the whitespace before the first class name
                    // in the list.
                    $gap = strlen($tokens[($keyword - 1)]['content']);
                    if ($gap !== 1) {
                        $error = 'Expected 1 space before '.$keywordType.' keyword; %s found';
                        $data  = array($gap);
                        $fix = $phpcsFile->addFixableError(
                            $error,
                            $keyword,
                            'SpaceBefore'.ucfirst($keywordType),
                            $data
                        );
                        if ($fix === true) {
                            $phpcsFile->fixer->replaceToken(($keyword - 1), ' ');
                        }
                    }
                }//end if
            }//end if
        }//end foreach
        // Check each of the extends/implements class names. If the extends/implements
        // keyword is the last content on the line, it means we need to check for
        // the multi-line format, so we do not include the class names
        // from the extends/implements list in the following check.
        // Note that classes can only extend one other class, so they can't use a
        // multi-line extends format, whereas an interface can extend multiple
        // other interfaces, and so uses a multi-line extends format.
        if ($tokens[$stackPtr]['code'] === T_INTERFACE) {
            $keywordTokenType = T_EXTENDS;
        } else {
            $keywordTokenType = T_IMPLEMENTS;
        }
        $implements          = $phpcsFile->findNext($keywordTokenType, ($stackPtr + 1), $openingBrace);
        $multiLineImplements = false;
        if ($implements !== false) {
            $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($openingBrace - 1), $implements, true);
            if ($tokens[$prev]['line'] !== $tokens[$implements]['line']) {
                $multiLineImplements = true;
            }
        }
        $find = array(
                 T_STRING,
                 $keywordTokenType,
                );
        $classNames = array();
        $nextClass  = $phpcsFile->findNext($find, ($className + 2), ($openingBrace - 1));
        while ($nextClass !== false) {
            $classNames[] = $nextClass;
            $nextClass    = $phpcsFile->findNext($find, ($nextClass + 1), ($openingBrace - 1));
        }
        $classCount         = count($classNames);
        $checkingImplements = false;
        $implementsToken    = null;
        foreach ($classNames as $i => $className) {
            if ($tokens[$className]['code'] === $keywordTokenType) {
                $checkingImplements = true;
                $implementsToken    = $className;
                continue;
            }
            if ($checkingImplements === true
                && $multiLineImplements === true
                && ($tokens[($className - 1)]['code'] !== T_NS_SEPARATOR
                || $tokens[($className - 2)]['code'] !== T_STRING)
            ) {
                $prev = $phpcsFile->findPrevious(
                    array(
                     T_NS_SEPARATOR,
                     T_WHITESPACE,
                    ),
                    ($className - 1),
                    $implements,
                    true
                );
                if ($prev === $implementsToken && $tokens[$className]['line'] !== ($tokens[$prev]['line'] + 1)) {
                    if ($keywordTokenType === T_EXTENDS) {
                        $error = 'The first item in a multi-line extends list '
                            . 'must be on the line following the extends keyword';
                        $fix   = $phpcsFile->addFixableError($error, $className, 'FirstExtendsInterfaceSameLine');
                    } else {
                        $error = 'The first item in a multi-line implements list '
                            . 'must be on the line following the implements keyword';
                        $fix   = $phpcsFile->addFixableError($error, $className, 'FirstInterfaceSameLine');
                    }
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();
                        for ($i = ($prev + 1); $i < $className; $i++) {
                            if ($tokens[$i]['code'] !== T_WHITESPACE) {
                                break;
                            }
                            $phpcsFile->fixer->replaceToken($i, '');
                        }
                        $phpcsFile->fixer->addNewline($prev);
                        $phpcsFile->fixer->endChangeset();
                    }
                } elseif ($tokens[$prev]['line'] !== ($tokens[$className]['line'] - 1)) {
                    if ($keywordTokenType === T_EXTENDS) {
                        $error = 'Only one interface may be specified per line in a multi-line extends declaration';
                        $fix   = $phpcsFile->addFixableError($error, $className, 'ExtendsInterfaceSameLine');
                    } else {
                        $error = 'Only one interface may be specified per line in a multi-line implements declaration';
                        $fix   = $phpcsFile->addFixableError($error, $className, 'InterfaceSameLine');
                    }
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();
                        for ($i = ($prev + 1); $i < $className; $i++) {
                            if ($tokens[$i]['code'] !== T_WHITESPACE) {
                                break;
                            }
                            $phpcsFile->fixer->replaceToken($i, '');
                        }
                        $phpcsFile->fixer->addNewline($prev);
                        $phpcsFile->fixer->endChangeset();
                    }
                } else {
                    $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($className - 1), $implements);
                    if ($tokens[$prev]['line'] !== $tokens[$className]['line']) {
                        $found = 0;
                    } else {
                        $found = strlen($tokens[$prev]['content']);
                    }
                    $expected = ($classIndent + $this->indent);
                    if ($found !== $expected) {
                        $error = 'Expected %s spaces before interface name; %s found';
                        $data  = array(
                                  $expected,
                                  $found,
                                 );
                        $fix   = $phpcsFile->addFixableError($error, $className, 'InterfaceWrongIndent', $data);
                        if ($fix === true) {
                            $padding = str_repeat(' ', $expected);
                            if ($found === 0) {
                                $phpcsFile->fixer->addContent($prev, $padding);
                            } else {
                                $phpcsFile->fixer->replaceToken($prev, $padding);
                            }
                        }
                    }
                }//end if
            } elseif ($tokens[($className - 1)]['code'] !== T_NS_SEPARATOR
                || $tokens[($className - 2)]['code'] !== T_STRING
            ) {
                // Not part of a longer fully qualified class name.
                if ($tokens[($className - 1)]['code'] === T_COMMA
                    || ($tokens[($className - 1)]['code'] === T_NS_SEPARATOR
                    && $tokens[($className - 2)]['code'] === T_COMMA)
                ) {
                    $error = 'Expected 1 space before "%s"; 0 found';
                    $data  = array($tokens[$className]['content']);
                    $fix   = $phpcsFile->addFixableError($error, ($nextComma + 1), 'NoSpaceBeforeName', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->addContentBefore(($nextComma + 1), ' ');
                    }
                } else {
                    if ($tokens[($className - 1)]['code'] === T_NS_SEPARATOR) {
                        $prev = ($className - 2);
                    } else {
                        $prev = ($className - 1);
                    }
                    $spaceBefore = strlen($tokens[$prev]['content']);
                    // This is where the silverorange customization is specified
                    // Adds indent check and disables phpcbf fixing for this rule
                    // (Because phpcbf applies an incorrect style when it fixes)
                    if ($spaceBefore !== 1 && $spaceBefore !== $this->indent) {
                        $error = 'Expected 1 space or indented new line before "%s"; %s found';
                        $data  = array(
                            $tokens[$className]['content'],
                            $spaceBefore,
                        );
                        $fix = $phpcsFile->addFixableError($error, $className, 'SpaceBeforeName', $data);
                    }
                }//end if
            }//end if
            if ($checkingImplements === true
                && $tokens[($className + 1)]['code'] !== T_NS_SEPARATOR
                && $tokens[($className + 1)]['code'] !== T_COMMA
            ) {
                if ($i !== ($classCount - 1)) {
                    // This is not the last class name, and the comma
                    // is not where we expect it to be.
                    if ($tokens[($className + 2)]['code'] !== $keywordTokenType) {
                        $error = 'Expected 0 spaces between "%s" and comma; %s found';
                        $data  = array(
                                  $tokens[$className]['content'],
                                  strlen($tokens[($className + 1)]['content']),
                                 );
                        $fix = $phpcsFile->addFixableError($error, $className, 'SpaceBeforeComma', $data);
                        if ($fix === true) {
                            $phpcsFile->fixer->replaceToken(($className + 1), '');
                        }
                    }
                }
                $nextComma = $phpcsFile->findNext(T_COMMA, $className);
            } else {
                $nextComma = ($className + 1);
            }//end if
        }//end foreach

        if ($tokens[($stackPtr - 1)]['code'] === T_WHITESPACE) {
            $prevContent = $tokens[($stackPtr - 1)]['content'];
            if ($prevContent !== $phpcsFile->eolChar) {
                $blankSpace = substr($prevContent, strpos($prevContent, $phpcsFile->eolChar));
                $spaces     = strlen($blankSpace);
                if ($tokens[($stackPtr - 2)]['code'] !== T_ABSTRACT
                    && $tokens[($stackPtr - 2)]['code'] !== T_FINAL
                ) {
                    if ($spaces !== 0) {
                        $type  = strtolower($tokens[$stackPtr]['content']);
                        $error = 'Expected 0 spaces before %s keyword; %s found';
                        $data  = array(
                                  $type,
                                  $spaces,
                                 );
                        $fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceBeforeKeyword', $data);
                        if ($fix === true) {
                            $phpcsFile->fixer->replaceToken(($stackPtr - 1), '');
                        }
                    }
                }
            }
        }
    }

    /**
     * Processes the closing section of a class declaration.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function processClose(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        if (isset($tokens[$stackPtr]['scope_closer']) === false) {
            return;
        }
        $closeBrace = $tokens[$stackPtr]['scope_closer'];
        // Check that the closing brace has one blank line after it.
        for ($nextContent = ($closeBrace + 1); $nextContent < $phpcsFile->numTokens; $nextContent++) {
            // Ignore comments on the same lines as the brace.
            if ($tokens[$nextContent]['line'] === $tokens[$closeBrace]['line']
                && ($tokens[$nextContent]['code'] === T_WHITESPACE
                || $tokens[$nextContent]['code'] === T_COMMENT)
            ) {
                continue;
            }
            if ($tokens[$nextContent]['code'] !== T_WHITESPACE) {
                break;
            }
        }
        if ($nextContent === $phpcsFile->numTokens) {
            // Ignore the line check as this is the very end of the file.
            $difference = 1;
        } else {
            $difference = ($tokens[$nextContent]['line'] - $tokens[$closeBrace]['line'] - 1);
        }
        $lastContent = $phpcsFile->findPrevious(T_WHITESPACE, ($closeBrace - 1), $stackPtr, true);
        if ($difference === -1
            || $tokens[$lastContent]['line'] === $tokens[$closeBrace]['line']
        ) {
            $error = 'Closing %s brace must be on a line by itself';
            $data  = array($tokens[$stackPtr]['content']);
            $fix   = $phpcsFile->addFixableError($error, $closeBrace, 'CloseBraceSameLine', $data);
            if ($fix === true) {
                if ($difference === -1) {
                    $phpcsFile->fixer->addNewlineBefore($nextContent);
                }
                if ($tokens[$lastContent]['line'] === $tokens[$closeBrace]['line']) {
                    $phpcsFile->fixer->addNewlineBefore($closeBrace);
                }
            }
        } elseif ($tokens[($closeBrace - 1)]['code'] === T_WHITESPACE) {
            $prevContent = $tokens[($closeBrace - 1)]['content'];
            if ($prevContent !== $phpcsFile->eolChar) {
                $blankSpace = substr($prevContent, strpos($prevContent, $phpcsFile->eolChar));
                $spaces     = strlen($blankSpace);
                if ($spaces !== 0) {
                    if ($tokens[($closeBrace - 1)]['line'] !== $tokens[$closeBrace]['line']) {
                        $error = 'Expected 0 spaces before closing brace; newline found';
                        $phpcsFile->addError($error, $closeBrace, 'NewLineBeforeCloseBrace');
                    } else {
                        $error = 'Expected 0 spaces before closing brace; %s found';
                        $data  = array($spaces);
                        $fix   = $phpcsFile->addFixableError($error, $closeBrace, 'SpaceBeforeCloseBrace', $data);
                        if ($fix === true) {
                            $phpcsFile->fixer->replaceToken(($closeBrace - 1), '');
                        }
                    }
                }
            }
        }//end if
        if ($difference !== -1 && $difference !== 1) {
            $error = 'Closing brace of a %s must be followed by a single blank line; found %s';
            $data  = array(
                      $tokens[$stackPtr]['content'],
                      $difference,
                     );
            $fix   = $phpcsFile->addFixableError($error, $closeBrace, 'NewlinesAfterCloseBrace', $data);
            if ($fix === true) {
                if ($difference === 0) {
                    $first = $phpcsFile->findFirstOnLine(array(), $nextContent, true);
                    $phpcsFile->fixer->addNewlineBefore($first);
                } else {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = ($closeBrace + 1); $i < $nextContent; $i++) {
                        if ($tokens[$i]['line'] <= ($tokens[$closeBrace]['line'] + 1)) {
                            continue;
                        } elseif ($tokens[$i]['line'] === $tokens[$nextContent]['line']) {
                            break;
                        }
                        $phpcsFile->fixer->replaceToken($i, '');
                    }
                    $phpcsFile->fixer->endChangeset();
                }
            }
        }
    }
}
