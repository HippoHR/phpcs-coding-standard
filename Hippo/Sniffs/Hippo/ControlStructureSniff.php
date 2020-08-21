<?php
/**
 * Hippo_Sniffs_Hippo_ControlStructureSniff.
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
 * Hippo_Sniffs_Hippo_ControlStructureSniff.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Dennis Broeks <dennis@uitzendbureau.nl>
 */

class ControlStructureSniff implements Sniff
{

    /**
     * Tokens to find parenthesis near.
     *
     * @var array
     */
    private $csParenthesis = [
        T_WHILE,
        T_FOR,
        T_FOREACH,
        T_IF,
        T_ELSEIF,
        T_CATCH,
        T_SWITCH,
    ];


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_WHILE,
            T_FOR,
            T_FOREACH,
            T_IF,
            T_ELSEIF,
            T_ELSE,
            T_TRY,
            T_CATCH,
            T_SWITCH,
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

        // Get first token after the control structure definition.
        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);

        if (in_array($tokens[$stackPtr]['code'], $this->csParenthesis) === true) {
            $closeParenthesis = $tokens[$next]['parenthesis_closer'];
            $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($closeParenthesis + 1), null, true);
        }

        // $next must be a curly bracket OR a ;
        if ($tokens[$next]['code'] !== T_SEMICOLON && $tokens[$next]['code'] !== T_OPEN_CURLY_BRACKET) {
            $error = 'All control structures must use curly brackets.';
            $phpcsFile->addError($error, $stackPtr, 'ControlStructureNoCurlyBrackets');
        }

    }//end process()


}//end class
