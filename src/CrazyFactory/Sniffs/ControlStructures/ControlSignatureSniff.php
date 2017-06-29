<?php

namespace CrazyFactory\Sniffs\ControlStructures;


use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class ControlSignatureSniff implements Sniff
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
     * @return int[]
     */
    public function register()
    {
        return [
            T_TRY,
            T_CATCH,
            T_DO,
            T_WHILE,
            T_FOR,
            T_IF,
            T_FOREACH,
            T_ELSE,
            T_ELSEIF,
            T_SWITCH,
        ];

    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[($stackPtr + 1)]) === false) {
            return;
        }

        // Single space after the keyword.
        $found = 1;
        if ($tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE) {
            $found = 0;
        }
        else if ($tokens[($stackPtr + 1)]['content'] !== ' ') {
            if (strpos($tokens[($stackPtr + 1)]['content'], $phpcsFile->eolChar) !== false) {
                $found = 'newline';
            }
            else {
                $found = strlen($tokens[($stackPtr + 1)]['content']);
            }
        }

        if ($found !== 1) {
            $error = 'Expected 1 space after %s keyword; %s found';
            $data = [
                strtoupper($tokens[$stackPtr]['content']),
                $found,
            ];

            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceAfterKeyword', $data);
            if ($fix === true) {
                if ($found === 0) {
                    $phpcsFile->fixer->addContent($stackPtr, ' ');
                }
                else {
                    $phpcsFile->fixer->replaceToken(($stackPtr + 1), ' ');
                }
            }
        }

        // Single space after closing parenthesis.
        if (isset($tokens[$stackPtr]['parenthesis_closer']) === true
            && isset($tokens[$stackPtr]['scope_opener']) === true
        ) {
            $closer = $tokens[$stackPtr]['parenthesis_closer'];
            $opener = $tokens[$stackPtr]['scope_opener'];
            $content = $phpcsFile->getTokensAsString(($closer + 1), ($opener - $closer - 1));

            if ($content !== ' ') {
                $error = 'Expected 1 space after closing parenthesis; found %s';
                if (trim($content) === '') {
                    $found = strlen($content);
                }
                else {
                    $found = '"' . str_replace($phpcsFile->eolChar, '\n', $content) . '"';
                }

                $fix = $phpcsFile->addFixableError($error, $closer, 'NewLineAfterCloseParenthesis', [$found]);
                if ($fix === true) {
                    if ($closer === ($opener - 1)) {
                        $phpcsFile->fixer->addContent($closer, ' ');
                    }
                    else {
                        $phpcsFile->fixer->beginChangeset();
                        if (trim($content) === '') {
                            $phpcsFile->fixer->addContent($closer, ' ');
                            if ($found !== 0) {
                                for ($i = ($closer + 1); $i < $opener; $i++) {
                                    $phpcsFile->fixer->replaceToken($i, '');
                                }
                            }
                        }
                        else {
                            $phpcsFile->fixer->addContent($closer, ' ' . $tokens[$opener]['content']);
                            $phpcsFile->fixer->replaceToken($opener, '');

                            if ($tokens[$opener]['line'] !== $tokens[$closer]['line']) {
                                $next = $phpcsFile->findNext(T_WHITESPACE, ($opener + 1), null, true);
                                if ($tokens[$next]['line'] !== $tokens[$opener]['line']) {
                                    for ($i = ($opener + 1); $i < $next; $i++) {
                                        $phpcsFile->fixer->replaceToken($i, '');
                                    }
                                }
                            }
                        }

                        $phpcsFile->fixer->endChangeset();
                    }//end if
                }//end if
            }//end if
        }//end if

        // Single newline after opening and closing brace.
        if (isset($tokens[$stackPtr]['scope_opener']) === true) {
            $this->requireNewLineAfterBrace(T_OPEN_CURLY_BRACKET, $tokens[$stackPtr], $tokens, $phpcsFile);
            $this->requireNewLineAfterBrace(T_CLOSE_CURLY_BRACKET, $tokens[$stackPtr], $tokens, $phpcsFile);
        }
        else if ($tokens[$stackPtr]['code'] === T_WHILE) {
            // Zero spaces after parenthesis closer.
            $closer = $tokens[$stackPtr]['parenthesis_closer'];
            $found = 0;
            if ($tokens[($closer + 1)]['code'] === T_WHITESPACE) {
                if (strpos($tokens[($closer + 1)]['content'], $phpcsFile->eolChar) !== false) {
                    $found = 'newline';
                }
                else {
                    $found = strlen($tokens[($closer + 1)]['content']);
                }
            }

            if ($found !== 0) {
                $error = 'Expected 0 spaces before semicolon; %s found';
                $data = [$found];
                $fix = $phpcsFile->addFixableError($error, $closer, 'SpaceBeforeSemicolon', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken(($closer + 1), '');
                }
            }
        }//end if

        // Only want to check multi-keyword structures from here on.
        if ($tokens[$stackPtr]['code'] === T_DO) {
            if (isset($tokens[$stackPtr]['scope_closer']) === false) {
                return;
            }
        }
        else if ($tokens[$stackPtr]['code'] === T_ELSE
            || $tokens[$stackPtr]['code'] === T_ELSEIF
            || $tokens[$stackPtr]['code'] === T_CATCH
        ) {
            if (isset($tokens[$stackPtr]['scope_opener']) === true
                && $tokens[$tokens[$stackPtr]['scope_opener']]['code'] === T_COLON
            ) {
                // Special case for alternate syntax, where this token is actually
                // the closer for the previous block, so there is no spacing to check.
                return;
            }

            $closer = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
            if ($closer === false || $tokens[$closer]['code'] !== T_CLOSE_CURLY_BRACKET) {
                return;
            }
        }
        else {
            return;
        }//end if

    }

    public function requireNewLineAfterBrace($type, $currentToken, $tokens, File $phpcsFile)
    {

        if ($type === T_OPEN_CURLY_BRACKET) {
            $brace = $currentToken['scope_opener'];
            $braceMsg = 'opening brace';
        }
        else if ($type === T_CLOSE_CURLY_BRACKET) {
            $brace = $currentToken['scope_closer'];
            $braceMsg = 'closing brace';
        }
        else {
            throw new \Exception('Invalid `type` value.');
        }

        for ($next = ($brace + 1); $next < $phpcsFile->numTokens; $next++) {
            $code = $tokens[$next]['code'];

            if ($code === T_WHITESPACE
                || ($code === T_INLINE_HTML
                    && trim($tokens[$next]['content']) === '')
            ) {
                continue;
            }

            // Skip all empty tokens on the same line as the opener.
            if ($tokens[$next]['line'] === $tokens[$brace]['line']
                && (isset(Tokens::$emptyTokens[$code]) === true
                    || $code === T_CLOSE_TAG)
            ) {
                continue;
            }

            // We found the first bit of a code, or a comment on the
            // following line.
            break;
        }//end for

        // Prevent undefined offset error
        // This occur when character after closing brace is white space
        if($next >= $phpcsFile->numTokens) {
            return;
        }

        if ($tokens[$next]['line'] === $tokens[$brace]['line']) {
            $error = 'Newline required after ' . $braceMsg;
            $fix = $phpcsFile->addFixableError($error, $brace, 'NewlineAfterOpenBrace');
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = ($brace + 1); $i < $next; $i++) {
                    if (trim($tokens[$i]['content']) !== '') {
                        break;
                    }

                    // Remove whitespace.
                    $phpcsFile->fixer->replaceToken($i, '');
                }

                $phpcsFile->fixer->addContent($brace, $phpcsFile->eolChar);
                $phpcsFile->fixer->endChangeset();
            }
        }//end if
    }
}