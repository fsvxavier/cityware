<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cityware\Security;

use Traversable;
use Zend\Stdlib\ArrayUtils;

/**
 * Description of Crypt
 *
 * @author fabricio.xavier
 */

/**
 * Symmetric encryption using the Mcrypt extension
 */
class Crypt {

    const DEFAULT_PADDING = 'pkcs7';

    /**
     * Key
     *
     * @var string
     */
    protected $key;

    /**
     * IV
     *
     * @var string
     */
    protected $iv;

    /**
     * Encryption algorithm
     *
     * @var string
     */
    protected $algo = 'rijndael-256';

    /**
     * Encryption mode
     *
     * @var string
     */
    protected $mode = 'ecb';

    /**
     * Padding
     *
     * @var Padding\PaddingInterface
     */
    protected $padding;

    /**
     * Padding plugins
     *
     * @var PaddingPluginManager
     */
    protected static $paddingPlugins = null;

    /**
     * Supported cipher algorithms
     *
     * @var array
     */
    protected $supportedAlgos = array(
        'aes' => 'rijndael-128',
        'blowfish' => 'blowfish',
        'des' => 'des',
        '3des' => 'tripledes',
        'tripledes' => 'tripledes',
        'cast-128' => 'cast-128',
        'cast-256' => 'cast-256',
        'rijndael-128' => 'rijndael-128',
        'rijndael-192' => 'rijndael-192',
        'rijndael-256' => 'rijndael-256',
        'saferplus' => 'saferplus',
        'serpent' => 'serpent',
        'twofish' => 'twofish'
    );

    /**
     * Supported encryption modes
     *
     * @var array
     */
    protected $supportedModes = array(
        'ecb' => 'ecb',
        'cbc' => 'cbc',
        'cfb' => 'cfb',
        'ctr' => 'ctr',
        'ofb' => 'ofb',
        'nofb' => 'nofb',
        'ncfb' => 'ncfb'
    );

    /**
     * Constructor
     *
     * @param  array|Traversable $options
     * @throws Exception\RuntimeException
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($options = array()) {

        $this->setKey(hash("SHA512", pack('N', "bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3"), true));

        if (!extension_loaded('mcrypt')) {
            throw new \Cityware\Exception\RuntimeException(
            'You cannot use ' . __CLASS__ . ' without the Mcrypt extension'
            );
        }
        if (!empty($options)) {
            if ($options instanceof Traversable) {
                $options = ArrayUtils::iteratorToArray($options);
            } elseif (!is_array($options)) {
                throw new \Cityware\Exception\InvalidArgumentException(
                'The options parameter must be an array, a Zend\Config\Config object or a Traversable'
                );
            }
            foreach ($options as $key => $value) {
                switch (strtolower($key)) {
                    case 'algo':
                    case 'algorithm':
                        $this->setAlgorithm($value);
                        break;
                    case 'mode':
                        $this->setMode($value);
                        break;
                    case 'key':
                        $this->setKey($value);
                        break;
                    case 'iv':
                    case 'salt':
                        $this->setSalt($value);
                        break;
                    case 'padding':
                        $plugins = static::getPaddingPluginManager();
                        $padding = $plugins->get($value);
                        $this->padding = $padding;
                        break;
                }
            }
        }
        $this->setDefaultOptions($options);
    }

    /**
     * Set default options
     *
     * @param  array $options
     * @return void
     */
    protected function setDefaultOptions($options = array()) {
        if (!isset($options['padding'])) {
            $plugins = static::getPaddingPluginManager();
            $padding = $plugins->get(self::DEFAULT_PADDING);
            $this->padding = $padding;
        }
    }

    /**
     * Returns the padding plugin manager.  If it doesn't exist it's created.
     *
     * @return PaddingPluginManager
     */
    public static function getPaddingPluginManager() {
        if (static::$paddingPlugins === null) {
            self::setPaddingPluginManager(new \Zend\Crypt\Symmetric\PaddingPluginManager());
        }

        return static::$paddingPlugins;
    }

