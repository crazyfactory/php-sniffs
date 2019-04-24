<?php
function foo()
{
    return 1;
}

function bar()
{
    $x = 2;
    return $x;
}

function hello()
{
    $x = 2;

    return $x;
}

function hi()
{
    $x = 2;
    // Some comment
    return $x;
}
function hiFive()
{
    $x = 2;
    /**
     * some docblock
     */
    return $x;
}

class Dummy {
    public function foo()
    {
        return 1;
    }

    public function bar()
    {
        $x = 2;
        return $x;
    }

    public function hello()
    {
        $x = 2;

        return $x;
    }

    public function hi()
    {
        $x = 2;
        // Some comment
        return $x;
    }

    public function hiFive()
    {
        $x = 2;
        /**
         * some docblock
         */
        return $x;
    }
}
