<?php

namespace Cityware\Generator\Adapter;

use \Exception;

class DatagridAdapter extends AdapterAbstract {

    public function create() {
        if (!is_dir(MODULES_PATH . ucfirst($this->getModule()))) {
            throw new Exception('Esta módulo não foi criado ou está escrito errado', 500);
        } elseif (empty($this->module)) {
            throw new Exception('Não foi definido o nome do modulo a ser criado', 500);
        } elseif (empty($this->controller)) {
            throw new Exception('Não foi definido o nome do controller a ser criado', 500);
        } elseif (empty($this->table)) {
            throw new Exception('Não foi definido a tabela do banco de dados para criação do formulário', 500);
        } elseif (!is_file(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'Controller' . DS . ucfirst($this->getController()) . 'Controller.php')) {
            throw new Exception('Não foi criado o controlador para este formuluário', 500);
        } else {
            $this->createDatagridFiles();
        }
    }

    /**
     * Função de criação dos arquivos do Datagrid
     * @throws \Exception
     */
    private function createDatagridFiles() {
        $db = \Cityware\Db\Factory::factory('zend');
        $tableMetadata = new \Zend\Db\Metadata\Metadata($db->getAdapter());
        $tableInfo = $tableMetadata->getTable($this->getTable(), $this->getSchema());

        $iniFile = $this->genConfigDatagridIni();
        $iniFile .= $this->genFieldsDatagridIni($tableInfo);

        $translateFile = $this->genTranslateDatagridArray($tableInfo);

        $moduleName = ucfirst($this->getModule());
        $moduleNameLower = strtolower($this->getModule());

        try {
            if (!empty($this->datagridName)) {
                file_put_contents(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'ini' . DS . strtolower($this->getController()) . DS . $this->datagridName . '.ini', $iniFile);
                chmod(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'ini' . DS . strtolower($this->getController()) . DS . $this->datagridName . '.ini', 0777);

                file_put_contents(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'translate' . DS . 'pt_BR' . DS . strtolower($this->getController()) . DS . $this->datagridName . '.php', $translateFile);
                chmod(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'translate' . DS . 'pt_BR' . DS . strtolower($this->getController()) . DS . $this->datagridName . '.php', 0777);

                $datagrid_template_phtml = file_get_contents(dirname(__FILE__) . DS . 'Datagrid' . DS . 'Template_Datagrid_Index.tpl');
                $datagridTemplatePhtml = str_replace("%moduleName%", $moduleName, str_replace("%moduleNameLower%", $moduleNameLower, $datagrid_template_phtml));
                file_put_contents(MODULES_PATH . ucfirst($this->getModule()) . DS . 'view' . DS . strtolower($this->getModule()) . DS . strtolower($this->getController()) . DS . $this->datagridName . '.phtml', $datagridTemplatePhtml);
                chmod(MODULES_PATH . ucfirst($this->getModule()) . DS . 'view' . DS . strtolower($this->getModule()) . DS . strtolower($this->getController()) . DS . $this->datagridName . '.phtml', 0644);
            } else {
                file_put_contents(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'ini' . DS . strtolower($this->getController()) . DS . 'datagrid.ini', $iniFile);
                chmod(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'ini' . DS . strtolower($this->getController()) . DS . 'datagrid.ini', 0777);

                file_put_contents(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'translate' . DS . 'pt_BR' . DS . strtolower($this->getController()) . DS . 'datagrid.php', $translateFile);
                chmod(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'translate' . DS . 'pt_BR' . DS . strtolower($this->getController()) . DS . 'datagrid.php', 0777);

                $datagrid_template_phtml = file_get_contents(dirname(__FILE__) . DS . 'Datagrid' . DS . 'Template_Datagrid_Index.tpl');
                $datagridTemplatePhtml = str_replace("%moduleName%", $moduleName, str_replace("%moduleNameLower%", $moduleNameLower, $datagrid_template_phtml));
                file_put_contents(MODULES_PATH . ucfirst($this->getModule()) . DS . 'view' . DS . strtolower($this->getModule()) . DS . strtolower($this->getController()) . DS . 'index.phtml', $datagridTemplatePhtml);
                chmod(MODULES_PATH . ucfirst($this->getModule()) . DS . 'view' . DS . strtolower($this->getModule()) . DS . strtolower($this->getController()) . DS . 'index.phtml', 0644);

                $datagrid_template_trash = file_get_contents(dirname(__FILE__) . DS . 'Datagrid' . DS . 'Template_Datagrid_Trash.tpl');
                $datagridTemplateTrash = str_replace("%moduleName%", $moduleName, str_replace("%moduleNameLower%", $moduleNameLower, $datagrid_template_trash));
                file_put_contents(MODULES_PATH . ucfirst($this->getModule()) . DS . 'view' . DS . strtolower($this->getModule()) . DS . strtolower($this->getController()) . DS . 'trash.phtml', $datagridTemplateTrash);
                chmod(MODULES_PATH . ucfirst($this->getModule()) . DS . 'view' . DS . strtolower($this->getModule()) . DS . strtolower($this->getController()) . DS . 'trash.phtml', 0644);
            }
        } catch (Exception $exc) {
            throw new Exception('Não foi possivel criar o arquivo de configuração do Datagrid! <br />' . $exc->getMessage(), 500);
        }
    }

