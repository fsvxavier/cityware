<?php

namespace Cityware\Generator\Adapter;

abstract class AdapterAbstract
{
    protected $module, $controller, $controllerName, $formName, $datagridName, $dbAdapter, $database, $table, $schema = null;

    /**
     * Função construtora
     * @param array $params
     */
    public function __construct(array $params = Array())
    {
        if (isset($params['schema']) and !empty($params['schema'])) {
            $this->setSchema($params['schema']);
        }
        if (isset($params['table']) and !empty($params['table'])) {
            $this->setTable($params['table']);
        }
        if (isset($params['module']) and !empty($params['module'])) {
            $this->setModule($params['module']);
        }
        if (isset($params['controller']) and !empty($params['controller'])) {
            $this->setController($params['controller']);
        }
    }

    public function getModule()
    {
        return $this->module;
    }

    public function setModule($module)
    {
        $this->module = $module;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
    }

    public function getControllerName()
    {
        return $this->controllerName;
    }

    public function setControllerName($controllerName)
    {
        $this->controllerName = $controllerName;
    }

    public function getFormName()
    {
        return $this->formName;
    }

    public function setFormName($formName)
    {
        $this->formName = $formName;
    }

    public function getDatagridName()
    {
        return $this->datagridName;
    }

    public function setDatagridName($datagridName)
    {
        $this->datagridName = $datagridName;
    }
    
    public function getDbAdapter() {
        return $this->dbAdapter;
    }

    public function setDbAdapter($dbAdapter) {
        $this->dbAdapter = $dbAdapter;
    }

        
    public function getDatabase() {
        return $this->database;
    }

    public function setDatabase($database) {
        $this->database = $database;
    }

    
    public function getTable()
    {
        return $this->table;
    }

    public function setTable($table)
    {
        $this->table = $table;
    }

    public function getSchema()
    {
        return $this->schema;
    }

    public function setSchema($schema)
    {
        $this->schema = $schema;
    }
    
    /**
     * Metodo Abstrato para criação
     */
    abstract public function create();

    /**
     * Metodo Abstrato para exclusão
     */
    abstract public function delete();
}
