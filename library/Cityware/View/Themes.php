<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cityware\View;

/**
 * Description of Themes
 *
 * @author fabricio.xavier
 */
class Themes {

    private $loadConfig, $moduleName, $controllerName, $actionName;

    public function __construct($moduleName, $controllerName = null, $actionName = null) {

        $this->moduleName = $moduleName;
        $this->controllerName = $controllerName;
        $this->actionName = $actionName;

        $themeConfigPath = MODULES_PATH . ucfirst($moduleName) . DS . 'config' . DS . 'theme.config.ini';
        $this->loadConfig = \Zend\Config\Factory::fromFile($themeConfigPath);
    }
    
    public function getPathLayout() {
        if (isset($this->loadConfig['themedefault']['theme']['path']['layout'])) {
            return PUBLIC_PATH . str_replace("/", DS, $this->loadConfig['themedefault']['theme']['path']['layout']);
        } else {
            throw new \Exception('Não foi definido um tema ativo, verifique o arquivo de configuração de temas!');
        }
    }

    public function getPathViews() {
        if (isset($this->loadConfig['themeactive']['theme']['path']['views'])) {
            return PUBLIC_PATH . str_replace("/", DS, $this->loadConfig['themeactive']['theme']['path']['views']);
        } else {
            throw new \Exception('Não foi definido um tema ativo, verifique o arquivo de configuração de temas!');
        }
    }

    public function getPathSkin() {
        if (isset($this->loadConfig['themeactive']['theme']['path']['views'])) {
            return PUBLIC_PATH . str_replace("/", DS, $this->loadConfig['themeactive']['theme']['path']['skin']);
        } else {
            throw new \Exception('Não foi definido um tema ativo, verifique o arquivo de configuração de temas!');
        }
    }

    public function getLinkSkin() {
        if (isset($this->loadConfig['themeactive']['theme']['path']['skin'])) {
            return URL_DEFAULT . $this->loadConfig['themeactive']['theme']['path']['skin'] . '/';
        } else {
            throw new \Exception('Não foi definido um tema ativo, verifique o arquivo de configuração de temas!');
        }
    }

    public function getResolverMap() {
        $templateMapResolver = new \Zend\View\Resolver\TemplateMapResolver();
        
        $arrayMapResolver = Array(
            "{$this->moduleName}/{$this->controllerName}/{$this->actionName}" => $this->getPathViews() . DS . "templates" . DS . "{$this->controllerName}" . DS . "{$this->actionName}.phtml",
            'layout/' . $this->moduleName => $this->getPathLayout() . DS . $this->moduleName . '.phtml',
            'layout/error' => $this->getPathLayout() . DS . 'error.phtml',
            'error/index' => $this->getPathViews() . DS . 'templates' . DS . 'error' . DS . 'index.phtml',
            'error/404' => $this->getPathViews() . DS . 'templates' . DS . 'error' . DS . '404.phtml',
        );
        
        return $templateMapResolver->setMap($arrayMapResolver);
    }
    
    public function getResolverPaths() {
        $pathResolver = new \Zend\View\Resolver\TemplatePathStack();
        
        $arrayPathResolver = Array(
            $this->moduleName . '_layout' => $this->getPathLayout(),
            $this->moduleName . '_template' => $this->getPathViews() . DS . 'templates',
            $this->moduleName . '_helpers' => $this->getPathViews() . DS . 'helpers',
        );
        
        return $pathResolver->setPaths($arrayPathResolver);
        
    }

}
