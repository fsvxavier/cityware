<?php

namespace Cityware\Generator\Adapter;

use Cityware\Format\FileFolder;
use \Exception;

class ModuleAdapter extends AdapterAbstract
{
    public function setModule($module)
    {
        if (strtolower($module) == 'admin' or strtolower($module) == 'site') {
            throw new Exception('O nomes de modulos "ADMIN" e "SITE" são reservados', 500);
        } else {
            parent::setModule($module);
        }
    }

    public function create()
    {
        if (empty($this->module)) {
            throw new Exception('Não foi definido o nome do modulo a ser criado', 500);
        } else {
            $this->createModuleFolders();
            $this->createConfigFiles();
            $this->createViewFiles();
            $this->createDefaultControllerMvcFile();
            $this->createPublicFiles();
        }
    }

    public function delete()
    {
        if (empty($this->module)) {
            throw new Exception('Não foi definido o nome do modulo a ser excluido', 500);
        } else {

            $libraryFolder = dirname(dirname(dirname(__FILE__)));

            FileFolder::removeFolder(MODULES_PATH . ucfirst($this->getModule()));
            FileFolder::removeFolder(PUBLIC_PATH . 'css' . DS . strtolower($this->getModule()));
            FileFolder::removeFolder(PUBLIC_PATH . 'js' . DS . strtolower($this->getModule()));
            FileFolder::removeFolder(PUBLIC_PATH . 'img' . DS . strtolower($this->getModule()));
            @unlink($libraryFolder . DS . 'Mvc' . DS . 'Controller' . DS . 'AbstractActionController'.  ucfirst($this->getModule()).'.php');

        }
    }

    /**
     * Função de criação das pastas do módulo
     */
    private function createModuleFolders()
    {
        // Criação de pasta de CONFIG do módulo
        FileFolder::createFolder(MODULES_PATH . ucfirst($this->getModule()) . DS . 'config');

        // Criação de pasta de TRANSLATE do módulo
        FileFolder::createFolder(MODULES_PATH . ucfirst($this->getModule()) . DS . 'language');

        // Criação de pastas do SRC do módulo
        FileFolder::createFolder(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'Controller');
        FileFolder::createFolder(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'Models');
        FileFolder::createFolder(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'View' . DS . 'Helper');
        FileFolder::createFolder(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'ini');
        FileFolder::createFolder(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'translate' . DS . 'pt_BR');

        // Criação de pastas de VIEW do módulo
        FileFolder::createFolder(MODULES_PATH . ucfirst($this->getModule()) . DS . 'view' . DS . strtolower($this->getModule()));
        FileFolder::createFolder(MODULES_PATH . ucfirst($this->getModule()) . DS . 'view' . DS . 'error');
        FileFolder::createFolder(MODULES_PATH . ucfirst($this->getModule()) . DS . 'view' . DS . 'layout');

        // Criação de pastas no PUBLIC
        FileFolder::createFolder(PUBLIC_PATH . 'css' . DS . strtolower($this->getModule()));
        FileFolder::createFolder(PUBLIC_PATH . 'js' . DS . strtolower($this->getModule()));
        FileFolder::createFolder(PUBLIC_PATH . 'img' . DS . strtolower($this->getModule()));
    }

    /**
     * Função de criação dos arquivos de configuração do modulo
     */
    private function createConfigFiles()
    {
        $moduleName = ucfirst($this->getModule());
        $moduleNameLower = strtolower($this->getModule());
        $moduleNameUpper = strtoupper($this->getModule());

        /* Criação do arquivo de configuração do módulo */
        $module_config_php = file_get_contents(dirname(__FILE__) . DS . 'Module' . DS . 'Module_config_php.tpl');
        $moduleConfigPhp = str_replace("%moduleNameUpper%", $moduleNameUpper, str_replace("%moduleName%", $moduleName, str_replace("%moduleNameLower%", $moduleNameLower, $module_config_php)));
        file_put_contents(MODULES_PATH . ucfirst($this->getModule()) . DS . 'config' . DS . 'module.config.php', "<?php\n\n".$moduleConfigPhp);
        chmod(MODULES_PATH . ucfirst($this->getModule()) . DS . 'config' . DS . 'module.config.php', 0644);

        /* Criação do arquivo de definição do módulo */
        $module_php = file_get_contents(dirname(__FILE__) . DS . 'Module' . DS . 'Module_php.tpl');
        $modulePhp = str_replace("%moduleNameUpper%", $moduleNameUpper, str_replace("%moduleName%", $moduleName, str_replace("%moduleNameLower%", $moduleNameLower, $module_php)));
        file_put_contents(MODULES_PATH . ucfirst($this->getModule()) . DS . 'Module.php', "<?php\n\n".$modulePhp);
        chmod(MODULES_PATH . ucfirst($this->getModule()) . DS . 'Module.php', 0644);
    }

