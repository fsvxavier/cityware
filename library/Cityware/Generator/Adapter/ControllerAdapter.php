<?php

namespace Cityware\Generator\Adapter;

use Cityware\Format\FileFolder;
use \Exception;

class ControllerAdapter extends AdapterAbstract
{
    public function delete()
    {
        // Remove as pastas do SRC do controlador
        FileFolder::removeFolder(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'Models' . DS . strtolower($this->getController()));
        FileFolder::removeFolder(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'ini' . DS . strtolower($this->getController()));
        FileFolder::removeFolder(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'translate' . DS . 'pt_BR' . DS . strtolower($this->getController()));

        // Remove a pasta de VIEW do controlador
        FileFolder::removeFolder(MODULES_PATH . ucfirst($this->getModule()) . DS . 'view' . DS . strtolower($this->getModule()) . DS . strtolower($this->getController()));

        // Remove o arquivo principal do controlador
        @unlink(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'Controller' . DS . ucfirst($this->getController()).'Controller.php');
    }

    public function create()
    {
        if (!is_dir(MODULES_PATH . ucfirst($this->getModule()))) {
            throw new Exception('Esta módulo não foi criado ou está escrito errado', 500);
        } elseif (empty($this->module)) {
            throw new Exception('Não foi definido o nome do modulo a ser criado', 500);
        } elseif (empty($this->controller)) {
            throw new Exception('Não foi definido o nome do controller a ser criado', 500);
        } else {
            $this->createControllerFolders();
            $this->createControllerFiles();
        }
    }

    /**
     * Função de criação das pastas do controlador
     */
    private function createControllerFolders()
    {
        // Criação de pastas do SRC do controlador
        FileFolder::createFolder(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'Models' . DS . strtolower($this->getController()));
        FileFolder::createFolder(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'ini' . DS . strtolower($this->getController()));
        FileFolder::createFolder(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'translate' . DS . 'pt_BR' . DS . strtolower($this->getController()));

        // Criação de pasta de VIEW do controlador
        FileFolder::createFolder(MODULES_PATH . ucfirst($this->getModule()) . DS . 'view' . DS . strtolower($this->getModule()) . DS . strtolower($this->getController()));
    }

    /**
     * Função de criação do arquivo de controller
     */
    private function createControllerFiles()
    {
        $moduleName = ucfirst($this->getModule());
        $moduleNameLower = strtolower($this->getModule());

        $controllerName = ucfirst($this->getController());
        $controllerNameLower = strtolower($this->getController());
        $controllerNameUpper = strtoupper($this->getController());

        /* Criação do arquivo de principal do controlador */
        $src_module_controller = file_get_contents(dirname(__FILE__) . DS . 'Controller' . DS . 'Src_Module_Controller.tpl');
        $srcModuleController = str_replace("%controllerNameUpper%", $controllerNameUpper, str_replace("%controllerNameLower%", $controllerNameLower, str_replace("%controllerName%", $controllerName, str_replace("%moduleName%", $moduleName, str_replace("%moduleNameLower%", $moduleNameLower, $src_module_controller)))));
        file_put_contents(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'Controller' . DS . ucfirst($this->getController()).'Controller.php', $srcModuleController);
        chmod(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'Controller' . DS . ucfirst($this->getController()).'Controller.php', 0644);

        /* Criação do arquivo de VIEW do controlador */
        file_put_contents(MODULES_PATH . ucfirst($this->getModule()) . DS . 'view' . DS . strtolower($this->getModule()) . DS . strtolower($this->getController()) . DS . 'index.phtml', '');
        chmod(MODULES_PATH . ucfirst($this->getModule()) . DS . 'view' . DS . strtolower($this->getModule()) . DS . strtolower($this->getController()) . DS . 'index.phtml', 0644);

        /* Criação do arquivo de TRANSLATE do controlador */
        file_put_contents(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'translate' . DS . 'pt_BR' . DS . strtolower($this->getController()) . DS . 'index.php', "<?php\n\nreturn Array();");
        chmod(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'translate' . DS . 'pt_BR' . DS . strtolower($this->getController()) . DS . 'index.php', 0644);

    }
}
