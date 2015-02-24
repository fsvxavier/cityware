<?php

return array(
    'view_helpers' => array(
        'invokables' => array(
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
