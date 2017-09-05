<?php
namespace CrazyFactory\Tests\ControlStructures;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class ControlSignatureUnitTest extends AbstractSniffUnitTest
{


    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @param string $testFile The name of the file being tested.
     *
     * @return array<int, int>
     */
    public function getErrorList($testFile = '')
    {
        return [
            5   => 1,
            7   => 1,
            10  => 1,
            12  => 1,
            18  => 2,
            20  => 1,
            22  => 2,
            28  => 2,
            32  => 1,
            38  => 2,
            42  => 1,
            48  => 2,
            52  => 1,
            56  => 1,
            62  => 2,
            66  => 3,
            70  => 1,
            76  => 4,
            80  => 3,
            94  => 1,
            99  => 1,
            104 => 1,
            108 => 2,
            112 => 1,
            113 => 1,
            130 => 2,
            134 => 1,
            158 => 1,
            165 => 1,
            170 => 2,
            185 => 1,
            190 => 2,
            191 => 2,
            195 => 1,
            208 => 1
        ];
    }//end getErrorList()


    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @return array<int, int>
     */
    public function getWarningList()
    {
        return [];
    }//end getWarningList()
}//end class
