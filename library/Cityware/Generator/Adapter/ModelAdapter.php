<?php

namespace Cityware\Generator\Adapter;

use Cityware\Component\Filesystem;
use Zend\Db\Metadata\Metadata;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\DocBlock\Tag;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\PropertyGenerator;

class ModelAdapter extends AdapterAbstract {

    private $fileFolder;
    private $ormFolder;
    private $oMetadata;

    public function __construct(array $params = Array()) {
        parent::__construct($params);

        $db = \Cityware\Db\Factory::factory();
        $this->setDbAdapter($db->getAdapter());

        $this->fileFolder = new Filesystem();
        $this->ormFolder = DATA_PATH . 'Orm' . DS;
        $this->oMetadata = new Metadata($this->getDbAdapter());

        return $this;
    }

    public function delete() {
        
    }

    /**
     * Função de criação dos models
     */
    public function create() {
        $this->createFolders();
        $this->generatorClassEntity();
    }

    /**
     * Função de criação de pasta do schema do banco
     * 
     * @return \Cityware\Generator\Adapter\ModelAdapter
     */
    private function createFolders() {
        foreach ($this->oMetadata->getSchemas() as $valueSchema) {
            if (!is_dir($this->ormFolder . $valueSchema)) {
                $this->fileFolder->mkdir($this->ormFolder . $this->toCamelCase($valueSchema) . DS . 'Entities');
                $this->fileFolder->mkdir($this->ormFolder . $this->toCamelCase($valueSchema) . DS . 'Tables');
            }
        }
        return $this;
    }

