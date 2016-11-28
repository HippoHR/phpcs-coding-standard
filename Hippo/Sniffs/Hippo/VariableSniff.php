<?php
/**
 * Hippo_Sniffs_Hippo_VariableSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Dennis Broeks <dennis@uitzendbureau.nl>
 */

/**
 * Hippo_Sniffs_Hippo_VariableSniff.
 *
 * Checks wether variable names are in StudlyCaps or camelCase.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Dennis Broeks <dennis@uitzendbureau.nl>
 */
class Hippo_Sniffs_Hippo_VariableSniff implements PHP_CodeSniffer_Sniff
{
    // http://php.net/manual/en/reserved.variables.php
    private $specialVars = array(
        '$_SERVER',
        '$_REQUEST',
        '$_GET',
        '$_POST',
        '$_SESSION',
        '$_FILES',
        '$_ENV',
        '$_COOKIE',
        '$php_errormsg',
        '$HTTP_RAW_POST_DATA',
        '$http_response_header'
    );

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_VARIABLE);

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
        // TODO:
        // - This script should also check for T_STRING_VARNAME, T_NUM_STRING and T_ENCAPSED_AND_WHITESPACE (although PHPCS does not seem to recognise these...)

        // Get tokens.
        $tokens = $phpcsFile->getTokens();

        $var = $tokens[$stackPtr]['content'];

        //echo $var . "\n";

        // Exclude special variables.
        if( in_array( $var, $this->specialVars ) )
        {
            return;
        }

        // Is this a class var?
        // Exclude class methods and $this->{$x} formats. ($x will be handled seperately)
        $next = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        $next2 = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($next + 1), null, true);
        $next3 = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($next2 + 1), null, true);
        if( $tokens[$next]['code'] == T_OBJECT_OPERATOR &&
            !( $tokens[$next3]['code'] == T_OPEN_PARENTHESIS || $tokens[$next2]['code'] == T_OPEN_CURLY_BRACKET ) )
        {
            // Recursively, check field name.
            $this->process($phpcsFile, $next2);
        }

        // Variable names must be camelCase or StudlyCaps.
        if( !preg_match( '/^\$?[a-z0-9]+$/i', $var ) )
        {
            $error = 'All variables must be in camelCase or StudlyCaps. Found "' . $var . '".';
            $phpcsFile->addError( $error, $stackPtr, 'VariableFormat' );
        }

    }//end process()


}//end class
