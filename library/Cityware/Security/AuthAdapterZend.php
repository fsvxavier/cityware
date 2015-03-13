<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cityware\Security;

use Zend\Authentication\Result as ResultValidation;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Storage\Session as SessionStorage;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Db\Sql\TableIdentifier;
use Cityware\Security;

/**
 * Description of AuthFactory
 *
 * @author fabricio.xavier
 */
class AuthAdapterZend extends AuthAdapter implements AdapterInterface
{
    private $returnColumns;

    /**
     * __construct() - Sets configuration options
     *
     * @param  string                               $tableName           Optional
     * @param  string                               $identityColumn      Optional
     * @param  string                               $credentialColumn    Optional
     * @param  string                               $credentialTreatment Optional
     * @return \Zend\Authentication\Adapter\DbTable
     */
    public function __construct($tableName = null, $schemaName = null, $identityColumn = null, $credentialColumn = null, array $returnColumns = null)
    {
        
        $this->clearIdentity();
                
        $zendDb = \Cityware\Db\Factory::factory();
        $this->zendDb = $zendDb->getAdapter();
        
        parent::__construct($this->zendDb);

        if (null !== $tableName) {
            if (null !== $schemaName) {
                $this->setTableName(new TableIdentifier($tableName, $schemaName));
            } else {
                $this->setTableName($tableName);
            }
        }

        if (null !== $identityColumn) {
            $this->setIdentityColumn($identityColumn);
        }

        if (null !== $credentialColumn) {
            $this->setCredentialColumn($credentialColumn);
        }

        $this->returnColumns = $returnColumns;
    }

    public function clearIdentity()
    {
        $auth = new AuthenticationService();
        $auth->clearIdentity();
    }

    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface
     *                                                                   If authentication cannot be performed
     */
    public function authenticate()
    {
        /* Anti Injection de login */
        $login1 = Security\AntiInjection::antiSqlInjection1($this->getIdentity());
        $login2 = Security\AntiInjection::antiSqlInjection2($login1);
        $identity = Security\AntiInjection::antiSqlInjection3($login2);

        /* Anti Injection de senha */
        $senha1 = Security\AntiInjection::antiSqlInjection1($this->getCredential());
        $senha2 = Security\AntiInjection::antiSqlInjection2($senha1);
        $senha3 = Security\AntiInjection::antiSqlInjection3($senha2);

        /* Criptografa a senha */
        $crypt = new Security\Crypt();
        $credential = $crypt->encrypt($senha3);

        //Define os dados para processar o login
        $this->setIdentity($identity)->setCredential($credential);

        //Faz inner join dos dados do perfil no SELECT do Auth_Adapter
        $select = $this->getDbSelect();
        $select->where("ind_status = 'A'");

        //Efetua o login
        $result = parent::authenticate();

        //Verifica se o login foi efetuado com sucesso
        if ($result->isValid()) {
            //Recupera o objeto do usuário, sem a senha
            $info = $this->getResultRowObject($this->returnColumns, $this->credentialColumn);

            $storage = new SessionStorage();
            $storage->write($info);

            if ($result->getCode()) {
                return new ResultValidation(ResultValidation::SUCCESS, ((array) $info));
            } else {
                return new ResultValidation(ResultValidation::FAILURE, null);
            }
        } else {
            return new ResultValidation(ResultValidation::FAILURE, null);
        }
    }

}