    /**
     * Função geradora das entidades dos schemas e tabelas do banco de dados
     * 
     * @return \Cityware\Generator\Adapter\ModelAdapter
     */
    private function generatorClassEntity() {

        /* Lista os schemas do banco de dados */
        foreach ($this->oMetadata->getSchemas() as $valueSchema) {

            $tableNames = $this->oMetadata->getTableNames($valueSchema);

            /* Lista as tabelas do banco de dados */
            foreach ($tableNames as $tableName) {

                $multiPk = $primaryKey = $bodyExchangeArray = null;

                $class = new ClassGenerator();

                $class->setNamespaceName('Orm\Admin\Entities');

                $docBlockClass = DocBlockGenerator::fromArray(array(
                            'shortDescription' => 'Classe tipo model da tabela ' . $tableName . ' dentro do schema ' . $valueSchema,
                            'longDescription' => null,
                ));

                $class->setDocBlock($docBlockClass);
                $class->setName($this->toCamelCase($tableName));

                $class->addProperty('DBSCHEMA', $valueSchema, PropertyGenerator::FLAG_CONSTANT);
                $class->addProperty('DBTABLE', $tableName, PropertyGenerator::FLAG_CONSTANT);

                foreach ($this->oMetadata->getConstraints($tableName, $valueSchema) as $constraint) {
                    if (!$constraint->hasColumns()) {
                        continue;
                    }

                    if ($constraint->isPrimaryKey()) {
                        $columns = $constraint->getColumns();

                        if (count($columns) > 1) {
                            $multiPk = true;
                            $primaryKey = implode(', ', $columns);
                        } else {
                            $multiPk = false;
                            $primaryKey = $columns[0];
                        }
                        $class->addProperty('MULTIPK', $multiPk, PropertyGenerator::FLAG_CONSTANT);
                        $class->addProperty('PKCOLUMN', $primaryKey, PropertyGenerator::FLAG_CONSTANT);
                    }
                }



                /* Cria os metodos setter/getter e as variáveis das colunas da tabela */

                $table = $this->oMetadata->getTable($tableName, $valueSchema);

                /* Lista as colunas da tabela do banco de dados */
                foreach ($table->getColumns() as $column) {

                    $varName = $this->camelCase($column->getName());
                    $class->addProperty($varName, null, PropertyGenerator::FLAG_PRIVATE);

                    $methodGet = 'get' . $this->toCamelCase($column->getName());
                    $methodSet = 'set' . $this->toCamelCase($column->getName());

                    $docBlockSet = DocBlockGenerator::fromArray(array(
                                'shortDescription' => 'Setter da coluna ' . $column->getName(),
                                'longDescription' => null,
                                'tags' => array(
                                    new Tag\ParamTag($varName, $this->prepareSqlTypeDocBlock($column->getDataType())),
                                ),
                    ));

                    $docBlockGet = DocBlockGenerator::fromArray(array(
                                'shortDescription' => 'Getter da coluna ' . $column->getName(),
                                'longDescription' => null,
                                'tags' => array(
                                    new Tag\ReturnTag(array(
                                        'datatype' => $this->prepareSqlTypeDocBlock($column->getDataType()),
                                            )),
                                ),
                    ));

                    $bodyGet = 'return $this->' . $varName . ';';
                    $bodySet = '$this->' . $varName . ' = $' . $this->camelCase($column->getName()) . ';';

                    $class->addMethod($methodSet, array($this->camelCase($column->getName())), MethodGenerator::FLAG_PUBLIC, $bodySet, $docBlockSet);
                    $class->addMethod($methodGet, array(), MethodGenerator::FLAG_PUBLIC, $bodyGet, $docBlockGet);

                    $bodyExchangeArray .= '$this->' . $varName . ' = (isset($data["' . $column->getName() . '"])) ? $data["' . $column->getName() . '"] : null;' . "\n\n";
                }

                $docBlockExchangeArray = DocBlockGenerator::fromArray(array(
                            'shortDescription' => 'Função para settar todos os objetos por meio de array',
                            'longDescription' => null,
                            'tags' => array(
                                new Tag\ParamTag('data', 'array'),
                            ),
                ));
                $class->addMethod('exchangeArray', array('data'), MethodGenerator::FLAG_PUBLIC, $bodyExchangeArray, $docBlockExchangeArray);

                $docBlockGetArrayCopy = DocBlockGenerator::fromArray(array(
                            'shortDescription' => 'Função para retornar os valores por meio de array dos objetos da classe',
                            'longDescription' => null,
                            'tags' => array(
                                new Tag\ReturnTag(array(
                                    'datatype' => 'mixed',
                                        )),
                            ),
                ));
                $class->addMethod('getArrayCopy', array(), MethodGenerator::FLAG_PUBLIC, 'return get_object_vars($this);', $docBlockGetArrayCopy);

                $classCode = "<?php" . PHP_EOL;

                $classCode .= $class->generate();

                /*
                  $idxConstraint = 0;
                  foreach ($this->oMetadata->getConstraints($tableName, $valueSchema) as $constraint) {

                  $typeConstraint = ($constraint->isPrimaryKey()) ? 'pk' : (($constraint->isForeignKey()) ? 'fk' : null);
                  if (!empty($typeConstraint)) {

                  $consName = $constraint->getName();
                  $contraintObj = $this->oMetadata->getConstraint($consName, $tableName, $valueSchema);

                  $constraintColumns = $contraintObj->getColumns();
                  if (count($constraintColumns) > 1) {
                  foreach ($constraintColumns as $valueConsColumns) {
                  $this->aDatabase[$valueSchema][$tableName][$valueConsColumns]['constraints']['type'] = $typeConstraint;
                  if ($typeConstraint === 'fk') {
                  $this->aDatabase[$valueSchema][$tableName][$valueConsColumns]['constraints']['schemaRef'] = $contraintObj->getReferencedTableSchema();
                  $this->aDatabase[$valueSchema][$tableName][$valueConsColumns]['constraints']['tableRef'] = $contraintObj->getReferencedTableName();
                  $this->aDatabase[$valueSchema][$tableName][$valueConsColumns]['constraints']['columnsRef'] = $contraintObj->getReferencedColumns();
                  }
                  }
                  } else {
                  $this->aDatabase[$valueSchema][$tableName][$constraintColumns[0]]['constraints']['type'] = $typeConstraint;
                  if ($typeConstraint === 'fk') {
                  $this->aDatabase[$valueSchema][$tableName][$constraintColumns[0]]['constraints']['schemaRef'] = $contraintObj->getReferencedTableSchema();
                  $this->aDatabase[$valueSchema][$tableName][$constraintColumns[0]]['constraints']['tableRef'] = $contraintObj->getReferencedTableName();
                  $this->aDatabase[$valueSchema][$tableName][$constraintColumns[0]]['constraints']['columnsRef'] = $contraintObj->getReferencedColumns();
                  }
                  }
                  }

                  $idxConstraint++;
                  }
                 * 
                 */
                file_put_contents($this->ormFolder . $this->toCamelCase($valueSchema) . DS . 'Entities' . DS . $this->toCamelCase($tableName) . '.php', $classCode);
                chmod($this->ormFolder . $this->toCamelCase($valueSchema) . DS . 'Entities' . DS . $this->toCamelCase($tableName) . '.php', 0644);

                $this->generatorClassEntityTable($valueSchema, $tableName);
            }


            //gera arquivo por tabela
        }
        return $this;
    }

