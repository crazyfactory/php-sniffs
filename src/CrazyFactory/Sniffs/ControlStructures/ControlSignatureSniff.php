<?php
namespace CrazyFactory\Sniffs\ControlStructures;

class ControlSignatureSniff extends \PHP_CodeSniffer\Standards\PEAR\Sniffs\ControlStructures\ControlSignatureSniff
{
    protected function getPatterns()
    {
        return [
            'try {EOL...} catch (...) {EOL',
            'do {EOL...} while (...);EOL',
            'while (...) {EOL',
            'for (...) {EOL',
            'if (...) {',
            'foreach (...) {EOL',
            '}EOLelse if (...) {EOL',
            '}EOLelseif (...) {EOL',
            '}EOLelse {',
        ];

    }//end getPatterns()
}