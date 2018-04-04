<?php
/**
 * Hippo_Sniffs_Hippo_StringSniff.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Dennis Broeks <dennis@uitzendbureau.nl>
 * @license  https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
namespace Hippo\Sniffs\Hippo;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Hippo_Sniffs_Hippo_StringSniff.
 *
 * Applies restrictions to double quoted strings.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Dennis Broeks <dennis@uitzendbureau.nl>
 */

class StringSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            // No var: 'Hello.', "Hello.", "Hello.\n".
            T_CONSTANT_ENCAPSED_STRING,
            // Contains var: "Hello $name.".
            T_DOUBLE_QUOTED_STRING,
        ];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param File $phpcsFile All the tokens found in the document.
     * @param int  $stackPtr  The position of the current token
     *                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        // Get tokens.
        $tokens = $phpcsFile->getTokens();

        // CHECK 1: T_DOUBLE_QUOTED_STRING's are not allowed.
        if ($tokens[$stackPtr]['code'] === T_DOUBLE_QUOTED_STRING) {
            $error = 'It\'s not allowed to use variables inside double quoted strings. Found: '.$tokens[$stackPtr]['content'];
            $phpcsFile->addError($error, $stackPtr, 'VariableInsideDoubleQuotedString');
        } else {
            // CHECK 2: Check content of double quoted string. Double quoted strings may only be used for special characters like "\n".
            // http://www.php.net/manual/en/language.types.string.php#language.types.string.syntax.double
            // Check the content of the double quoted string.
            $string = $tokens[$stackPtr]['content'];

            // Exclude single quoted strings. Unfortunately, when having al multiline string like this:
            // "SELECT *
            // FROM jobs
            // WHERE id = 1;"
            // 3 T_CONSTANT_ENCAPSED_STRING tokens are generated. So we need to find the first one (or the last one) and find out if single quotes
            // or double quotes are used.
            $firstString = $stackPtr;
            while (true) {
                // Only set $firstString when the condition is true.
                $ptr = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($firstString - 1), null, true);
                if ($tokens[$ptr]['code'] === T_CONSTANT_ENCAPSED_STRING) {
                    $firstString = $ptr;
                } else {
                    break;
                }
            }

            // Exclude single quoted strings.
            if (substr($tokens[$firstString]['content'], 0, 1) === '\'') {
                return;
            }

             // Remove leading quote.
            $string = ltrim($string, '"');

            // Remove trailing ", if not escaped.
            if (substr($string, 0, -2) !== '\"') {
                $string = rtrim($string, '"');
            }

            // Find out if there are illegal characters in the input string.
            // Not allowed: ""
            // Not allowed: "Hello"
            // Allowed: "\n\t\r\v\e\f\\\$\"\0\00\000\xA9\xFF\xa9\xff".
            if (preg_match('/^((\\\\[ntrvef$\\\\"])|(\\\\[0-7]{1,3})|(\\\\x[0-9A-Fa-f]{1,2}))+$/', $string) !== 1) {
                 $error = 'Double quoted string contains illegal character(s) or is empty. Found: '.$tokens[$stackPtr]['content'];
                 $phpcsFile->addError($error, $stackPtr, 'DoubleQuotedStringIllegalCharacter');
            }
        }//end if

    }//end process()


}//end class
