<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cityware\Log;

/**
 * Description of SendMail
 *
 * @author fabricio.xavier
 */
class SendMail {

    public function __construct($exception, $event) {
        
        if ($event->getParam('response')->getStatusCode() != 404) {
            
        }
        
    }

}
