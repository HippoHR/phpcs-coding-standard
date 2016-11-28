<?php
/**
 * Hippo_Sniffs_Hippo_OperatorSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Dennis Broeks <dennis@uitzendbureau.nl>
 */

/**
 * Hippo_Sniffs_Hippo_OperatorSniff.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Dennis Broeks <dennis@uitzendbureau.nl>
 */
class Hippo_Sniffs_Hippo_OperatorSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array_unique( array_merge(
            PHP_CodeSniffer_Tokens::$operators,
            PHP_CodeSniffer_Tokens::$booleanOperators,
            PHP_CodeSniffer_Tokens::$comparisonTokens,
            PHP_CodeSniffer_Tokens::$assignmentTokens,
            PHP_CodeSniffer_Tokens::$arithmeticTokens,
            PHP_CodeSniffer_Tokens::$equalityTokens,
            array(
                T_STRING_CONCAT
            )
        ) );
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

        $prev = $phpcsFile->findPrevious(null, $stackPtr-1, null, true);
        $next = $phpcsFile->findNext(null, $stackPtr+1, null, true);

        $allowNoSpacesNext = false;
        $allowMultipleSpacesPrev = false;
        $allowMultipleSpacesNext = false;

        // In case the '-' operator is used, we want to allow '4 * -1'.
        // -( $y * 2 )   -> Allowed
        // 3 * -1        -> Allowed
        // -1 * 3        -> Allowed
        // ( -1 ) * 3    -> Allowed0485520012
        // 3 -1          -> Not allowed
        // $Y->z -$x     -> Not allowed
        // TODO: Improve this. For how, we simply allow any case where there is either 1 or 0 space on the right side of a T_MINUS.
        if( $tokens[ $stackPtr ]['code'] === T_MINUS || $tokens[ $stackPtr ]['code'] === T_BITWISE_AND )
        {
            $allowNoSpacesNext = true;
        }

        $prevCont = $tokens[$prev]['content'];
        $nextCont = $tokens[$next]['content'];
        $prevLen = strlen( $prevCont );
        $nextLen = strlen( $nextCont );
        $prevType = $tokens[$prev]['code'];
        $nextType = $tokens[$next]['code'];

        // If there is a new line in the whitespace, exclude it from the check.
        if( strpos( $prevCont, "\n" ) !== false )
        {
            $allowMultipleSpacesPrev = true;
        }
        if( strpos( $nextCont, "\n" ) !== false )
        {
            $allowMultipleSpacesNext = true;
        }


        // All tokens must be wrapped inside single spaces on both sides.
        if( ( $prevType === T_WHITESPACE && $prevLen > 1 && !$allowMultipleSpacesPrev ) ||
            $prevType !== T_WHITESPACE ||
            ( $nextType === T_WHITESPACE && $nextLen > 1 && !$allowMultipleSpacesNext ) ||
            ( $nextType !== T_WHITESPACE && !$allowNoSpacesNext )
        )
        {
            $error = 'Operators, comparison, assignment, arithmetic, concatenation and equality tokens must be surrounded by single spaces on both sides. Operator: ' . $tokens[$stackPtr]['content'];
            $phpcsFile->addError( $error, $stackPtr, 'OperatorSpacing' );
        }
    }//end process()


}//end class
