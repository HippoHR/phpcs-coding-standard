<?php
/**
 * Squiz_Sniffs_CSS_ClassDefinitionOpeningBraceSpaceSniff.
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
use PHP_CodeSniffer\Util\Tokens;

/**
 * Squiz_Sniffs_CSS_ClassDefinitionOpeningBraceSpaceSniff.
 *
 * Ensure there is a single space before the opening brace in a class definition
 * and the content starts on the next line.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: 1.4.3
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

class ClassDefinitionOpeningBraceSpaceSniff implements Sniff
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
        return [T_OPEN_CURLY_BRACKET];

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

        /*
            There are several valid cases:
            1. top level selector + 1 new line + opening bracket OR
            2. media query ending with parenthesis + 1 new line + opening bracket
            3. nested selector + 1 new line + some indentation spaces + opening bracket
        */

        $isNested = false;
        $prBr     = $phpcsFile->findPrevious(T_OPEN_CURLY_BRACKET, ($stackPtr - 1));
        if ($prBr !== false) {
            $isNested = ($stackPtr > $tokens[$prBr]['bracket_opener']) && ($stackPtr < $tokens[$prBr]['bracket_closer']);
        }

        $case1 = $stackPtr >= 2 && $tokens[($stackPtr - 2)]['code'] == T_STRING && $tokens[($stackPtr - 1)]['content'] == PHP_EOL;

        $case2 = $stackPtr >= 2 && $tokens[($stackPtr - 2)]['code'] == T_CLOSE_PARENTHESIS && $tokens[($stackPtr - 1)]['content'] == PHP_EOL;

        $case3 = $isNested && $stackPtr >= 3 && $tokens[($stackPtr - 3)]['code'] == T_STRING && $tokens[($stackPtr - 2)]['content'] == PHP_EOL &&
          $tokens[($stackPtr - 1)]['code'] == T_WHITESPACE;

        if (!($case1 || $case2 || $case3)) {
            $error = 'Expected 1 newline before opening brace of class definition; 0 or more than 1 found';
            $phpcsFile->addError($error, $stackPtr, 'NoneBefore');
        }

        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        if ($next === false) {
            return;
        }

        // Check for nested class definitions.
        $nested = false;
        $found  = $phpcsFile->findNext(
            T_OPEN_CURLY_BRACKET,
            ($stackPtr + 1),
            $tokens[$stackPtr]['bracket_closer']
        );
        if ($found !== false) {
            $nested = true;
        }

        $foundLines = ($tokens[$next]['line'] - $tokens[$stackPtr]['line'] - 1);
        if ($nested === true) {
            if ($foundLines !== 1) {
                $error = 'Expected 1 blank line after opening brace of nesting class definition; %s found';
                $data  = [$foundLines];
                $phpcsFile->addError($error, $stackPtr, 'AfterNesting', $data);
            }
        } else {
            if ($foundLines !== 0) {
                $error = 'Expected 0 blank lines after opening brace of class definition; %s found';
                $data  = [$foundLines];
                $phpcsFile->addError($error, $stackPtr, 'After', $data);
            }
        }

    }//end process()


}//end class
