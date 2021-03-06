<?php
/**
 * Squiz_Sniffs_CSS_ColourDefinitionSniff.
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
namespace Hippo\Sniffs\CSS;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Squiz_Sniffs_CSS_ColourDefinitionSniff.
 *
 * Ensure colours are defined in upper-case and do not use shortcuts.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: 1.4.3
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

class ColourDefinitionSniff implements Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = ['CSS'];


    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return array(int)
     */
    public function register()
    {
        return [T_COLOUR];

    }//end register()


    /**
     * Processes the tokens that this sniff is interested in.
     *
     * @param File $phpcsFile The file where the token was found.
     * @param int  $stackPtr  The position in the stack where
     *                        the token was found.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $colour = $tokens[$stackPtr]['content'];

        $expected = strtoupper($colour);
        if ($colour !== $expected) {
            $error = 'CSS colours must be defined in uppercase; expected %s but found %s';
            $data  = [
                $expected,
                $colour,
            ];

            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'NotUpper', $data);
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken($stackPtr, $expected);
            }
        }

        // Now check if shorthand is used.
        if (strlen($colour) === 4) {
            $expected = '#'.$colour[1].$colour[1].$colour[2].$colour[2].$colour[3].$colour[3];
            $error    = 'CSS colours must use hex triplets; expected %s but found %s';
            $data     = [
                $expected,
                $colour,
            ];

            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Triplet', $data);
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken($stackPtr, $expected);
            }
        }

    }//end process()


}//end class
