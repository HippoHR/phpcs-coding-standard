<?php
/**
 * Hippo\Sniffs\Commenting\InlineBlockCommentSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

namespace Hippo\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class InlineBlockCommentSniff implements Sniff
{


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[]
     * @see    Tokens.php
     */
    public function register()
    {
        return [
            T_COMMENT,
            T_DOC_COMMENT_OPEN_TAG,
        ];

    }//end register()


    /**
     * Called when one of the token types that this sniff is listening for
     * is found.
     *
     * The stackPtr variable indicates where in the stack the token was found.
     * A sniff can acquire information this token, along with all the other
     * tokens within the stack by first acquiring the token stack:
     *
     * <code>
     *    $tokens = $phpcsFile->getTokens();
     *    echo 'Encountered a '.$tokens[$stackPtr]['type'].' token';
     *    echo 'token information: ';
     *    print_r($tokens[$stackPtr]);
     * </code>
     *
     * If the sniff discovers an anomaly in the code, they can raise an error
     * by calling addError() on the \PHP_CodeSniffer\Files\File object, specifying an error
     * message and the position of the offending token:
     *
     * <code>
     *    $phpcsFile->addError('Encountered an error', $stackPtr);
     * </code>
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where the
     *                                               token was found.
     * @param int                         $stackPtr  The position in the PHP_CodeSniffer
     *                                               file's token stack where the token
     *                                               was found.
     *
     * @return void|int Optionally returns a stack pointer. The sniff will not be
     *                  called again on the current file until the returned stack
     *                  pointer is reached. Return (count($tokens) + 1) to skip
     *                  the rest of the file.
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // If it's an inline comment, return.
        if (substr($tokens[$stackPtr]['content'], 0, 2) !== '/*') {
            return;
        }

        if ($tokens[$stackPtr]['code'] === T_DOC_COMMENT_OPEN_TAG) {
            $end = $tokens[$stackPtr]['comment_closer'];

            // Skip any multiline doc comments.
            if ($tokens[$stackPtr]['line'] !== $tokens[$end]['line']) {
                return;
            }

            $content = '';
            for ($i = ($stackPtr + 1); $i < $end; $i++) {
                $content .= $tokens[$i]['content'];
            }
        } else if ($tokens[$stackPtr]['code'] === T_COMMENT) {
            // Only handle single line comments.
            if (strpos($tokens[$stackPtr]['content'], '/*') === false || strpos($tokens[$stackPtr]['content'], '*/') === false) {
                return;
            }

            $content = trim($tokens[$stackPtr]['content'], '/*');
        }

        // Don't handle empty comments.
        if (empty($content) === true) {
            return;
        }

        $expected      = 1;
        $length        = strlen($content);
        $leadingSpace  = ($length - strlen(ltrim($content)));
        $trailingSpace = ($length - strlen(rtrim($content)));

        if ($leadingSpace !== $expected) {
            $data = [
                $expected,
                $leadingSpace,
            ];
            $phpcsFile->addError('Comment should start with %d whitespace, but found %d', $stackPtr, 'LeadingWhitespace', $data);
        }

        if ($trailingSpace !== $expected) {
            $data = [
                $expected,
                $trailingSpace,
            ];
            $phpcsFile->addError('Comment should end with %d whitespace, but found %d', $stackPtr, 'TrailingWhitespace', $data);
        }

    }//end process()


}//end class
