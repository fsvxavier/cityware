<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cityware\Log;

/**
 * Description of FileSave
 *
 * @author fabricio.xavier
 */
class FileSave {

    public function __construct($exception, $event) {
        $log = new \Zend\Log\Logger();
        $writer = new \Zend\Log\Writer\Stream(LOG_PATH . 'log_' . date('Y_m_d') . '.log', 'a+', "\n\n");
        $logger = $log->addWriter($writer);
        $routeMatch = $event->getRouteMatch();
        $logger->debug("\n--------------------- INICIO DO LOG ----------------------\n");
        if (isset($routeMatch) and ( is_object($routeMatch) and ! empty($routeMatch))) {
            $logger->info($routeMatch->getMatchedRouteName());
            $logger->info($routeMatch->getParams());
        }
        
        if ($event->getParam('response')->getStatusCode() != 404) {
            //$logger->info($e->getRequest());
            $logger->err($exception->getFile());
            $logger->err($exception->getLine());
            $logger->err($exception->getCode());
            $logger->err($exception->getMessage());
            $logger->err($exception->getTraceAsString());
        }
        $logger->debug("\n--------------------- FINAL DO LOG ----------------------\n\n\n\n");
    }

}
