<?php

namespace SilverorangeLegacy\Sniffs\Classes;

use PHP_CodeSniffer\Standards\Squiz\Sniffs\Classes\ClassFileNameSniff as
SquizClassFileNameSniff;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

/**
 * Contains code from Squiz\Sniffs\Classes\ClassFileNameSniff
 * Tests that the file name and the name of the class declared in the file match
 * Ignored if filepath contains /admin/components/
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
class ClassFileNameSniff extends SquizClassFileNameSniff
{
    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $fullPath = basename($phpcsFile->getFilename());
        $fileName = mb_substr($fullPath, 0, mb_strrpos($fullPath, '.'));

        /*
         * Ignore filenames in two cases:
         * No filename, probably means STDIN, can't do this check
         * File path contains /admin/components/, which in the silverorange
         * site structure usually have different class names
         */
        if ($fileName === '' ||
            mb_stripos($phpcsFile->getFilename(), '/admin/components/') !== false) {
            return;
        }
        $tokens  = $phpcsFile->getTokens();
        $decName = $phpcsFile->findNext(T_STRING, $stackPtr);
        if ($tokens[$decName]['content'] !== $fileName) {
            $error = '%s name doesn\'t match filename; expected "%s %s"';
            $data = array(
                ucfirst($tokens[$stackPtr]['content']),
                $tokens[$stackPtr]['content'],
                $fileName,
            );
            $phpcsFile->addError($error, $stackPtr, 'NoMatch', $data);
        }
    }
}