    /**
     * Função geradora das classes tipo table das entidades
     * @param string $schemaName
     * @param string $tableName
     * 
     * @return \Cityware\Generator\Adapter\ModelAdapter
     */
    private function generatorClassEntityTable($schemaName, $tableName) {
        $template_model_table = file_get_contents(dirname(__FILE__) . DS . 'Model' . DS . 'Src_Module_Model_Table.tpl');
        $templateModelTableSchema = str_replace("%SchemaClass%", $this->toCamelCase($schemaName), str_replace("%SchemaName%", $schemaName, $template_model_table));
        $templateModelTable = str_replace("%TableClass%", $this->toCamelCase($tableName), str_replace("%TableName%", $tableName, $templateModelTableSchema));
        file_put_contents($this->ormFolder . $this->toCamelCase($schemaName) . DS . 'Tables' . DS . $this->toCamelCase($tableName) . 'Table.php', $templateModelTable);
        chmod($this->ormFolder . $this->toCamelCase($schemaName) . DS . 'Tables' . DS . $this->toCamelCase($tableName) . 'Table.php', 0644);
        
        return $this;
    }

    /**
     * Função para converter de nome para formato camelCase
     * @param string $name
     * @return string
     */
    private function toCamelCase($name) {
        return implode('', array_map('ucfirst', explode('_', $name)));
    }

    /**
     * Função para converter de nome para formato camelCase
     * @param string $str
     * @param array $exclude
     * @return string
     */
    private function camelCase($str, array $exclude = array()) {
        // non-alpha and non-numeric characters become spaces
        $str = ucwords(trim(preg_replace('/[^a-z0-9' . implode("", $exclude) . ']+/i', ' ', $str)));
        return lcfirst(str_replace(" ", "", $str));
    }

    /**
     * Função para converter nome do formato camelCase para separado por UNDERLINE
     * @param string $name
     * @return string
     */
    private function fromCamelCase($name) {
        return trim(preg_replace_callback('/([A-Z])/', function($c) {
                    return '_' . strtolower($c[1]);
                }, $name), '_');
    }

    private function prepareSqlTypeDocBlock($type) {
        switch (strtolower($type)) {
            case 'serial':
            case 'bigserial':
            case 'integer':
            case 'bigint':
            case 'int':
            case 'int4':
            case 'int8':
            case 'numeric':
                $docblockType = 'integer';
                break;
            
            case 'real':
            case 'float':
            case 'float4':
            case 'float8':
            case 'decimal':
            case 'money':
                $docblockType = 'float';
                break;

            case 'double':
                $docblockType = 'double';
                break;
            case 'bool':
            case 'boolean':
                $docblockType = 'boolean';
                break;
            
            case 'bytea':
            case 'time':
            case 'timetz':
            case 'time with time zone':
            case 'time without time zone':
            case 'date':
            case 'datetime':
            case 'timestamp':
            case 'timestamptz':
            case 'timestamp with time zone':
            case 'timestamp without time zone':
            case 'text':
            case 'json':
            case 'xml':
            case 'char':
            case 'varchar':
            case 'character':
            case 'character varying':
                $docblockType = 'string';
                break;
            default:
                $docblockType = 'mixed';
                break;
        }

        return $docblockType;
    }

