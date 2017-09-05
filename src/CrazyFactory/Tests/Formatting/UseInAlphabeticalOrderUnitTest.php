<?php
namespace CrazyFactory\Tests\Formatting;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class UseInAlphabeticalOrderUnitTest extends AbstractSniffUnitTest
{

    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @return array<int, int>
     */
    protected function getErrorList($testFile = '')
    {
        switch ($testFile) {
            case 'UseInAlphabeticalOrderUnitTest.1.inc':
                return [
                    2 => 6
                ];
                break;
            case 'UseInAlphabeticalOrderUnitTest.2.inc':
                return [
                    3 => 1
                ];
                break;
            default:
                return [];
        }
    }

    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @return array(int => int)
     */
    protected function getWarningList()
    {
        return [];
    }
}
