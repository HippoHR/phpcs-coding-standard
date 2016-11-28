<?php
/**
 * Hippo_Sniffs_Hippo_ControlStructureSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Dennis Broeks <dennis@uitzendbureau.nl>
 */

/**
 * Hippo_Sniffs_Hippo_ControlStructureSniff.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Dennis Broeks <dennis@uitzendbureau.nl>
 */
class Hippo_Sniffs_Hippo_ControlStructureSniff implements PHP_CodeSniffer_Sniff
{
   private $csParenthesis = array(
       T_WHILE,
       T_FOR,
       T_FOREACH,
       T_IF,
       T_ELSEIF,
       T_CATCH,
       T_SWITCH
   );


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
            T_WHILE,
            T_FOR,
            T_FOREACH,
            T_IF,
            T_ELSEIF,
            T_ELSE,
            T_TRY,
            T_CATCH,
            T_SWITCH
        );
    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile All the tokens found in the document.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        // Get tokens.
        $tokens = $phpcsFile->getTokens();

        // Get first token after the control structure definition.
        $next = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, $stackPtr+1, null, true);

        if( in_array( $tokens[$stackPtr]['code'], $this->csParenthesis ) )
        {
          $closeParenthesis = $tokens[$next]['parenthesis_closer'];
          $next = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, $closeParenthesis+1, null, true);
        }

        // $next must be a curly bracket OR a ;
        if( $tokens[$next]['code'] != T_SEMICOLON && $tokens[$next]['code'] != T_OPEN_CURLY_BRACKET )
        {
            $error = 'All control structures must use curly brackets.';
            $phpcsFile->addError( $error, $stackPtr, 'ControlStructureNoCurlyBrackets' );
        }
    }//end process()


}//end class
