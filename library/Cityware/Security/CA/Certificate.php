<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cityware\Security\CA;

/**
 * Description of Certificate
 *
 * @author fabricio.xavier
 */
class Certificate {

    private $countryName;
    private $stateOrProvinceName;
    private $localityName;
    private $organizationName;
    private $organizationalUnitName;
    private $commonName;
    private $emailAddress;
    private $keyBits = 2048;
    private $keyType = OPENSSL_KEYTYPE_RSA;
    private $digestAlg = 'sha512';
    private $privateKey;
    private $publicKey;
    private $csrkey;
    private $pathFile;
    private $privateKeyFileName;
    private $publicKeyFileName;
    private $licenseFileName;
    private $csrKeyFilename;

    public function getKeyBits() {
        return $this->keyBits;
    }

    public function getKeyType() {
        return $this->keyType;
    }

    public function getDigestAlg() {
        return $this->digestAlg;
    }

    public function getPathFile() {
        return $this->pathFile;
    }

    public function getPrivateKeyFileName() {
        return $this->privateKeyFileName;
    }

    public function getPublicKeyFileName() {
        return $this->publicKeyFileName;
    }

    public function getCsrKeyFilename() {
        return $this->csrKeyFilename;
    }

    public function setKeyBits($keyBits) {
        $this->keyBits = $keyBits;
        return $this;
    }

    public function setKeyType($keyType) {
        $this->keyType = $keyType;
        return $this;
    }

    public function setDigestAlg($digestAlg) {
        $this->digestAlg = $digestAlg;
        return $this;
    }

    public function setPathFile($pathFile) {
        $this->pathFile = $pathFile;
        return $this;
    }

    public function setPrivateKeyFileName($privateKeyFileName) {
        $this->privateKeyFileName = $privateKeyFileName;
        return $this;
    }

    public function setPublicKeyFileName($publicKeyFileName) {
        $this->publicKeyFileName = $publicKeyFileName;
        return $this;
    }

    public function setCsrKeyFilename($csrKeyFilename) {
        $this->csrKeyFilename = $csrKeyFilename;
        return $this;
    }
    
    function getLicenseFileName() {
        return $this->licenseFileName;
    }

    function setLicenseFileName($licenseFileName) {
        $this->licenseFileName = $licenseFileName;
        return $this;
    }

    
    /* DN Definitions */

    public function setCountryName($countryName) {
        $this->countryName = $countryName;
        return $this;
    }

    public function setStateOrProvinceName($stateOrProvinceName) {
        $this->stateOrProvinceName = $stateOrProvinceName;
        return $this;
    }

    public function setLocalityName($localityName) {
        $this->localityName = $localityName;
        return $this;
    }

    public function setOrganizationName($organizationName) {
        $this->organizationName = $organizationName;
        return $this;
    }

    public function setOrganizationalUnitName($organizationalUnitName) {
        $this->organizationalUnitName = $organizationalUnitName;
        return $this;
    }

    public function setCommonName($commonName) {
        $this->commonName = $commonName;
        return $this;
    }

    public function setEmailAddress($emailAddress) {
        $this->emailAddress = $emailAddress;
        return $this;
    }

    public function generatorFiles(array $dn = null, $days = 365, $domain = null) {

        if (empty($dn)) {
            $dn = Array();
        }

        if (empty($this->pathFile)) {
            $this->pathFile = SSL_PATH;
        }

        if (empty($this->privateKeyFileName)) {
            $this->privateKeyFileName = $this->pathFile . 'private.pem';
        }

        if (empty($this->publicKeyFileName)) {
            $this->publicKeyFileName = $this->pathFile . 'public.pem';
        }

        if (empty($this->csrKeyFilename)) {
            $this->csrKeyFilename = $this->pathFile . 'csr.key';
        }
        
        if (empty($this->licenseFileName)) {
            $this->licenseFileName = $this->pathFile . 'license.sig';
        }

        if ($this->validateDn($dn)) {

            $this->privateKey = openssl_pkey_new();

            $config = array(
                "digest_alg" => $this->digestAlg,
                "private_key_bits" => $this->keyBits,
                "private_key_type" => $this->keyType
            );

            // Generate a certificate signing request
            $this->csrkey = openssl_csr_new($dn, $this->privateKey, $config);

            // You will usually want to create a self-signed certificate at this
            // point until your CA fulfills your request.
            // This creates a self-signed cert that is valid for 365 days
            $this->publicKey = openssl_csr_sign($this->csrkey, null, $this->privateKey, $days);

            $this->genCsrCertFile();
            $this->genPrivateKeyFile();
            $this->genPublicKeyFile();
            $this->genLicenseFile($domain);
        }
    }