    /**
     * Função que gera um array dos metadados
     */
    public function getDatabaseArray() {
        
        $aDatabase = Array();
        
        foreach ($this->oMetadata->getSchemas() as $valueSchema) {
            $aDatabase[$valueSchema] = Array();

            $tableNames = $this->oMetadata->getTableNames($valueSchema);

            foreach ($tableNames as $tableName) {
                $aDatabase[$valueSchema][$tableName] = Array();

                $table = $this->oMetadata->getTable($tableName, $valueSchema);

                $idxColumn = 0;
                foreach ($table->getColumns() as $column) {
                    $aDatabase[$valueSchema][$tableName][$column->getName()]['name'] = $column->getName();
                    $aDatabase[$valueSchema][$tableName][$column->getName()]['nameClass'] = $this->camelCase($column->getName());
                    $aDatabase[$valueSchema][$tableName][$column->getName()]['type'] = $column->getDataType();
                    $idxColumn++;
                }

                $idxConstraint = 0;
                foreach ($this->oMetadata->getConstraints($tableName, $valueSchema) as $constraint) {

                    $typeConstraint = ($constraint->isPrimaryKey()) ? 'pk' : (($constraint->isForeignKey()) ? 'fk' : null);
                    if (!empty($typeConstraint)) {

                        $consName = $constraint->getName();
                        $contraintObj = $this->oMetadata->getConstraint($consName, $tableName, $valueSchema);

                        $constraintColumns = $contraintObj->getColumns();
                        if (count($constraintColumns) > 1) {
                            foreach ($constraintColumns as $valueConsColumns) {
                                $aDatabase[$valueSchema][$tableName][$valueConsColumns]['constraints']['type'] = $typeConstraint;
                                if ($typeConstraint === 'fk') {
                                    $aDatabase[$valueSchema][$tableName][$valueConsColumns]['constraints']['schemaRef'] = $contraintObj->getReferencedTableSchema();
                                    $aDatabase[$valueSchema][$tableName][$valueConsColumns]['constraints']['tableRef'] = $contraintObj->getReferencedTableName();
                                    $aDatabase[$valueSchema][$tableName][$valueConsColumns]['constraints']['columnsRef'] = $contraintObj->getReferencedColumns();
                                }
                            }
                        } else {
                            $aDatabase[$valueSchema][$tableName][$constraintColumns[0]]['constraints']['type'] = $typeConstraint;
                            if ($typeConstraint === 'fk') {
                                $aDatabase[$valueSchema][$tableName][$constraintColumns[0]]['constraints']['schemaRef'] = $contraintObj->getReferencedTableSchema();
                                $aDatabase[$valueSchema][$tableName][$constraintColumns[0]]['constraints']['tableRef'] = $contraintObj->getReferencedTableName();
                                $aDatabase[$valueSchema][$tableName][$constraintColumns[0]]['constraints']['columnsRef'] = $contraintObj->getReferencedColumns();
                            }
                        }
                    }

                    $idxConstraint++;
                }
            }
        }

        return $aDatabase;
    }

    /**
     * Função que gera um print dos metadados
     */
    public function getPrintMetadata() {
        $metadata = new Metadata($this->getDbAdapter());

        foreach ($metadata->getSchemas() as $valueSchema) {

            echo 'In Schema ' . $valueSchema . PHP_EOL;

            // get the table names
            $tableNames = $metadata->getTableNames($valueSchema);

            foreach ($tableNames as $tableName) {
                echo 'In Table ' . $tableName . PHP_EOL;

                $table = $metadata->getTable($tableName, $valueSchema);


                echo '    With columns: ' . PHP_EOL;
                foreach ($table->getColumns() as $column) {
                    echo '        ' . $column->getName()
                    . ' -> ' . $column->getDataType()
                    . PHP_EOL;
                }

                echo PHP_EOL;
                echo '    With constraints: ' . PHP_EOL;

                foreach ($metadata->getConstraints($tableName, $valueSchema) as $constraint) {
                    /** @var $constraint Zend\Db\Metadata\Object\ConstraintObject */
                    echo '        ' . $constraint->getName()
                    . ' -> ' . $constraint->getType()
                    . PHP_EOL;
                    if (!$constraint->hasColumns()) {
                        continue;
                    }
                    echo '            column: ' . implode(', ', $constraint->getColumns());
                    if ($constraint->isForeignKey()) {
                        $fkCols = array();
                        foreach ($constraint->getReferencedColumns() as $refColumn) {
                            $fkCols[] = $constraint->getReferencedTableName() . '.' . $refColumn;
                        }
                        echo ' => ' . implode(', ', $fkCols);
                    }
                    echo PHP_EOL;
                }

                echo '----' . PHP_EOL;
            }
            echo '-------------------------------------------' . PHP_EOL;
        }
    }

}
