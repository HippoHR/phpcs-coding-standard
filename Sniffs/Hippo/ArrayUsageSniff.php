<?php
/**
 * Hippo_Sniffs_Hippo_ArrayUsageSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Dennis Broeks <dennis@uitzendbureau.nl>
 */

/**
 * Hippo_Sniffs_Hippo_ArrayUsageSniff.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Dennis Broeks <dennis@uitzendbureau.nl>
 */
class Hippo_Sniffs_Hippo_ArrayUsageSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
            T_OPEN_SQUARE_BRACKET,   // [
            T_CLOSE_SQUARE_BRACKET   // ]
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

        if( $tokens[ $stackPtr ][ 'code' ] == T_OPEN_SQUARE_BRACKET )
        {
            // CHECK 1: A T_OPEN_SQUARE_BRACKET must be followed by a single space OR a close bracket.
            $next = $phpcsFile->findNext(null, $stackPtr+1, null, true);
            if( $tokens[ $next ][ 'code' ] == T_WHITESPACE )
            {
                // If there is no content, tell user no spaces should be used.
                $next2 = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, $next+1, null, true);
                if( $tokens[ $next2 ][ 'code' ] == T_CLOSE_SQUARE_BRACKET )
                {
                     $error = 'No spacing allowed between opening and closing square brackets when there is no content.';
                     $phpcsFile->addError( $error, $stackPtr, 'IncorrectSpacingSquareBracket' );
                }
                elseif( $tokens[ $next ][ 'content' ] != ' ' )
                {
                     $error = 'Exactly one space between the opening square bracket and the content of the brackets is required.';
                     $phpcsFile->addError( $error, $stackPtr, 'IncorrectSpacingSquareBracket' );
                }
            }
            elseif( $tokens[ $next ][ 'code' ] != T_CLOSE_SQUARE_BRACKET )
            {
                $error = 'Exactly one space between the opening square bracket and the content of the brackets is required. Found 0.';
                $phpcsFile->addError( $error, $stackPtr, 'IncorrectSpacingSquareBracket' );
            }
        }
        elseif( $tokens[ $stackPtr ][ 'code' ] == T_CLOSE_SQUARE_BRACKET  )
        {
            // CHECK 2: A T_CLOSE_SQUARE_BRACKET must be preceded by a single space OR a open bracket.
            $prev = $phpcsFile->findPrevious(null, $stackPtr-1, null, true);
            if( $tokens[ $prev ][ 'code' ] == T_WHITESPACE )
            {
                $prev2 = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, $prev-1, null, true);
                if( $tokens[ $prev2 ][ 'code' ] == T_OPEN_SQUARE_BRACKET )
                {
                      // Handled in CHECK 1. We do not want double error reports.
                }
                elseif( $tokens[ $prev ][ 'content' ] != ' ' )
                {
                     $error = 'Exactly one space before the closing square bracket and the content of the brackets is required.';
                     $phpcsFile->addError( $error, $stackPtr, 'IncorrectSpacingSquareBracket' );
                }
            }
            elseif( $tokens[ $prev ][ 'code' ] != T_OPEN_SQUARE_BRACKET )
            {
                $error = 'Exactly one space between the closing square bracket and the content of the brackets is required. Found 0.';
                $phpcsFile->addError( $error, $stackPtr, 'IncorrectSpacingSquareBracket' );
            }
        }


        //$error = 'It\'s not allowed to use variables inside double quoted strings. Found: ' . $tokens[ $stackPtr ][ 'content' ];
        //$phpcsFile->addError( $error, $stackPtr, 'VariableInsideDoubleQuotedString' );

    }//end process()


}//end class
