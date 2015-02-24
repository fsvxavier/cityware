<?php

namespace Cityware\Security\Ids;

abstract class Log
{
    protected $logger = null;
    protected $resource = null;

    abstract public function emergency($message, array $context = array());

    abstract public function alert($message, array $context = array());

    abstract public function critical($message, array $context = array());

    abstract public function error($message, array $context = array());

    abstract public function warning($message, array $context = array());

    abstract public function notice($message, array $context = array());

    abstract public function info($message, array $context = array());

    abstract public function debug($message, array $context = array());

    abstract public function log($level, $message, array $context = array());

    /**
     * Init the object and connect if string is given
     *
     * @param object $connectString Connection string to logger instance
     */
    public function __construct($connectString = null)
    {
        if ($connectString !== null) {
            $this->connect($connectString);
        }
    }

    /**
     * Set the logger object instance
     *
     * @param object $logger Logger instance
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * Get the current logger instance
     *
     * @return object Logger instance
     */
    public function getLogger()
    {
        return $this->logger;
    }

}