    /**
     * Função geradora do arquivo de tradução do formulário
     * @param  object $tableInfo
     * @return string
     */
    private function genTranslateDatagridArray($tableInfo) {
        $primaryKeyColumn = null;

        foreach ($tableInfo->getConstraints() as $value) {
            if ($value->getType() == 'PRIMARY KEY') {
                $temp = $value->getColumns();
                $primaryKeyColumn = $temp[0];
            }
        }

        $stringArray = "<?php\n\nreturn array(";

        $nome = (!empty($this->controllerName)) ? $this->getControllerName() : "Nome do Datagrid";

        $title = "'title_action' => '{$nome}',\n
            'subtitle_action' => 'Aqui você poderá gerenciar o(s) {$nome} do sistema',\n\n";

        $fields = null;
        foreach ($tableInfo->getColumns() as $value) {

            if ($value->getName() === 'ind_status') {
                $fields .= "'ind_status' => 'Status',\n";
                $fields .= "'ind_status_values' => 'Ativo,Bloqueado',\n";
            } elseif ($value->getName() === $primaryKeyColumn) {
                $fields .= "'{$value->getName()}' => 'ID',\n";
            } elseif ($value->getName() === 'dta_cadastro') {
                $fields .= "'{$value->getName()}' => 'Data de Cadastro',\n";
            } elseif (stripos($value->getName(), 'nom_') !== false) {
                $fields .= "'{$value->getName()}' => 'Nome',\n";
            } else {
                $fields .= "'{$value->getName()}' => '{$value->getName()}',\n";
            }
        }

        $stringArray .= $title;
        $stringArray .= $fields;

        $stringArray .= ");";

        return $stringArray;
    }

    /**
     * Função de geração dos dados de configuração da grid
     * @return string
     */
    private function genConfigDatagridIni() {
        $classTable = \Cityware\Format\Text::convertTableName($this->table);

        $aliases = explode("_", $this->table);

        $alias = '';
        foreach ($aliases as $value) {
            $alias .= strtolower($value[0]);
        }

        $return = "[gridconfig]\n\n";
        $return .= "grid.module = \"{$this->module}\"\n";
        $return .= "grid.controller = \"{$this->controller}\"\n";
        if ($this->schema != null) {
            $return .= "grid.schema = \"{$this->schema}\"\n";
        }
        $return .= "grid.table = \"{$this->table}\"\n";
        $return .= "grid.tableAlias = \"{$alias}\"\n";
        $return .= "grid.tableClass = \"{$classTable}\"\n";
        $return .= "grid.orderdefault = \"ASC\"\n";

        return $return;
    }

    /**
     * Função de geração dos campos da grid
     * @param  object $tableInfo
     * @return string
     */
    private function genFieldsDatagridIni($tableInfo) {
        $return = "\n\n[gridfieldsconfig]\n\n";

        $primaryKeyColumn = null;

        foreach ($tableInfo->getConstraints() as $value) {
            if ($value->getType() == 'PRIMARY KEY') {
                $temp = $value->getColumns();
                $primaryKeyColumn = $temp[0];
            }
        }

        $return .= "{$primaryKeyColumn}.name = \"{$primaryKeyColumn}\"\n";
        $return .= "{$primaryKeyColumn}.sortable = \"true\"\n";
        $return .= "{$primaryKeyColumn}.searchable = \"true\"\n";
        $return .= "{$primaryKeyColumn}.align = \"left\"\n";
        $return .= "{$primaryKeyColumn}.type = \"primarykey\"\n\n";

        $names = Array();
        foreach ($tableInfo->getColumns() as $value) {
            $names[] = $value->getName();

            if (stripos($value->getName(), 'nom_') !== false) {
                $return .= "{$value->getName()}.name = \"{$value->getName()}\"\n";
                $return .= "{$value->getName()}.sortable = \"true\"\n";
                $return .= "{$value->getName()}.searchable = \"true\"\n";
                $return .= "{$value->getName()}.align = \"left\"\n";
                $return .= "{$value->getName()}.type = \"string\"\n\n";
            }

            if ('ind_status' === $value->getName()) {
                $return .= "ind_status.name = \"ind_status\"\n";
                $return .= "ind_status.sortable = \"true\"\n";
                $return .= "ind_status.searchable = \"true\"\n";
                $return .= "ind_status.align = \"left\"\n";
                $return .= "ind_status.class = \" filter-select \"\n";
                $return .= "ind_status.type = \"status\"\n";
                $return .= "ind_status.values = \"A,B\"\n\n";
            }

            if ('dta_cadastro' === $value->getName()) {
                $return .= "dta_cadastro.name = \"dta_cadastro\"\n";
                $return .= "dta_cadastro.sortable = \"true\"\n";
                $return .= "dta_cadastro.searchable = \"true\"\n";
                $return .= "dta_cadastro.align = \"left\"\n";
                $return .= "dta_cadastro.type = \"date\"\n\n";
            }
        }

        return $return;
    }

    public function delete() {
        
    }

}