    private function validateDn(array $dn) {
        $dnModel = Array(
            "countryName" => "Nome do país",
            "stateOrProvinceName" => "Nome do Estado ou provincia",
            "localityName" => "Nome da Cidade",
            "organizationName" => "nome da Empresa",
            "organizationalUnitName" => "Unidade Organizacional da Empresa. Ex: Matriz",
            "commonName" => "Dominio da empresa. Ex:. cityware.com.br",
            "emailAddress" => "E-mail"
        );

        $compared = array_diff_key($dnModel, $dn);

        if (!empty($compared)) {
            foreach ($compared as $key => $value) {
                if (empty($this->{$key})) {
                    throw new \Exception("O Domain Name Data não está completo favor preencher : '" . $value . "'", 500);
                }
            }
        } else {
            return true;
        }
    }

    private function genCsrCertFile() {
        openssl_csr_export_to_file($this->csrkey, $this->csrKeyFilename);
    }

    private function genPrivateKeyFile() {
        openssl_pkey_export_to_file($this->privateKey, $this->privateKeyFileName);
    }

    private function genPublicKeyFile() {
        openssl_x509_export_to_file($this->publicKey, $this->publicKeyFileName);
    }
    
    private function genLicenseFile($domain = null) {
        
        $sourceArrayCert = openssl_x509_parse($this->publicKey);
        $sourceArray = Array(
            'domain' => ((!empty($domain)) ? $domain : $sourceArrayCert['subject']['CN']),
            'expire' => $sourceArrayCert['validTo_time_t'],
            'city' => $sourceArrayCert['subject']['L'],
            'state' => $sourceArrayCert['subject']['ST'],
            'country' => $sourceArrayCert['subject']['C']
        );
        $sourceJson = json_encode($sourceArray);
        
        try {
            $encripted = null;
            openssl_private_encrypt($sourceJson, $encripted, $this->privateKey);
        } catch (\Exception $exc) {
            echo $exc->getMessage();
        }

        file_put_contents($this->licenseFileName, base64_encode($encripted));
    }
    
    public function getLicenseData($publicFilePem = null, $publicFileSig = null) {
        if (empty($publicFilePem)) {
            throw new \Exception('Nenhuma chave publica definida!', 500);
        }

        if (!is_file($publicFilePem)) {
            throw new \Exception('Arquivo de chave publica não existe!', 500);
        }
        
        if (empty($publicFileSig)) {
            throw new \Exception('Nenhum arquivo de licença definido!', 500);
        }
        
        if (!is_file($publicFileSig)) {
            throw new \Exception('Arquivo de licença não existe!', 500);
        }
        
        $license = file_get_contents($publicFileSig);
        
        $publicKey = file_get_contents($publicFilePem);
        $pubKey = openssl_pkey_get_public($publicKey);
        
        $decripted = '';
        openssl_public_decrypt(base64_decode($license), $decripted, $pubKey);
        
        
        return json_decode($decripted, true);
    }

    public function genCertPlataforma($days, $domain = null) {

        $dn = array(
            "countryName" => "BR",
            "stateOrProvinceName" => "Minas Gerais",
            "localityName" => "Uberlandia",
            "organizationName" => "Cityware",
            "organizationalUnitName" => "Matriz",
            "commonName" => "cityware.com.br",
            "emailAddress" => "hostmaster@cityware.com.br"
        );

        $moduleName = 'Cityware';

        $filePrivateKey = SSL_PATH . 'plataforma' . DS . strtolower($moduleName) . '.private.pem';
        $filePublicKey = SSL_PATH . 'plataforma' . DS . strtolower($moduleName) . '.public.pem';
        $fileCsrKey = SSL_PATH . 'plataforma' . DS . strtolower($moduleName) . '.csr.key';
        $fileLicenseSig = SSL_PATH . 'plataforma' . DS . strtolower($moduleName) . '.license.sig';

        try {
            $this->setPrivateKeyFileName($filePrivateKey);
            $this->setPublicKeyFileName($filePublicKey);
            $this->setCsrKeyFilename($fileCsrKey);
            $this->setLicenseFileName($fileLicenseSig);
            $this->generatorFiles($dn, $days, $domain);
        } catch (\Exception $exc) {
            throw new \Exception('Erro ao criar os certificados' . $exc->getMessage(), 500);
        }
    }

