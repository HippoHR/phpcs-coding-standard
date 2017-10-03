<?php
/**
 * Hippo_Sniffs_Hippo_KeywordSniff.
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

/**
 * Hippo_Sniffs_Hippo_KeywordSniff.
 *
 * Checks wether keywords are used correctly.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Dennis Broeks <dennis@uitzendbureau.nl>
 */

class KeywordSniff implements Sniff
{


        /**
         * Returns an array of tokens this test wants to listen for.
         *
         * @return array
         */
    public function register()
    {
            // Source: http://php.net/manual/en/reserved.keywords.php.
            return array(
                    T_HALT_COMPILER,
                    T_ABSTRACT,
                    T_LOGICAL_AND,
                    T_ARRAY,
                    T_AS,
                    T_BREAK,
                    // T_CALLABLE,    Not recognised.
                    T_CASE,
                    T_CATCH,
                    T_CLASS,
                    T_CLONE,
                    T_CONST,
                    T_CONTINUE,
                    T_DECLARE,
                    T_DEFAULT,
                    T_EXIT,
                    T_DO,
                    T_ECHO,
                    T_ELSE,
                    T_ELSEIF,
                    T_EMPTY,
                    T_ENDDECLARE,
                    T_ENDFOR,
                    T_ENDFOREACH,
                    T_ENDIF,
                    T_ENDSWITCH,
                    T_ENDWHILE,
                    T_EVAL,
                    T_EXIT,
                    T_EXTENDS,
                    T_FINAL,
                    T_FOR,
                    T_FOREACH,
                    T_FUNCTION,
                    T_GLOBAL,
                    T_GOTO,
                    T_IF,
                    T_IMPLEMENTS,
                    T_INCLUDE,
                    T_INCLUDE_ONCE,
                    T_INSTANCEOF,
                    // T_INSTEADOF,  Not recognised.
                    T_INTERFACE,
                    T_ISSET,
                    T_LIST,
                    T_NAMESPACE,
                    T_NEW,
                    T_LOGICAL_OR,
                    T_PRINT,
                    T_PRIVATE,
                    T_PROTECTED,
                    T_PUBLIC,
                    T_REQUIRE,
                    T_REQUIRE_ONCE,
                    T_RETURN,
                    T_STATIC,
                    T_SWITCH,
                    T_THROW,
                    T_TRAIT,
                    T_TRY,
                    T_UNSET,
                    T_USE,
                    T_VAR,
                    T_WHILE,
                    T_LOGICAL_XOR,
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
            $tokens = $phpcsFile->getTokens();

            // Check if lowercase.
        if ($tokens[$stackPtr]['content'] !== strtolower($tokens[$stackPtr]['content'])) {
                $error = 'PHP keywords must be lower case. Found: '.$tokens[$stackPtr]['content'];
                $phpcsFile->addError($error, $stackPtr, 'KeywordNotLowerCase');
        }

    }//end process()


}//end class
