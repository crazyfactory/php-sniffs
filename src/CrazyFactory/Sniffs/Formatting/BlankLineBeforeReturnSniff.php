<?php
namespace CrazyFactory\Sniffs\Formatting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Class BlankLineBeforeReturnSniff
 *
 * @package CrazyFactory\Sniffs\Formatting
 *
 * @link https://github.com/widop/CodeSniffer [Original version]
 */
class BlankLineBeforeReturnSniff implements Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = [
        'PHP',
        'JS',
    ];

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_RETURN];
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param File $phpcsFile All the tokens found in the document.
     * @param int  $stackPtr  The position of the current token in
     *                        the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens          = $phpcsFile->getTokens();
        $current         = $stackPtr;
        $previousLine    = $tokens[$stackPtr]['line'] - 1;
        $prevLineTokens  = [];

        while ($tokens[$current]['line'] >= $previousLine) {
            if ($tokens[$current]['line'] == $previousLine
                && $tokens[$current]['type'] != 'T_WHITESPACE'
                && $tokens[$current]['type'] != 'T_COMMENT'
            ) {
                $prevLineTokens[] = $tokens[$current]['type'];
            }

            if ($current === 0) {
                break;
            }

            $current--;
        }

        if (isset($prevLineTokens[0])
            && ($prevLineTokens[0] == 'T_OPEN_CURLY_BRACKET' || $prevLineTokens[0] == 'T_DOC_COMMENT_CLOSE_TAG')
            || in_array('T_CASE', $prevLineTokens)
            || in_array('T_DEFAULT', $prevLineTokens)
        ) {
            return;
        }
        elseif (count($prevLineTokens) > 0) {
            $fix = $phpcsFile->addFixableError(
                'Missing blank line before return statement',
                $stackPtr,
                'BlankLineBeforeReturn'
            );

            $returnPosition = $this->getReturnPosition($current, $tokens);

            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->addNewlineBefore($returnPosition);
                $phpcsFile->fixer->endChangeset();
            }
        }

        return;
    }

    /**
     * @param $current
     * @param $tokens
     *
     * @return mixed
     */
    private function getReturnPosition($current, $tokens)
    {
        while ($current <= count($tokens) && $tokens[$current]['type'] !== 'T_RETURN') {
            $current++;
            continue;
        }

        return $current;
    }
}
