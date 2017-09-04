<?php

use stdClass;
use DateTime;
use Closure;
use Error;
use TypeError;
use InvalidArgumentException;

/**
 * @var Closure $closure
 * @property Error $e
 */

$cat = 1;
$dt = new DateTime();

/**
 * @return TypeError
 * @throws InvalidArgumentException
 */
function foo() {
    return;
}