    /**
     * Retorna as informações de uma chave publica
     * @param string $publicFilePem
     * @return array
     * @throws \Exception
     */
    public function parsePublicKey($publicFilePem = null) {

        if (empty($publicFilePem)) {
            throw new \Exception('Nenhuma chave publica definida!', 500);
        }

        if (!is_file($publicFilePem)) {
            throw new \Exception('Arquivo de chave publica não existe!', 500);
        }

        $cert = file_get_contents($publicFilePem);
        return openssl_x509_parse($cert);
    }

    /**
     * Retorna se o certificado está expirado
     * @param string $publicFilePem
     * @return boolean
     */
    public function isExpiredCert($publicFilePem = null) {

        $timestamp = strtotime(date('Y-m-d H:i:s'));

        $data = $this->parsePublicKey($publicFilePem);
        $diff = $data['validTo_time_t'] - $timestamp;
                
        if($diff < 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function getDaysToExpire($publicFilePem = null) {
        $timestamp = strtotime(date('Y-m-d H:i:s'));

        $data = $this->parsePublicKey($publicFilePem);
        $diferenca = $data['validTo_time_t'] - $timestamp;
        
        return ((int)floor( $diferenca / (60 * 60 * 24))); 
    }

    /**
     * Retorna a data e hora de expiração do certificado com base na chave pública
     * @param string $publicFilePem
     * @return string
     */
    public function getExpirationDate($publicFilePem = null) {
        $data = $this->parsePublicKey($publicFilePem);
        return date('Y-m-d H:i:s', $data['validTo_time_t']);
    }

    /**
     * Retorna a data e hora de criação do certificado com base na chave pública
     * @param string $publicFilePem
     * @return string
     */
    public function getCreateDate($publicFilePem = null) {
        $data = $this->parsePublicKey($publicFilePem);
        return date('Y-m-d H:i:s', $data['validFrom_time_t']);
    }
    
    /**
     * Compara o certificado de Chave Publica com a Chave Privada
     * @param string $publicCertFilePem
     * @param string $privateCertFileKey
     * @return boolean
     * @throws \Exception
     */
    public function verifyCertificate($publicCertFilePem = null, $privateCertFileKey = null) {
        if (empty($publicCertFilePem)) {
            throw new \Exception('Nenhuma chave publica definida!', 500);
        }

        if (!is_file($publicCertFilePem)) {
            throw new \Exception('Arquivo de chave publica não existe!', 500);
        }
        
        if (empty($privateCertFileKey)) {
            throw new \Exception('Nenhuma chave privada definida!', 500);
        }

        if (!is_file($privateCertFileKey)) {
            throw new \Exception('Arquivo de chave privada não existe!', 500);
        }
                
        $key = openssl_pkey_get_private(file_get_contents($privateCertFileKey));
        $x509_res = openssl_x509_read(file_get_contents($publicCertFilePem));
        return openssl_x509_check_private_key($x509_res, $key);
    }
    
    public function compareKeys($pubkey = null, $privateCertFileKey = null) {
        if (empty($pubkey)) {
            throw new \Exception('Nenhuma chave publica definida!', 500);
        }

        if (!is_file($pubkey)) {
            throw new \Exception('Arquivo de chave publica não existe!', 500);
        }
        
        if (empty($privateCertFileKey)) {
            throw new \Exception('Nenhuma chave privada definida!', 500);
        }

        if (!is_file($privateCertFileKey)) {
            throw new \Exception('Arquivo de chave privada não existe!', 500);
        }
        
        $key = openssl_pkey_get_private(file_get_contents($privateCertFileKey));
        $details = openssl_pkey_get_details($key);
        
        
        $sPubKey = file_get_contents($pubkey);
        $sPrivKey = $details['key'];
                
        if(strcmp($sPubKey, $sPrivKey) == 0){
            return true;
        } else {
            return false;
        }
    }

}