    private function createViewFiles()
    {
        $moduleName = ucfirst($this->getModule());
        $moduleNameLower = strtolower($this->getModule());
        $moduleNameUpper = strtoupper($this->getModule());

        /* Criação do arquivo de layout do módulo */
        $module_view_layout = file_get_contents(dirname(__FILE__) . DS . 'Module' . DS . 'Module_view_layout.tpl');
        $moduleViewLayout = str_replace("%moduleNameUpper%", $moduleNameUpper, str_replace("%moduleName%", $moduleName, str_replace("%moduleNameLower%", $moduleNameLower, $module_view_layout)));
        file_put_contents(MODULES_PATH . ucfirst($this->getModule()) . DS . 'view' . DS . 'layout' . DS . $moduleNameLower.'.phtml', $moduleViewLayout);
        chmod(MODULES_PATH . ucfirst($this->getModule()) . DS . 'view' . DS . 'layout' . DS . $moduleNameLower.'.phtml', 0644);

        /* Criação do arquivo de erro 404 do módulo */
        $module_error_404 = file_get_contents(dirname(__FILE__) . DS . 'Module' . DS . 'Module_error_404.tpl');
        $moduleError404 = str_replace("%moduleNameUpper%", $moduleNameUpper, str_replace("%moduleName%", $moduleName, str_replace("%moduleNameLower%", $moduleNameLower, $module_error_404)));
        file_put_contents(MODULES_PATH . ucfirst($this->getModule()) . DS . 'view' . DS . 'error' . DS . '404.phtml', $moduleError404);
        chmod(MODULES_PATH . ucfirst($this->getModule()) . DS . 'view' . DS . 'error' . DS . '404.phtml', 0644);

        /* Criação do arquivo de erro INDEX do módulo */
        $module_error_index = file_get_contents(dirname(__FILE__) . DS . 'Module' . DS . 'Module_error_index.tpl');
        $moduleErrorIndex = str_replace("%moduleNameUpper%", $moduleNameUpper, str_replace("%moduleName%", $moduleName, str_replace("%moduleNameLower%", $moduleNameLower, $module_error_index)));
        file_put_contents(MODULES_PATH . ucfirst($this->getModule()) . DS . 'view' . DS . 'error' . DS . 'index.phtml', $moduleErrorIndex);
        chmod(MODULES_PATH . ucfirst($this->getModule()) . DS . 'view' . DS . 'error' . DS . 'index.phtml', 0644);
    }

    /**
     * Função de criação do arquivo de controller padrão do modulo
     */
    private function createDefaultControllerMvcFile()
    {
        $moduleName = ucfirst($this->getModule());
        $moduleNameLower = strtolower($this->getModule());
        $moduleNameUpper = strtoupper($this->getModule());

        $moduleControllerFolder = MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'Controller';

        /* Criação do arquivo de controller padrão do módulo */
        $cityware_mvc_controller = file_get_contents(dirname(__FILE__) . DS . 'Module' . DS . 'Cityware_Mvc_Controller.tpl');
        $citywareMvcController = str_replace("%moduleName%", $moduleName, str_replace("%moduleNameUpper%", $moduleNameUpper, str_replace("%moduleNameLower%", $moduleNameLower, $cityware_mvc_controller)));
        file_put_contents($moduleControllerFolder . DS . 'AbstractActionController'.$moduleName.'.php', $citywareMvcController);
        chmod($moduleControllerFolder . DS . 'AbstractActionController'.$moduleName.'.php', 0644);
    }

    /**
     * Função de criação dos arquivos de configuração do modulo
     */
    private function createPublicFiles()
    {
        $moduleNameLower = strtolower($this->getModule());

        /* Criação do arquivo de controller padrão do módulo */
        file_put_contents(PUBLIC_PATH . 'css' . DS . $moduleNameLower . DS . 'style.css', '/**/');
        chmod(PUBLIC_PATH . 'css' . DS . $moduleNameLower . DS . 'style.css', 0644);

        file_put_contents(PUBLIC_PATH . 'css' . DS . $moduleNameLower . DS . "mediaquery.css", '/**/');
        chmod(PUBLIC_PATH . 'css' . DS . $moduleNameLower . DS . 'mediaquery.css', 0644);
    }

}
