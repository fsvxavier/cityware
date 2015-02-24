<?php
namespace Cityware\Exception;

/**
 * Runtime exception
 *
 * Exception thrown if an error which can only be found on runtime occurs.
 */
class RuntimeException extends \RuntimeException implements ExceptionInterface
{
}
