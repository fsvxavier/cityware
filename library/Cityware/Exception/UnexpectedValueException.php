<?php

namespace Cityware\Exception;

/**
 * Unexpected value exception
 *
 * Exception thrown if a value does not match with a set of values. This
 * typically happens when a function calls another function and expects the
 * return value to be of a certian type or value, not including arithmetic or
 * buffer related errors.
 */
class UnexpectedValueException extends \UnexpectedValueException implements ExceptionInterface
{
}
