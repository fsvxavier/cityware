<?php

namespace Orm\%SchemaClass%\Tables;
 
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\TableIdentifier;
use Orm\%SchemaClass%\Entities\%TableClass%;
 
class %TableClass%Table
{
    protected $tableGateway;
 
    public function __construct(Adapter $adapter)
    {
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new %TableClass%());
        
        $tableIdentifier = new TableIdentifier(%TableClass%::DBTABLE, %TableClass%::DBSCHEMA);
 
        $this->tableGateway = new TableGateway($tableIdentifier, $adapter, null, $resultSetPrototype);
    }
    
    /**
     * Recuperar todos os elementos da tabela
     *
     * @return ResultSet
     */
    public function fetchAll()
    {
        return $this->tableGateway->select()->toArray();
    }
 
    public function fetchAllPaginator()
    {
        $resultSet = $this->tableGateway->select();
        $resultSet->buffer();
        $resultSet->next();
        return $resultSet;
    }
    
    /**
     * Localizar linha especifico pelo id da tabela
     *
     * @param type $id
     * @return \Model\Contato
     * @throws \Exception
     */
    public function find($id)
    {
        if (!%TableClass%::MULTIPK) {
            $id = (int) $id;
            $rowset = $this->tableGateway->select(array(%TableClass%::PKCOLUMN => $id));
            $row = $rowset->current();
            if (!$row) {
                throw new \Exception("Não foi encontrado registro com id = {$id}");
            }
            return $row;
        } else {
            throw new \Exception("Não foi realizar a busca pois é uma tabela de relacionamento com multiplos Primary Keys");
        }
    }
    
    
    /**
     * Apagar uma linha especificada pelo id da tabela 
     * @param integer $id
     * @return boolean
     * @throws \Exception
     */
    public function delete($id) {
        if (!%TableClass%::MULTIPK) {
            $id = (int) $id;
            $rowset = $this->tableGateway->delete(array(%TableClass%::PKCOLUMN => $id));
            if (!$rowset) {
                throw new \Exception("Não foi possivel apagar o registro com id = {$id}");
            }
            return true;
        } else {
            throw new \Exception("Não foi realizar a busca pois é uma tabela de relacionamento com multiplos Primary Keys");
        }
    }
}