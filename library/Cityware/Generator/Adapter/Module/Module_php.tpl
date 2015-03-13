namespace %moduleName%;

use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\Config\SessionConfig;
use Zend\Session\SessionManager;
use Zend\Session\Container;
use Cityware\Services\ServiceLocatorFactory;
use Cityware\Log\FileSave as LogFileSave;
use Cityware\Log\SendMail as LogSendMail;

class Module {
    
    public function init(ModuleManager $moduleManager) {
        $sharedEvents = $moduleManager->getEventManager()->getSharedManager();

        /* Tratamento da configuração do layout do módulo no evento de dispatch */
        $sharedEvents->attach(__NAMESPACE__, 'dispatch', function($e) {
            $controller = $e->getTarget();
            $controller->layout('layout/%moduleNameLower%');
        }, 100);
    }

    public function onBootstrap(MvcEvent $e) {

        $eventManager = $e->getApplication()->getEventManager();

        /* Acesso o arquivod de coniguração global */
        $config = \Zend\Config\Factory::fromFile(GLOBAL_CONFIG_PATH . 'global.php');

        /* Configura a inicialização de sessao */
        if (isset($config['session'])) {
            $sessionConfig = new SessionConfig();
            $sessionConfig->setOptions($config['session']);

            $sessionManager = new SessionManager($sessionConfig);
            $sessionManager->regenerateId(true);
            $chain = $sessionManager->getValidatorChain();
            $chain->attach('session.validate', array(new \Zend\Session\Validator\HttpUserAgent(), 'isValid'));
            $chain->attach('session.validate', array(new \Zend\Session\Validator\RemoteAddr(), 'isValid'));
            $sessionManager->setValidatorChain($chain);
            $sessionManager->start();

            Container::setDefaultManager($sessionManager);
        }

        /* Configuração do php.ini em tempo de execução */
        if (array_key_exists('php_settings', $config)) {
            $phpSettings = $config['php_settings'];
            if (is_array($phpSettings)) {
                foreach ($phpSettings as $key => $value) {
                    if (false === ini_set($key, $value)) {
                        throw new \RuntimeException('Cannot set ini \'' . $key . '\' to \'' . $value);
                    }
                }
            }
        }

        /* Tratamento das variáveis padrões de modulo, controlador e action no evento de rota */
        $eventManager->attach(\Zend\Mvc\MvcEvent::EVENT_ROUTE, array($this, 'onRouteDefaults'));

        /* Tratamento das variaveis para renderização de layout no evento de render */
        $eventManager->attach(\Zend\Mvc\MvcEvent::EVENT_RENDER, function($e) {
            $children = $e->getViewModel()->getChildren()[0];
            foreach ($children->getVariables() as $key => $value) {
                $e->getViewModel()->setVariable($key, $value);
            }
        });

        /* Tratamento de exceptions nos eventos de erro */
        $eventManager->attach(\Zend\Mvc\MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onException'));
        $eventManager->attach(\Zend\Mvc\MvcEvent::EVENT_RENDER_ERROR, array($this, 'onException'));

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    /**
     * Função de retorno da configuração do módulo
     * @return array
     */
    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * Função de configuração dos arquivos de autoload do módulo
     * @return array
     */
    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
            'Zend\Loader\ClassMapAutoloader' => array(
                LIBRARY_PATH . 'fsvxavier' . DS . 'cityware' . DS . 'autoload_classmap.php',
            ),
        );
    }

    /**
     * Serviço de auto mapping dos controladores
     * @return type
     */
    public function getControllerConfig() {
        return array(
            'abstract_factories' => array(
                'Cityware\Services\ControlAbstractFactory'
            ),
        );
    }

    /**
     * Função de definição de variáveis estáticas defult do sistema
     * @param MvcEvent $e
     */
    public function onRouteDefaults(MvcEvent $e) {
        $route = $e->getRouteMatch();

        $module = $route->getParam('module', strtolower(__NAMESPACE__));
        $controller = $route->getParam('__CONTROLLER__');
        $action = $route->getParam('action');
       
        
        /* define module name, controller name, action name */
        define('MODULE_NAME', strtolower($module));
        define('CONTROLLER_NAME', strtolower($controller));
        define('ACTION_NAME', strtolower($action));
        
        (!defined('LANGUAGE')) ? define('LANGUAGE', $route->getParam('language', 'br')) : null;

        (!defined('MODULE_VIEW')) ? define('MODULE_VIEW', MODULES_PATH . ucfirst($module) . DS . 'view' . DS . strtolower($module) . DS) : null;
        (!defined('MODULE_INI')) ? define('MODULE_INI', MODULES_PATH . ucfirst($module) . DS . 'src' . DS . ucfirst($module) . DS . 'ini' . DS) : null;
        (!defined('MODULE_TRANSLATE')) ? define('MODULE_TRANSLATE', MODULES_PATH . ucfirst($module) . DS . 'src' . DS . ucfirst($module) . DS . 'translate' . DS) : null;
        (!defined('MODULE_CONTROLLER')) ? define('MODULE_CONTROLLER', MODULES_PATH . ucfirst($module) . DS . 'src' . DS . ucfirst($module) . DS . 'Controller' . DS) : null;

        ServiceLocatorFactory::setInstance($e->getApplication()->getServiceManager());
    }

    /**
     * Função de tratamento de erros e log
     * @param MvcEvent $e
     * @return type
     */
    public function onException(MvcEvent $e) {

        $vm = $e->getViewModel();
        $vm->setTemplate('layout/error');

        $config = $e->getApplication()->getServiceManager()->get('config');

        $exception = $e->getParam('exception');

        if (isset($config['eventerrorlogger']) == false) {
            return;
        }
        if ($config['eventerrorlogger']['log'] == false) {
            return;
        }

        foreach ($config['eventerrorlogger']['loggers'] as $loggerName) {

            if (!empty($loggerName) == false) {
                continue;
            }

            /* Grava o Log em arquivo se ativo na configuração global */
            ($loggerName == 'LogFileSave' and ! empty($exception)) ? new LogFileSave($exception, $e) : '';

            /* Envia e-mail com Log se ativo na configuração global */
            ($loggerName == 'LogSendMail' and ! empty($exception)) ? new LogSendMail($exception, $e) : '';
        }
    }

}