    /**
     * Set the padding plugin manager
     *
     * @param  string|PaddingPluginManager        $plugins
     * @throws Exception\InvalidArgumentException
     * @return void
     */
    public static function setPaddingPluginManager($plugins) {
        if (is_string($plugins)) {
            if (!class_exists($plugins)) {
                throw new \Cityware\Exception\InvalidArgumentException(sprintf(
                        'Unable to locate padding plugin manager via class "%s"; class does not exist', $plugins
                ));
            }
            $plugins = new $plugins();
        }
        if (!$plugins instanceof \Zend\Crypt\Symmetric\PaddingPluginManager) {
            throw new \Cityware\Exception\InvalidArgumentException(sprintf(
                    'Padding plugins must extend %s\PaddingPluginManager; received "%s"', __NAMESPACE__, (is_object($plugins) ? get_class($plugins) : gettype($plugins))
            ));
        }
        static::$paddingPlugins = $plugins;
    }

    /**
     * Get the maximum key size for the selected cipher and mode of operation
     *
     * @return int
     */
    public function getKeySize() {
        return mcrypt_get_key_size($this->supportedAlgos[$this->algo], $this->supportedModes[$this->mode]);
    }

    /**
     * Set the encryption key
     * If the key is longer than maximum supported, it will be truncated by getKey().
     *
     * @param  string                             $key
     * @throws Exception\InvalidArgumentException
     * @return Mcrypt
     */
    public function setKey($key) {

        $cipherKey = substr($key, 0, $this->getKeySize());

        $keyLen = strlen($cipherKey);

        if (!$keyLen) {
            throw new \Cityware\Exception\InvalidArgumentException('The key cannot be empty');
        }
        $keySizes = mcrypt_module_get_supported_key_sizes($this->supportedAlgos[$this->algo]);
        $maxKey = $this->getKeySize();

        /*
         * blowfish has $keySizes empty, meaning it can have arbitrary key length.
         * the others are more picky.
         */
        if (!empty($keySizes) && $keyLen < $maxKey) {

            if (!in_array($keyLen, $keySizes)) {
                throw new \Cityware\Exception\InvalidArgumentException(
                "The size of the key must be one of " . implode(", ", $keySizes) . " bytes or longer"
                );
            }
        }
        $this->key = $cipherKey;

        return $this;
    }

    /**
     * Get the encryption key
     *
     * @return string
     */
    public function getKey() {
        if (empty($this->key)) {
            return null;
        }
        return substr($this->key, 0, $this->getKeySize());
    }

    /**
     * Set the encryption algorithm (cipher) supported:
     * 
     * 'aes'
     * 'blowfish'
     * 'des'
     * '3des'
     * 'tripledes'
     * 'cast-128'
     * 'cast-256'
     * 'rijndael-128'
     * 'rijndael-192'
     * 'rijndael-256'
     * 'saferplus'
     * 'serpent'
     * 'twofish'
     *
     * @param  string                             $algo
     * @throws Exception\InvalidArgumentException
     * @return Mcrypt
     */
    public function setAlgorithm($algo) {
        if (!array_key_exists($algo, $this->supportedAlgos)) {
            throw new \Cityware\Exception\InvalidArgumentException(
            "The algorithm $algo is not supported by " . __CLASS__
            );
        }
        $this->algo = $algo;

        return $this;
    }

    /**
     * Get the encryption algorithm
     *
     * @return string
     */
    public function getAlgorithm() {
        return $this->algo;
    }

    /**
     * Set the padding object
     *
     * @param  Padding\PaddingInterface $padding
     * @return Mcrypt
     */
    public function setPadding(\Zend\Crypt\Symmetric\Padding\PaddingInterface $padding) {
        $this->padding = $padding;

        return $this;
    }

    /**
     * Get the padding object
     *
     * @return Padding\PaddingInterface
     */
    public function getPadding() {
        return $this->padding;
    }

    /**
     * Encrypt
     *
     * @param  string $data
     * @throws Exception\InvalidArgumentException
     * @return string
     */
    public function encrypt($data) {
        // Cannot encrypt empty string
        if (!is_string($data) || $data === '') {
            throw new \Cityware\Exception\InvalidArgumentException('The data to encrypt cannot be empty');
        }
        if (null === $this->getKey()) {
            throw new \Cityware\Exception\InvalidArgumentException('No key specified for the encryption');
        }
        if (null === $this->getSalt()) {
            throw new \Cityware\Exception\InvalidArgumentException('The salt (IV) cannot be empty');
        }
        if (null === $this->getPadding()) {
            throw new \Cityware\Exception\InvalidArgumentException('You have to specify a padding method');
        }
        // padding
        $dataCipher = $this->padding->pad($data, $this->getBlockSize());
        $iv = $this->getSalt();
        // encryption
        $result = mcrypt_encrypt($this->supportedAlgos[$this->algo], $this->getKey(), $dataCipher, $this->supportedModes[$this->mode], $iv);

        return base64_encode($this->getKey() . $result);
    }

