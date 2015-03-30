<?php

namespace %moduleName%\Controller;

use Cityware\Mvc\Controller\AbstractActionController;
use Zend\I18n\Translator\Translator;
use Zend\Config\Factory AS ZendConfigFile;

abstract class AbstractActionController%moduleName% extends AbstractActionController {

    public function __construct() {
        parent::__construct();
        $this->session = $this->getSessionAdapter();
        
        $this->defineHtmlHeader();
        $this->defineScriptsBase();
    }

    /**
     * Retorna o adaptador de sessao
     * @return \Zend\Session\Container
     */
    public function getSessionAdapter($name = SESSION_%moduleNameUpper%) {
        return parent::getSessionAdapter($name);
    }

    /**
     * Define os cabeçalhos de HTML da página
     */
    public function defineHtmlHeader() {
        $this->setHeadTitle('Cityware - Modulo %moduleName%');
        $this->setDoctype();
        $this->setContentType();
        $this->setMetaName('viewport', 'width=device-width, initial-scale=1.0');
        $this->setMetaHttpEquiv('X-UA-Compatible', 'IE=edge');
    }

    /**
     * Define os Scripts de CSS e Javascript padrões da página
     */
    public function defineScriptsBase() {
    
        /* Definição do CSS default */
        $this->setHeadCssLink(URL_DEFAULT . 'css/%moduleNameLower%/bootstrap.min.css');
        $this->setHeadCssLink(URL_DEFAULT . 'css/font-awesome.min.css');
        
        $this->setHeadCssLink(URL_DEFAULT . 'css/%moduleNameLower%/style.css');

        /* Definição do JS default */
        $this->setHeadJsLink(URL_DEFAULT . 'js/jquery.min.js');
        $this->setHeadJsLink(URL_DEFAULT . 'js/jquery.browser.min.js');

        $this->setHeadJsLink(URL_DEFAULT . 'js/respond.min.js', Array('conditional' => 'if lt IE 9'));
        $this->setHeadJsLink(URL_DEFAULT . 'js/html5shiv.min.js', Array('conditional' => 'if lt IE 9'));

        $this->setHeadJsLink(URL_DEFAULT . 'js/bootstrap.min.js');
    }

    /**
     * Action padrão de datagrid de dados
     * @return type
     */
    public function indexAction() {
        return $this->getViewModel();
    }
}