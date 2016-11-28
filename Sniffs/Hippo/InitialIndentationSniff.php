<?php
/**
 * Hippo_Sniffs_Hippo_InitialIndentationSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Dennis Broeks <dennis@uitzendbureau.nl>
 */

/**
 * Hippo_Sniffs_Hippo_InitialIndentationSniff.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Dennis Broeks <dennis@uitzendbureau.nl>
 */
class Hippo_Sniffs_Hippo_InitialIndentationSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
            T_OPEN_TAG
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

        // Find next token.
        $next = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, $stackPtr+1, null, true);

        // Determine indentation of this next token.
        if( ( $tokens[$next]['column'] - 1 ) !== 0 )
        {
            $error = 'The initial indentation should be 0. Found: ' . ( $tokens[$next]['column'] - 1 );
            $phpcsFile->addError( $error, $stackPtr, 'IncorrectInitialIndentation' );
        }

    }//end process()


}//end class