    /**
     * Decrypt
     *
     * @param  string $data
     * @throws Exception\InvalidArgumentException
     * @return string
     */
    public function decrypt($data) {
        if (empty($data)) {
            throw new \Cityware\Exception\InvalidArgumentException('The data to decrypt cannot be empty');
        } else {
            $cipherData = base64_decode($data);
        }
        if (null === $this->getKey()) {
            throw new \Cityware\Exception\InvalidArgumentException('No key specified for the decryption');
        }
        if (null === $this->getPadding()) {
            throw new \Cityware\Exception\InvalidArgumentException('You have to specify a padding method');
        }
        $ciphertext = substr($cipherData, $this->getKeySize());
        $result = mcrypt_decrypt($this->supportedAlgos[$this->algo], $this->getKey(), $ciphertext, $this->supportedModes[$this->mode], $this->getSalt());
        // unpadding
        return $this->padding->strip($result);
    }

    /**
     * Get the original salt value
     * @param string $data
     * @return string
     * @throws \Cityware\Exception\InvalidArgumentException
     */
    public function getCipherDataSalt($data) {
        if (empty($data)) {
            throw new \Cityware\Exception\InvalidArgumentException('The data to decrypt cannot be empty');
        } else {
            $cipherData = base64_decode($data);
        }
        return substr($cipherData, 0, $this->getSaltSize());
    }

    /**
     * Get the salt (IV) size
     *
     * @return int
     */
    public function getSaltSize() {
        return mcrypt_get_iv_size($this->supportedAlgos[$this->algo], $this->supportedModes[$this->mode]);
    }

    /**
     * Get the supported algorithms
     *
     * @return array
     */
    public function getSupportedAlgorithms() {
        return array_keys($this->supportedAlgos);
    }

    /**
     * Set the salt (IV)
     *
     * @param  string                             $salt
     * @throws Exception\InvalidArgumentException
     * @return Mcrypt
     */
    public function setSalt($salt) {
        if (empty($salt)) {
            throw new \Cityware\Exception\InvalidArgumentException('The salt (IV) cannot be empty');
        }
        if (strlen($salt) < $this->getSaltSize()) {
            throw new \Cityware\Exception\InvalidArgumentException('The size of the salt (IV) must be at least ' . $this->getSaltSize() . ' bytes');
        }
        $this->iv = $salt;

        return $this;
    }

    /**
     * Get the salt (IV) according to the size requested by the algorithm
     *
     * @return string
     */
    public function getSalt() {
        if (empty($this->iv)) {
            return mcrypt_create_iv(mcrypt_get_iv_size($this->algo, $this->mode), MCRYPT_RAND);
        }
        if (strlen($this->iv) < $this->getSaltSize()) {
            throw new \Cityware\Exception\RuntimeException(
            'The size of the salt (IV) must be at least ' . $this->getSaltSize() . ' bytes'
            );
        }

        return substr($this->iv, 0, $this->getSaltSize());
    }

    /**
     * Get the original salt value
     *
     * @return string
     */
    public function getOriginalSalt() {
        return $this->iv;
    }

    /**
     * Set the cipher mode
     *
     * @param  string                             $mode
     * @throws Exception\InvalidArgumentException
     * @return Mcrypt
     */
    public function setMode($mode) {
        if (!empty($mode)) {
            $mode = strtolower($mode);
            if (!array_key_exists($mode, $this->supportedModes)) {
                throw new \Cityware\Exception\InvalidArgumentException(
                "The mode $mode is not supported by " . __CLASS__
                );
            }
            $this->mode = $mode;
        }

        return $this;
    }

    /**
     * Get the cipher mode
     *
     * @return string
     */
    public function getMode() {
        return $this->mode;
    }

    /**
     * Get all supported encryption modes
     *
     * @return array
     */
    public function getSupportedModes() {
        return array_keys($this->supportedModes);
    }

    /**
     * Get the block size
     *
     * @return int
     */
    public function getBlockSize() {
        return mcrypt_get_block_size($this->supportedAlgos[$this->algo], $this->supportedModes[$this->mode]);
    }

}
