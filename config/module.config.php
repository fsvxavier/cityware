<?php

return array(
    'view_helpers' => array(
        'invokables' => array(
            'currency' => 'Cityware\View\Helper\Currency',
            'number' => 'Cityware\View\Helper\Number',
        )
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'getParam' => 'Cityware\Mvc\Controller\Plugins\GetParam',
            'setParam' => 'Cityware\Mvc\Controller\Plugins\SetParam',
            'getAllParams' => 'Cityware\Mvc\Controller\Plugins\GetAllParams',
        )
    ),
    'global_variables' => array(
        
    ),
);
