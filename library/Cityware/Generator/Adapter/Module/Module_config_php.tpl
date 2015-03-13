return array(
    'controllers' => array(
        'invokables' => array(
            '%moduleName%\Controller\Index' => '%moduleName%\Controller\IndexController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'subdomain' => array(
                'type' => 'Hostname',
                'options' => array(
                    'route' => '%moduleNameLower%.localhost.local',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => '%moduleName%\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => '%moduleName%\Controller',
                                'controller' => 'Index',
                                'action' => 'index',
                                'module' => '%moduleNameLower%'
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(//permite mandar dados pela url
                            'wildcard' => array(
                                'type' => 'Zend\Mvc\Router\Http\Wildcard',
                                'options' => array(
                                    'key_value_delimiter' => '/',
                                    'param_delimiter' => '/',
                                ),
                                'may_terminate' => true,
                            ),
                        ),
                    ),
                ),
            ),
            '%moduleNameLower%' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/%moduleNameLower%',
                    'defaults' => array(
                        '__NAMESPACE__' => '%moduleName%\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                        'module' => '%moduleNameLower%'
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => '%moduleName%\Controller',
                                'controller' => 'Index',
                                'action' => 'index',
                                'module' => '%moduleNameLower%'
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(//permite mandar dados pela url
                            'wildcard' => array(
                                'type' => 'Zend\Mvc\Router\Http\Wildcard',
                                'options' => array(
                                    'key_value_delimiter' => '/',
                                    'param_delimiter' => '/',
                                ),
                                'may_terminate' => true,
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'pt_BR',
        'translation_file_patterns' => array(
            array('type' => 'array', 'base_dir' => __DIR__ . '/../src/%moduleName%/translate', 'pattern' => '%s.php'),
            array('type' => 'phparray', 'base_dir'    => __DIR__ . '/../src/%moduleName%/translate', 'pattern' => '/%s/Zend_Validate.php', 'text_domain' => 'default'),
            array('type' => 'gettext', 'base_dir' => __DIR__ . '/../language', 'pattern'  => '%s.mo'),
        ),
    ),
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
        'doctype' => 'XHTML5',
        'layout' => 'layout/%moduleNameLower%',
        'display_exceptions' => true,
        'exception_template' => 'error/index',
        'display_not_found_reason' => true,
        'not_found_template' => 'error/404',
        'template_map' => array(
            '%moduleNameLower%/index/index' => __DIR__ . '/../view/%moduleNameLower%/index/index.phtml',
            'layout/%moduleNameLower%' => __DIR__ . '/../view/layout/%moduleNameLower%.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
        ),
        'template_path_stack' => array(
            '%moduleNameLower%' => __DIR__ . '/../view',
        ),
    ),
    'view_helpers' => array(
        'factories' => array(
            
        ),
        
        'invokables' => array(
            
        )
    ),
    'service_manager' => array(
        'services' => array(
            'error_handler' => 'Error\Controller\ErrorController',
        )
    ),
);
