<?php
/**
 * Hippo_Sniffs_CSS_IndentationSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Dennis Claassen <claassen@uitzendbureau.nl>
 */

/**
 * Hippo_Sniffs_CSS_IndentationSniff.
 *
 * Ensures styles are indented 2 spaces.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Dennis Claassen <claassen@uitzendbureau.nl>
 */
class Hippo_Sniffs_CSS_IndentationSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array('CSS');


    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return array(int)
     */
    public function register()
    {
        return array(T_OPEN_TAG);

    }//end register()


    /**
     * Processes the tokens that this sniff is interested in.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
     * @param int                  $stackPtr  The position in the stack where
     *                                        the token was found.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        #######################################################
        # Variant of Squiz_Sniffs_CSS_IndentationSniff
        #
        # Changes:
        # - Changed the expectedIndent to grow with 2 instead of 4
        #######################################################

        $tokens = $phpcsFile->getTokens();

        $numTokens    = (count($tokens) - 2);
        $currentLine  = 0;
        $indentLevel  = 0;
        $nestingLevel = 0;
        for ($i = 1; $i < $numTokens; $i++) {
            if ($tokens[$i]['code'] === T_COMMENT) {
                // Dont check the indent of comments.
                continue;
            }

            if ($tokens[$i]['code'] === T_OPEN_CURLY_BRACKET) {
                $indentLevel++;

                // Check for nested class definitions.
                $found  = $phpcsFile->findNext(
                    T_OPEN_CURLY_BRACKET,
                    ($i + 1),
                    $tokens[$i]['bracket_closer']
                );
                if ($found !== false) {
                    $nestingLevel = $indentLevel;
                }
                // Do not check the indentation of a line with a T_OPEN_CURLY_BRACKET
                continue;
            } else if ($tokens[($i + 1)]['code'] === T_CLOSE_CURLY_BRACKET) {
                $indentLevel--;
            }

            if ($tokens[$i]['line'] === $currentLine) {
                continue;
            }

            // We started a new line, so check indent.
            if ($tokens[$i]['code'] === T_WHITESPACE) {
                $content     = str_replace($phpcsFile->eolChar, '', $tokens[$i]['content']);
                $foundIndent = strlen($content);
            } else {
                $foundIndent = 0;
            }

            $expectedIndent = ($indentLevel * 2);
            if ($expectedIndent > 0 && strpos($tokens[$i]['content'], $phpcsFile->eolChar) !== false
            ) {
/*
// We do allow Blank Lines
                if ($nestingLevel !== $indentLevel) {
                    $error = 'Blank lines are not allowed in class definitions';
                    $phpcsFile->addError($error, $i, 'BlankLine');
                }
*/
            } else if ($foundIndent !== $expectedIndent) {
                $error = 'Line indented incorrectly; expected %s spaces, found %s';
                $data  = array(
                          $expectedIndent,
                          $foundIndent,
                         );
                $phpcsFile->addError($error, $i, 'Incorrect', $data);
            }

            $currentLine = $tokens[$i]['line'];
        }//end for

    }//end process()

}//end class
