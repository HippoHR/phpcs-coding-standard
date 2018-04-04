<?php
/**
 * Hippo_Sniffs_Functions_FunctionCallSignatureSniff.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Dennis Broeks <dennis@uitzendbureau.nl>
 * @license  https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
namespace Hippo\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Hippo_Sniffs_Functions_FunctionCallSignatureSniff.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Dennis Broeks <dennis@uitzendbureau.nl>
 */

class FunctionCallSignatureSniff implements Sniff
{

    /**
     * The number of spaces code should be indented.
     *
     * @var integer
     */
    public $indent = 4;

    /**
     * If TRUE, multiple arguments can be defined per line in a multi-line call.
     *
     * @var boolean
     */
    public $allowMultipleArguments = true;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_STRING];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $stackPtr  The position of the current token
     *                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        //
        // Variant of PEAR/Sniffs/Functions/FunctionCallSignatureSniff.php
        //
        // Changes:
        // - Fixed spaces around parenthesis.
        //
        $tokens = $phpcsFile->getTokens();

        // Find the next non-empty token.
        $openBracket = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);

        if ($tokens[$openBracket]['code'] !== T_OPEN_PARENTHESIS) {
            // Not a function call.
            return;
        }

        if (isset($tokens[$openBracket]['parenthesis_closer']) === false) {
            // Not a function call.
            return;
        }

        // Find the previous non-empty token.
        $search   = Tokens::$emptyTokens;
        $search[] = T_BITWISE_AND;
        $previous = $phpcsFile->findPrevious($search, ($stackPtr - 1), null, true);
        if ($tokens[$previous]['code'] === T_FUNCTION) {
            // It's a function definition, not a function call.
            return;
        }

        $closeBracket = $tokens[$openBracket]['parenthesis_closer'];

        if (($stackPtr + 1) !== $openBracket) {
            // Checking this: $value = my_function[*](...).
            $error = 'Space before opening parenthesis of function call prohibited';
            $phpcsFile->addError($error, $stackPtr, 'SpaceBeforeOpenBracket');
        }

        $next = $phpcsFile->findNext(T_WHITESPACE, ($closeBracket + 1), null, true);
        if ($tokens[$next]['code'] === T_SEMICOLON) {
            if (in_array($tokens[($closeBracket + 1)]['code'], Tokens::$emptyTokens) === true) {
                $error = 'Space after closing parenthesis of function call prohibited';
                $phpcsFile->addError($error, $closeBracket, 'SpaceAfterCloseBracket');
            }
        }

        // Check if this is a single line or multi-line function call.
        if ($tokens[$openBracket]['line'] === $tokens[$closeBracket]['line']) {
            $this->processSingleLineCall($phpcsFile, $stackPtr, $openBracket, $tokens);
        } else {
            $this->processMultiLineCall($phpcsFile, $stackPtr, $openBracket, $tokens);
        }

    }//end process()


    /**
     * Processes single-line calls.
     *
     * @param File  $phpcsFile   The file being scanned.
     * @param int   $stackPtr    The position of the current token
     *                           in the stack passed in $tokens.
     * @param int   $openBracket The position of the openning bracket
     *                           in the stack passed in $tokens.
     * @param array $tokens      The stack of tokens that make up
     *                           the file.
     *
     * @return void
     */
    public function processSingleLineCall(File $phpcsFile, $stackPtr, $openBracket, $tokens)
    {
        // In case of no arguments, no spaces are allowed.
        $between           = $phpcsFile->findNext(T_WHITESPACE, ($openBracket + 1), null, true);
        $betweenInclSpaces = $phpcsFile->findNext(null, ($openBracket + 1), null, true);
        $closer            = $tokens[$openBracket]['parenthesis_closer'];

        if ($tokens[$betweenInclSpaces]['code'] === T_WHITESPACE
            && $tokens[$between]['code'] === T_CLOSE_PARENTHESIS
        ) {
            // No arguments.
            $error = 'No space allowed in function call without arguments';
            $phpcsFile->addError($error, $stackPtr, 'SpaceAfterOpenBracket');
        } else if ($between !== $closer) {
            if ($tokens[($openBracket + 1)]['code'] !== T_WHITESPACE) {
                // Checking this: $value = my_function( [*]... ).
                $error = 'Space required after opening parenthesis of function call';
                $phpcsFile->addError($error, $stackPtr, 'SpaceAfterOpenBracket');
            } else if ($tokens[($openBracket + 1)]['code'] === T_WHITESPACE
                && strlen($tokens[($openBracket + 1)]['content']) !== 1
            ) {
                $error = 'Only one space allowed after opening parenthesis of function call, found multiple';
                $phpcsFile->addError($error, $stackPtr, 'SpaceAfterOpenBracket');
            }

            if ($tokens[($closer - 1)]['code'] !== T_WHITESPACE) {
                // Checking this: $value = my_function( ...[*] ).
                $error = 'Space before closing parenthesis of function call required';
                $phpcsFile->addError($error, $closer, 'SpaceBeforeCloseBracket');
            } else if ($tokens[($closer - 1)]['code'] === T_WHITESPACE
                && strlen($tokens[($closer - 1)]['content']) !== 1
            ) {
                $error = 'Only one space allowed before closing parenthesis of function call, found multiple';
                $phpcsFile->addError($error, $stackPtr, 'SpaceBeforeCloseBracket');
            }
        }//end if

    }//end processSingleLineCall()


    /**
     * Processes multi-line calls.
     *
     * @param File  $phpcsFile   The file being scanned.
     * @param int   $stackPtr    The position of the current token
     *                           in the stack passed in $tokens.
     * @param int   $openBracket The position of the openning bracket
     *                           in the stack passed in $tokens.
     * @param array $tokens      The stack of tokens that make up
     *                           the file.
     *
     * @return void
     */
    public function processMultiLineCall(File $phpcsFile, $stackPtr, $openBracket, $tokens)
    {
        // We need to work out how far indented the function
        // call itself is, so we can work out how far to
        // indent the arguments.
        $functionIndent = 0;
        for ($i = ($stackPtr - 1); $i >= 0; $i--) {
            if ($tokens[$i]['line'] !== $tokens[$stackPtr]['line']) {
                $i++;
                break;
            }
        }

        if ($tokens[$i]['code'] === T_WHITESPACE) {
            $functionIndent = strlen($tokens[$i]['content']);
        }

        // Each line between the parenthesis should be indented n spaces.
        $closeBracket = $tokens[$openBracket]['parenthesis_closer'];
        $lastLine     = $tokens[$openBracket]['line'];
        for ($i = ($openBracket + 1); $i < $closeBracket; $i++) {
            // Skip nested function calls.
            if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
                $i        = $tokens[$i]['parenthesis_closer'];
                $lastLine = $tokens[$i]['line'];
                continue;
            }

            if ($tokens[$i]['line'] !== $lastLine) {
                $lastLine = $tokens[$i]['line'];

                // Ignore heredoc indentation.
                if (in_array($tokens[$i]['code'], Tokens::$heredocTokens) === true) {
                    continue;
                }

                // Ignore multi-line string indentation.
                if (in_array($tokens[$i]['code'], Tokens::$stringTokens) === true) {
                    if ($tokens[$i]['code'] === $tokens[($i - 1)]['code']) {
                        continue;
                    }
                }

                // We changed lines, so this should be a whitespace indent token, but first make
                // sure it isn't a blank line because we don't need to check indent unless there
                // is actually some code to indent.
                if ($tokens[$i]['code'] === T_WHITESPACE) {
                    $nextCode = $phpcsFile->findNext(T_WHITESPACE, ($i + 1), ($closeBracket + 1), true);
                    if ($tokens[$nextCode]['line'] !== $lastLine) {
                        $error = 'Empty lines are not allowed in multi-line function calls';
                        $phpcsFile->addError($error, $i, 'EmptyLine');
                        continue;
                    }
                } else {
                    $nextCode = $i;
                }

                // Check if the next line contains an object operator, if so rely on
                // the ObjectOperatorIndentSniff to test the indent.
                if ($tokens[$nextCode]['type'] === 'T_OBJECT_OPERATOR') {
                    continue;
                }

                if ($nextCode === $closeBracket) {
                    // Closing brace needs to be indented to the same level
                    // as the function call.
                    $expectedIndent = $functionIndent;
                } else {
                    $expectedIndent = ($functionIndent + $this->indent);
                }

                if ($tokens[$i]['code'] !== T_WHITESPACE) {
                    // Just check if it is a multi-line block comment. If so, we can
                    // calculate the indent from the whitespace before the content.
                    if ($tokens[$i]['code'] === T_COMMENT
                        && $tokens[($i - 1)]['code'] === T_COMMENT
                    ) {
                        $trimmed     = ltrim($tokens[$i]['content']);
                        $foundIndent = (strlen($tokens[$i]['content']) - strlen($trimmed));
                    } else {
                        $foundIndent = 0;
                    }
                } else {
                    $foundIndent = strlen($tokens[$i]['content']);
                }

                if ($expectedIndent !== $foundIndent) {
                    $error = 'Multi-line function call not indented correctly; expected %s spaces but found %s';
                    $data  = [
                        $expectedIndent,
                        $foundIndent,
                    ];
                    $phpcsFile->addError($error, $i, 'Indent', $data);
                }
            }//end if

            // Skip the rest of a closure.
            if ($tokens[$i]['code'] === T_CLOSURE) {
                $i        = $tokens[$i]['scope_closer'];
                $lastLine = $tokens[$i]['line'];
                continue;
            }

            // Skip the rest of a short array.
            if ($tokens[$i]['code'] === T_OPEN_SHORT_ARRAY) {
                $i        = $tokens[$i]['bracket_closer'];
                $lastLine = $tokens[$i]['line'];
                continue;
            }

            if ($this->allowMultipleArguments === false && $tokens[$i]['code'] === T_COMMA) {
                // Comma has to be the last token on the line.
                $next = $phpcsFile->findNext([T_WHITESPACE, T_COMMENT], ($i + 1), $closeBracket, true);
                if ($next !== false
                    && $tokens[$i]['line'] === $tokens[$next]['line']
                ) {
                    $error = 'Only one argument is allowed per line in a multi-line function call';
                    $phpcsFile->addError($error, $next, 'MultipleArguments');
                }
            }
        }//end for

        if ($tokens[($openBracket + 1)]['content'] !== $phpcsFile->eolChar) {
            $error = 'Opening parenthesis of a multi-line function call must be the last content on the line';
            $phpcsFile->addError($error, $stackPtr, 'ContentAfterOpenBracket');
        }

        $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($closeBracket - 1), null, true);
        if ($tokens[$prev]['line'] === $tokens[$closeBracket]['line']) {
            $error = 'Closing parenthesis of a multi-line function call must be on a line by itself';
            $phpcsFile->addError($error, $closeBracket, 'CloseBracketLine');
        }

    }//end processMultiLineCall()


}//end class
