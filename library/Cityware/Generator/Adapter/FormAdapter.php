<?php

namespace Cityware\Generator\Adapter;

use \Exception;

class FormAdapter extends AdapterAbstract {

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
            $this->createFormFiles();
        }
    }

    /**
     * Função de criação dos arquivos de Formulário
     * @throws \Exception
     */
    private function createFormFiles() {
        $db = \Cityware\Db\Factory::factory('zend');
        $tableMetadata = new \Zend\Db\Metadata\Metadata($db->getAdapter());
        $tableInfo = $tableMetadata->getTable($this->getTable(), $this->getSchema());

        $iniFile = $this->genConfigFormIni();
        $iniFile .= $this->genFieldsFormIni($tableInfo);
        $iniFile .= $this->genButtonsFormIni();

        $translateFile = $this->genTranslateFormArray($tableInfo);

        $moduleName = ucfirst($this->getModule());
        $moduleNameLower = strtolower($this->getModule());

        try {
            if (!empty($this->formName)) {
                file_put_contents(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'ini' . DS . strtolower($this->getController()) . DS . $this->formName . '.ini', $iniFile);
                chmod(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'ini' . DS . strtolower($this->getController()) . DS . $this->formName . '.ini', 0777);

                file_put_contents(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'translate' . DS . 'pt_BR' . DS . strtolower($this->getController()) . DS . $this->formName . '.php', $translateFile);
                chmod(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'translate' . DS . 'pt_BR' . DS . strtolower($this->getController()) . DS . $this->formName . '.php', 0777);

                $template_Form = file_get_contents(dirname(__FILE__) . DS . 'Form' . DS . 'Template_Form.tpl');
                $templateForm = str_replace("%moduleName%", $moduleName, str_replace("%moduleNameLower%", $moduleNameLower, $template_Form));
                file_put_contents(MODULES_PATH . ucfirst($this->getModule()) . DS . 'view' . DS . strtolower($this->getModule()) . DS . strtolower($this->getController()) . DS . $this->formName . '.phtml', $templateForm);
                chmod(MODULES_PATH . ucfirst($this->getModule()) . DS . 'view' . DS . strtolower($this->getModule()) . DS . strtolower($this->getController()) . DS . $this->formName . '.phtml', 0644);
            } else {
                file_put_contents(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'ini' . DS . strtolower($this->getController()) . DS . 'add.ini', $iniFile);
                chmod(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'ini' . DS . strtolower($this->getController()) . DS . 'add.ini', 0777);

                file_put_contents(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'translate' . DS . 'pt_BR' . DS . strtolower($this->getController()) . DS . 'add.php', $translateFile);
                chmod(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'translate' . DS . 'pt_BR' . DS . strtolower($this->getController()) . DS . 'add.php', 0777);

                file_put_contents(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'ini' . DS . strtolower($this->getController()) . DS . 'edit.ini', $iniFile);
                chmod(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'ini' . DS . strtolower($this->getController()) . DS . 'edit.ini', 0777);

                file_put_contents(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'translate' . DS . 'pt_BR' . DS . strtolower($this->getController()) . DS . 'edit.php', $translateFile);
                chmod(MODULES_PATH . ucfirst($this->getModule()) . DS . 'src' . DS . ucfirst($this->getModule()) . DS . 'translate' . DS . 'pt_BR' . DS . strtolower($this->getController()) . DS . 'edit.php', 0777);

                $template_Form = file_get_contents(dirname(__FILE__) . DS . 'Form' . DS . 'Template_Form.tpl');
                $templateForm = str_replace("%moduleName%", $moduleName, str_replace("%moduleNameLower%", $moduleNameLower, $template_Form));

                file_put_contents(MODULES_PATH . ucfirst($this->getModule()) . DS . 'view' . DS . strtolower($this->getModule()) . DS . strtolower($this->getController()) . DS . 'add.phtml', $templateForm);
                chmod(MODULES_PATH . ucfirst($this->getModule()) . DS . 'view' . DS . strtolower($this->getModule()) . DS . strtolower($this->getController()) . DS . 'add.phtml', 0644);

                file_put_contents(MODULES_PATH . ucfirst($this->getModule()) . DS . 'view' . DS . strtolower($this->getModule()) . DS . strtolower($this->getController()) . DS . 'edit.phtml', $templateForm);
                chmod(MODULES_PATH . ucfirst($this->getModule()) . DS . 'view' . DS . strtolower($this->getModule()) . DS . strtolower($this->getController()) . DS . 'edit.phtml', 0644);
            }
        } catch (Exception $exc) {
            throw new Exception('Não foi possivel criar o arquivo de configuração do formulário! <br />' . $exc->getMessage(), 500);
        }
    }

    /**
     * Função geradora do arquivo de tradução do formulário
     * @param  object $tableInfo
     * @return string
     */
    private function genTranslateFormArray($tableInfo) {
        $stringArray = "<?php\n\nreturn array(";

        $nome = (!empty($this->controllerName)) ? $this->getControllerName() : "Nome do Datagrid";

        $title = "'title_action' => '{$nome}',\n
            'subtitle_action' => 'Aqui você poderá gerenciar o(s) {$nome} do sistema',\n";

        $buttons = "\n\n'btn_submit' => 'Enviar',\n
            'btn_reset' => 'Limpar',\n";

        $fields = null;
        foreach ($tableInfo->getColumns() as $value) {

            $fields .= "\n\n'{$value->getName()}' => '{$value->getName()}',\n
                '{$value->getName()}_description' => 'Descrição',\n
                '{$value->getName()}_placeholder' => 'Placeholder',\n
                '{$value->getName()}_tooltip' => 'Tooltip',\n";
                

            if (strtolower($value->getName()) === 'ind_status') {
                $fields .= "'{$value->getName()}_values' => 'Ativo,Bloqueado',\n";
            }
        }

        $stringArray .= $title . "\n";
        $stringArray .= $fields . "\n";
        $stringArray .= $buttons . "\n";

        $stringArray .= ");";

        return $stringArray;
    }

    /**
     * Função de geração dos dados de configuração do formulário
     * @return string
     */
    private function genConfigFormIni() {
        $classTable = \Cityware\Format\Text::convertTableName($this->table);

        $return = "[formconfig]\n\n";
        $return .= "form.module = \"{$this->module}\"\n";
        $return .= "form.controller = \"{$this->controller}\"\n";
        $return .= "form.enctype = \"multipart/form-data\"\n";
        $return .= "form.method = \"post\"\n";
        if ($this->schema != null) {
            $return .= "form.schema = \"{$this->schema}\"\n";
        }
        $return .= "form.destination = \"{$this->controller}\"\n";
        $return .= "form.table = \"{$this->table}\"\n";
        $return .= "form.tableClass = \"{$classTable}\"\n";
        $return .= "form.pathfiles = \"{$this->controller}\"\n";
        $return .= "form.id = \"form{$classTable}\"\n";

        return $return;
    }

    /**
     * Função de geração dos campos do formulário
     * @param  object $tableInfo
     * @return string
     */
    private function genFieldsFormIni($tableInfo) {
        $return = "\n\n[formfieldsconfig]\n\n";

        $primaryKeyColumn = null;

        foreach ($tableInfo->getConstraints() as $key => $value) {
            if ($value->getType() == 'PRIMARY KEY') {
                $temp = $value->getColumns();
                $primaryKeyColumn = $temp[0];
            }
        }

        foreach ($tableInfo->getColumns() as $value) {

            $returnExtra = $returnBody = $returnType = $returnValidation = null;

            $key = $value->getName();

            if ($key == $primaryKeyColumn) {
                $returnType .= "{$key}.type = \"primary\"\n";
                $returnBody .= "{$key}.name = \"{$key}\"\n";
            } elseif (strtolower($key) === 'ind_status') {
                $returnType .= "{$key}.type = \"status\"\n";
                $returnBody .= "{$key}.name = \"{$key}\"\n";
                $returnExtra .= "{$key}.values = \"A,B\"\n";
            } else {

                switch (strtolower($value->getDataType())) {
                    case 'integer':
                        $returnType .= "{$key}.type = \"number\"\n";
                        $returnValidation .= "{$key}.validation = \"required number\"\n";
                        $returnValidation .= "{$key}.validationtype[] = \"int\"\n\n";
                        break;
                    case 'text':
                        $returnType .= "{$key}.type = \"textarea\"\n";
                        $returnValidation .= "{$key}.validation = \"required\"\n\n";
                        break;
                    case 'character varying':
                        $returnType .= "{$key}.type = \"text\"\n";
                        $returnValidation .= "{$key}.validation = \"required\"\n\n";
                        break;
                    case 'character':
                        $returnType .= "{$key}.type = \"status\"\n";
                        $returnValidation .= "{$key}.validation = \"required\"\n\n";
                        break;
                    case 'timestamp without time zone':
                        $returnType .= "{$key}.type = \"datetime\"\n";
                        $returnValidation .= "{$key}.validation = \"required\"\n";
                        $returnValidation .= "{$key}.validationtype[] = \"datetime\"\n\n";
                        break;
                    case 'date':
                        $returnType .= "{$key}.type = \"date\"\n";
                        $returnValidation .= "{$key}.validation = \"required\"\n";
                        $returnValidation .= "{$key}.validationtype[] = \"date\"\n\n";
                        break;
                    case 'time without time zone':
                        $returnType .= "{$key}.type = \"time\"\n";
                        $returnValidation .= "{$key}.validation = \"required\"\n";
                        $returnValidation .= "{$key}.validationtype[] = \"time\"\n\n";
                        break;
                    case 'decimal':
                    case 'float':
                    case 'double':
                        $returnType .= "{$key}.type = \"float\"\n";
                        $returnValidation .= "{$key}.validation = \"required\"\n";
                        $returnValidation .= "{$key}.validationtype[] = \"float\"\n\n";
                        break;
                    default:
                        $returnType .= "{$key}.type = \"text\"\n";
                        $returnValidation .= "{$key}.validation = \"required\"\n\n";
                        break;
                }

                $returnBody .= "{$key}.name = \"{$key}\"\n";
                $returnBody .= "{$key}.tooltip = \"false\"\n";
                $returnBody .= "{$key}.description = \"false\"\n";
                $returnBody .= "{$key}.placeholder = \"false\"\n";
            }
            $return .= $returnType . $returnBody . $returnValidation . $returnExtra . "\n";
        }

        return $return;
    }

    /**
     * Função de geração dos botões do formulário
     * @return string
     */
    private function genButtonsFormIni() {
        $return = "[formbuttonconfig]\n\n";
        $return .= "submit.type = \"submit\"\n";
        $return .= "submit.name = \"submit\"\n\n";
        $return .= "submit.class = \"btn-success\"\n\n";

        return $return;
    }

    public function delete() {
        
    }

}
