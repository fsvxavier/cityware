<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cityware\Format;

use DOMDocument;

/**
 * Description of Xml
 *
 * @author Fabricio
 */
class Xml {

    /**
     * Formatação de XML com base em DOMDocument
     * @param string $xml
     * @return string
     */
    public static function format($xml) {
        $domtree = new DOMDocument('1.0', 'UTF-8');
        $domtree->loadXML($xml);
        $domtree->preserveWhiteSpace = false;
        $domtree->formatOutput = true;
        $domtree->setIndent = (1);
        return $domtree->saveXML();
    }

    /**
     * 
     * @param string $sourceXml
     * @param string $sourceXsd
     * @return boolean
     */
    public function xsdValidateBySource($sourceXml, $sourceXsd) {
        
        libxml_use_internal_errors(true);
        
        $domDoc = new DOMDocument();
        $domDoc->loadXML($sourceXml);

        if (!$domDoc->schemaValidateSource($sourceXsd)) {
            print $this->libXmlDisplayErrors();
        } else {
            return true;
        }
    }
    
    /**
     * 
     * @param string $fileXml
     * @param string $fileXsd
     * @return boolean
     */
    public function xsdValidateByFile($fileXml, $fileXsd) {
        
        libxml_use_internal_errors(true);
        
        $domDoc = new DOMDocument();
        $domDoc->load($fileXml);

        if (!$domDoc->schemaValidate($fileXsd)) {
            print $this->libXmlDisplayErrors();
        } else {
            return true;
        }
    }

    /**
     * 
     * @param object $error
     * @return string
     */
    private function libXmlDisplayError($error) {
        $return = "<br/>\n";
        switch ($error->level) {
            case LIBXML_ERR_WARNING:
                $return .= "<b>Warning $error->code</b>: ";
                break;
            case LIBXML_ERR_ERROR:
                $return .= "<b>Error $error->code</b>: ";
                break;
            case LIBXML_ERR_FATAL:
                $return .= "<b>Fatal Error $error->code</b>: ";
                break;
        }
        $return .= trim($error->message);
        if ($error->file) {
            $return .= " in <b>$error->file</b>";
        }
        $return .= " on line <b>$error->line</b>\n";

        return $return;
    }

    /**
     * 
     * @return string
     */
    private function libXmlDisplayErrors() {
        $errors = libxml_get_errors();
        $return = "'<b>Erro encontrado na comparação do XML com XSD!</b>'<br/>\n";
        foreach ($errors as $error) {
            $return .= $this->libXmlDisplayError($error);
        }
        libxml_clear_errors();
        return $return;
    }

}
