<?php
/**
 * Hippo_Sniffs_Hippo_NamespaceSniff.
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
 * Hippo_Sniffs_Hippo_NamespaceSniff.
 *
 * Checks wether namespaces are used correctly.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Dennis Broeks <dennis@uitzendbureau.nl>
 */

class NamespaceSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_NAMESPACE,
                T_USE,
               );

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
        // TODO:
        // - No constants may be defined in a namespace (except class constants)
        // - No functions may be defined in a namespace (except class methods)
        // Get tokens.
        $tokens = $phpcsFile->getTokens();

        // CHECK 1: Make sure there is max 1 T_NAMESPACE in this file.
        if ($tokens[$stackPtr]['code'] === T_NAMESPACE) {
            foreach ($tokens as $ptr => $token) {
                if ($ptr !== $stackPtr && $token['code'] === T_NAMESPACE) {
                    $error = 'Only one namespace per file is allowed.';
                    $phpcsFile->addError($error, $stackPtr, 'MaxNamespacesPerFile');
                    break;
                }
            }
        }

        // CHECK 2: Make sure no namespace blocks are used ("namespace AnotherProject { ... }").
        if ($tokens[$stackPtr]['code'] === T_NAMESPACE) {
            $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);

            // Find first interesting token.
            while ($tokens[$next]['code'] === T_STRING || $tokens[$next]['code'] === T_NS_SEPARATOR) {
                $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($next + 1), null, true);
            }

            // Throw an error if the next character is a T_OPEN_CURLY_BRACKET.
            if ($tokens[$next]['code'] === T_OPEN_CURLY_BRACKET) {
                $error = 'No namespace blocks are allowed.';
                $phpcsFile->addError($error, $stackPtr, 'NoNamespaceBlocksAllowed');
            }
        }

        // CHECK 3: (Sub)namespace names must be in StudlyCaps.
        if ($tokens[$stackPtr]['code'] === T_NAMESPACE) {
            $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);

            // Loop until we don't find a T_STRING or T_NS_SEPARATOR anymore.
            while ($tokens[$next]['code'] === T_STRING || $tokens[$next]['code'] === T_NS_SEPARATOR) {
                // If this is a T_STRING, validate the name.
                if ($tokens[$next]['code'] === T_STRING) {
                    // Validate.
                    $name = $tokens[$next]['content'];
                    if (preg_match('/^[A-Z0-9][a-zA-Z0-9]*$/', $name) !== 1) {
                        $error = 'Namespace names must be in StudlyCaps. Found: '.$name;
                        $phpcsFile->addError($error, $stackPtr, 'InvalidNamespaceName');
                    }
                }

                $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($next + 1), null, true);
            }
        }//end if

        // CHECK 4: Leading T_NS_SEPARATOR.
        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);

        // The next token may not be an T_NS_SEPARATOR.
        if ($tokens[$next]['code'] === T_NS_SEPARATOR) {
            if ($tokens[$stackPtr]['code'] === T_NAMESPACE) {
                $error = 'Namespace definitions may not start with a leading backslash. ';
            } else {
                $error = 'Namespace usages may not start with a leading backslash. ';
            }

            $phpcsFile->addError($error, $stackPtr, 'NamespaceLeadingBackslash');
        }

    }//end process()


}//end class
