<?php
/**
 * Hippo_Sniffs_Functions_FunctionDeclarationSniff.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Dennis Broeks <dennis@uitzendbureau.nl>
 * @license  https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link     https://github.com/HippoHR/phpcs-coding-standard
 */
namespace Hippo\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Functions\OpeningFunctionBraceBsdAllmanSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Functions\OpeningFunctionBraceKernighanRitchieSniff;

/**
 * Hippo_Sniffs_Functions_FunctionDeclarationSniff.
 *
 * Ensure single and multi-line function declarations are defined correctly.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Dennis Broeks <dennis@uitzendbureau.nl>
 * @license  https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link     https://github.com/HippoHR/phpcs-coding-standard
 */

class FunctionDeclarationSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_FUNCTION,
            T_CLOSURE,
        ];

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
        // Variant of PEAR/Sniffs/Functions/FunctionDeclarationSniff.php.
        //
        // Changes:
        // - tabs are 2 spaces instead of 4,
        // - opening brace should be on seperate line.
        $tokens = $phpcsFile->getTokens();

        // Opening brace should be on seperate line.
        $errorData = [$tokens[$stackPtr]['content']];
        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            $methodProperties = $phpcsFile->getMethodProperties($stackPtr);
            // If the method is abstract, we don't need a body.
            if ($methodProperties['is_abstract'] === true) {
                return;
            }

            // If the method is in a interface, we don't need a body.
            if ($phpcsFile->hasCondition($stackPtr, T_INTERFACE) === true) {
                return;
            }

            // The method should probably have a body.
            $error = 'Possible parse error: %s missing opening or closing brace';
            $phpcsFile->addWarning($error, $stackPtr, 'MissingBrace', $errorData);
            return;
        }

        $curlyBrace  = $tokens[$stackPtr]['scope_opener'];
        $lastContent = $phpcsFile->findPrevious(T_WHITESPACE, ($curlyBrace - 1), $stackPtr, true);
        $classLine   = $tokens[$lastContent]['line'];
        $braceLine   = $tokens[$curlyBrace]['line'];
        if ($braceLine === $classLine) {
            $error = 'Opening brace of a %s must be on the line after the definition';
            $phpcsFile->addError($error, $curlyBrace, 'OpenBraceNewLine', $errorData);
            return;
        } else if ($braceLine > ($classLine + 1)) {
            $error = 'Opening brace of a %s must be on the line following the %s declaration; found %s line(s)';
            $data  = [
                $tokens[$stackPtr]['content'],
                $tokens[$stackPtr]['content'],
                ($braceLine - $classLine - 1),
            ];
            $phpcsFile->addError($error, $curlyBrace, 'OpenBraceWrongLine', $data);
            return;
        }

        $spaces = 0;
        if ($tokens[($stackPtr + 1)]['code'] === T_WHITESPACE) {
            $spaces = strlen($tokens[($stackPtr + 1)]['content']);
        }

        $expectedSpaces = [
            T_CLOSURE  => 0,
            T_FUNCTION => 1,
        ];

        $expectedNrOfSpaces = $expectedSpaces[$tokens[$stackPtr]['code']];
        if ($spaces !== $expectedNrOfSpaces) {
            $error = 'Expected %s spaces after FUNCTION keyword; %s found';
            $data  = [
                $expectedNrOfSpaces,
                $spaces,
            ];
            $phpcsFile->addError($error, $stackPtr, 'SpaceAfterFunction', $data);
        }

        // Must be one space before and after USE keyword for closures.
        $openBracket  = $tokens[$stackPtr]['parenthesis_opener'];
        $closeBracket = $tokens[$stackPtr]['parenthesis_closer'];
        if ($tokens[$stackPtr]['code'] === T_CLOSURE) {
            $use = $phpcsFile->findNext(T_USE, ($closeBracket + 1), $tokens[$stackPtr]['scope_opener']);
            if ($use !== false) {
                if ($tokens[($use + 1)]['code'] !== T_WHITESPACE) {
                    $length = 0;
                } else if ($tokens[($use + 1)]['content'] === "\t") {
                    $length = '\t';
                } else {
                    $length = strlen($tokens[($use + 1)]['content']);
                }

                if ($length !== 1) {
                    $error = 'Expected 1 space after USE keyword; found %s';
                    $data  = [$length];
                    $phpcsFile->addError($error, $use, 'SpaceAfterUse', $data);
                }

                if ($tokens[($use - 1)]['code'] !== T_WHITESPACE) {
                    $length = 0;
                } else if ($tokens[($use - 1)]['content'] === "\t") {
                    $length = '\t';
                } else {
                    $length = strlen($tokens[($use - 1)]['content']);
                }

                if ($length !== 1) {
                    $error = 'Expected 1 space before USE keyword; found %s';
                    $data  = [$length];
                    $phpcsFile->addError($error, $use, 'SpaceBeforeUse', $data);
                }
            }//end if
        }//end if

        // Check if this is a single line or multi-line declaration.
        $singleLine = true;
        if ($tokens[$openBracket]['line'] === $tokens[$closeBracket]['line']) {
            // Closures may use the USE keyword and so be multi-line in this way.
            if ($tokens[$stackPtr]['code'] === T_CLOSURE) {
                if ($use !== false) {
                    // If the opening and closing parenthesis of the use statement
                    // are also on the same line, this is a single line declaration.
                    $open  = $phpcsFile->findNext(T_OPEN_PARENTHESIS, ($use + 1));
                    $close = $tokens[$open]['parenthesis_closer'];
                    if ($tokens[$open]['line'] !== $tokens[$close]['line']) {
                        $singleLine = false;
                    }
                }
            }
        } else {
            $singleLine = false;
        }

        if ($singleLine === true) {
            $this->processSingleLineDeclaration($phpcsFile, $stackPtr, $tokens);
        } else {
            $this->processMultiLineDeclaration($phpcsFile, $stackPtr, $tokens);
        }

    }//end process()


    /**
     * Processes single-line declarations.
     *
     * Just uses the Generic BSD-Allman brace sniff.
     *
     * @param File  $phpcsFile The file being scanned.
     * @param int   $stackPtr  The position of the current token
     *                         in the stack passed in $tokens.
     * @param array $tokens    The stack of tokens that make up
     *                         the file.
     *
     * @return void
     */
    public function processSingleLineDeclaration(File $phpcsFile, $stackPtr, $tokens)
    {
        if ($tokens[$stackPtr]['code'] === T_CLOSURE) {
            $sniff = new OpeningFunctionBraceKernighanRitchieSniff();
        } else {
            $sniff = new OpeningFunctionBraceBsdAllmanSniff();
        }

        $sniff->process($phpcsFile, $stackPtr);

    }//end processSingleLineDeclaration()


    /**
     * Processes mutli-line declarations.
     *
     * @param File  $phpcsFile The file being scanned.
     * @param int   $stackPtr  The position of the current token
     *                         in the stack passed in $tokens.
     * @param array $tokens    The stack of tokens that make up
     *                         the file.
     *
     * @return void
     */
    public function processMultiLineDeclaration(File $phpcsFile, $stackPtr, $tokens)
    {
        // We need to work out how far indented the function
        // declaration itself is, so we can work out how far to
        // indent parameters.
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

        // The closing parenthesis must be on a new line, even
        // when checking abstract function definitions.
        $closeBracket = $tokens[$stackPtr]['parenthesis_closer'];
        $prev         = $phpcsFile->findPrevious(
            T_WHITESPACE,
            ($closeBracket - 1),
            null,
            true
        );

        if ($tokens[$closeBracket]['line'] !== $tokens[$tokens[$closeBracket]['parenthesis_opener']]['line']) {
            if ($tokens[$prev]['line'] === $tokens[$closeBracket]['line']) {
                $error = 'The closing parenthesis of a multi-line function declaration must be on a new line';
                $phpcsFile->addError($error, $closeBracket, 'CloseBracketLine');
            }
        }

        // If this is a closure and is using a USE statement, the closing
        // parenthesis we need to look at from now on is the closing parenthesis
        // of the USE statement.
        if ($tokens[$stackPtr]['code'] === T_CLOSURE) {
            $use = $phpcsFile->findNext(T_USE, ($closeBracket + 1), $tokens[$stackPtr]['scope_opener']);
            if ($use !== false) {
                $open         = $phpcsFile->findNext(T_OPEN_PARENTHESIS, ($use + 1));
                $closeBracket = $tokens[$open]['parenthesis_closer'];

                $prev = $phpcsFile->findPrevious(
                    T_WHITESPACE,
                    ($closeBracket - 1),
                    null,
                    true
                );

                if ($tokens[$closeBracket]['line'] !== $tokens[$tokens[$closeBracket]['parenthesis_opener']]['line']) {
                    if ($tokens[$prev]['line'] === $tokens[$closeBracket]['line']) {
                        $error = 'The closing parenthesis of a multi-line use declaration must be on a new line';
                        $phpcsFile->addError($error, $closeBracket, 'CloseBracketLine');
                    }
                }
            }//end if
        }//end if

        // Each line between the parenthesis should be indented 2 spaces.
        $openBracket = $tokens[$stackPtr]['parenthesis_opener'];
        $lastLine    = $tokens[$openBracket]['line'];
        for ($i = ($openBracket + 1); $i < $closeBracket; $i++) {
            if ($tokens[$i]['line'] !== $lastLine) {
                if ($i === $tokens[$stackPtr]['parenthesis_closer']
                    || ($tokens[$i]['code'] === T_WHITESPACE
                    && ($i + 1) === $tokens[$stackPtr]['parenthesis_closer'])
                ) {
                    // Closing braces need to be indented to the same level
                    // as the function.
                    $expectedIndent = $functionIndent;
                } else {
                    $expectedIndent = ($functionIndent + 2);
                }

                // We changed lines, so this should be a whitespace indent token.
                if ($tokens[$i]['code'] !== T_WHITESPACE) {
                    $foundIndent = 0;
                } else {
                    $foundIndent = strlen($tokens[$i]['content']);
                }

                if ($expectedIndent !== $foundIndent) {
                    $error = 'Multi-line function declaration not indented correctly; expected %s spaces but found %s';
                    $data  = [
                        $expectedIndent,
                        $foundIndent,
                    ];
                    $phpcsFile->addError($error, $i, 'Indent', $data);
                }

                $lastLine = $tokens[$i]['line'];
            }//end if

            if ($tokens[$i]['code'] === T_ARRAY) {
                // Skip arrays as they have their own indentation rules.
                $i        = $tokens[$i]['parenthesis_closer'];
                $lastLine = $tokens[$i]['line'];
                continue;
            }
        }//end for

    }//end processMultiLineDeclaration()


}//end class
