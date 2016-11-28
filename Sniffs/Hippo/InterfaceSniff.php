<?php
/**
 * Hippo_Sniffs_Hippo_InterfaceSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Dennis Broeks <dennis@uitzendbureau.nl>
 */

/** 
 * Hippo_Sniffs_Hippo_InterfaceSniff.
 *
 * Checks wether interface names are correct.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Dennis Broeks <dennis@uitzendbureau.nl>
 */         
class Hippo_Sniffs_Hippo_InterfaceSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */ 
    public function register()
    {   
        return array(T_INTERFACE);
    
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
        
        $interfaceNamePtr = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        $interfaceName = $tokens[$interfaceNamePtr]['content'];
        
        // Variable names must be camelCase or StudlyCaps.
        if( substr( $interfaceName, 0, 1 ) !== 'I' )
        {   
            $error = 'Interface names must begin with an I. Found "' . $interfaceName . '".';
            $phpcsFile->addError( $error, $stackPtr, 'InterfaceName' );
        }

    }//end process()

    
}//end class

?>
