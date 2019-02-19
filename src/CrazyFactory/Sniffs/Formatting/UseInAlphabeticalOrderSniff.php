<?php
namespace CrazyFactory\Sniffs\Formatting;

/**
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @since         CakePHP CodeSniffer 0.1.10
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Ensures all the use are in alphabetical order.
 *
 */
class UseInAlphabeticalOrderSniff implements Sniff
{

    /**
     * Processed files
     *
     * @var array
     */
    protected $processed = [];
    /**
     * The list of use statements, their content and scope.
     *
     * @var array
     */
    protected $uses = [];
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_USE];
    }
    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param File $phpcsFile The file being scanned.
     * @param integer $stackPtr The position of the current token in the stack passed in $tokens.
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if (isset($this->processed[$phpcsFile->getFilename()])) {
            return;
        }

        $this->uses = [];
        $next = $stackPtr;
        $tokens = $phpcsFile->getTokens();

        while ($next !== false) {
            $this->checkUseToken($phpcsFile, $next);
            $next = $phpcsFile->findNext(T_USE, $next + 1);
        }

        // Prevent multiple uses in the same file from entering
        $this->processed[$phpcsFile->getFilename()] = true;
        foreach ($this->uses as $scope => $used) {
            $defined = $sorted = array_keys($used);
            natcasesort($sorted);
            $sorted = array_values($sorted);

            if ($sorted === $defined) {
                continue;
            }

            foreach ($defined as $i => $name) {
                if ($name !== $sorted[$i]) {
                    $error = 'Use classes must be in alphabetical order.';
                    $fix = $phpcsFile->addFixableError($error, $used[$name], 'UseInAlphabeticalOrder', []);
                }
            }

            if (isset($fix) && $fix === true) {
                $phpcsFile->fixer->beginChangeset();

                // Remove all old use statements
                $semiColon = $stackPtr;
                foreach ($defined as $i => $name) {
                    $semiColon = $phpcsFile->findEndOfStatement($semiColon);
                    for ($j = $stackPtr; $j <= $semiColon; $j++) {
                        // Remain "Use" keyword because we will use it to insert new use statement
                        if ($tokens[$j]['code'] !== T_USE && $tokens[$j]['content'] !== "\n") {
                            $phpcsFile->fixer->replaceToken($j, '');
                        }
                    }
                }

                // Insert new use statements
                $usePos = $phpcsFile->findNext(T_USE, $stackPtr);
                foreach ($sorted as $name) {
                    $phpcsFile->fixer->addContent($usePos, " {$name};");
                    $usePos = $phpcsFile->findNext(T_USE, $usePos + 1);
                }

                $phpcsFile->fixer->endChangeset();
            }
        }
    }

    /**
     * Check all the use tokens in a file.
     *
     * @param File $phpcsFile The file to check.
     * @param integer $stackPtr  The index of the first use token.
     *
     * @return void
     */
    private function checkUseToken(File $phpcsFile, $stackPtr)
    {
        // If the use token is for a closure we want to ignore it.
        $isClosure = $this->isClosure($phpcsFile, $stackPtr);

        if ($isClosure) {
            return;
        }

        $tokens = $phpcsFile->getTokens();
        // Only one USE declaration allowed per statement.
        $next = $phpcsFile->findNext([T_COMMA, T_SEMICOLON], ($stackPtr + 1));
        if ($tokens[$next]['code'] === T_COMMA) {
            $error = 'There must be one USE keyword per declaration';
            $phpcsFile->addError($error, $stackPtr, 'MultipleDeclarations');
        }
        $content = '';
        $end = $phpcsFile->findNext([T_SEMICOLON, T_OPEN_CURLY_BRACKET], $stackPtr);
        $useTokens = array_slice($tokens, $stackPtr, $end - $stackPtr, true);
        foreach ($useTokens as $index => $token) {
            if (in_array($token['code'], [T_STRING, T_NS_SEPARATOR, T_WHITESPACE, T_AS])) {
                $content .= $token['content'];
            }
        }
        // Check for class scoping on use. Traits should be
        // ordered independently.
        $scope = 0;
        if (!empty($token['conditions'])) {
            $scope = key($token['conditions']);
        }

        $this->uses[$scope][$content] = 1;
    }

    /**
     * Check if the current stackPtr is a use token that is for a closure.
     *
     * @param File $phpcsFile
     * @param integer $stackPtr
     *
     * @return bool
     */
    private function isClosure(File $phpcsFile, $stackPtr)
    {
        return $phpcsFile->findPrevious(
            [T_CLOSURE],
            ($stackPtr - 1),
            null,
            false,
            null,
            true
        );
    }
}
