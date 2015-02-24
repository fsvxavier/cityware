<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cityware\Format;

/**
 * Description of FieldGrid
 *
 * @author Fabricio
 */
final class FieldGrid
{
    /**
     * Função de mascara de valores
      * @param mixed $valor
     * @param  string $tipo
     * @param  string $formato
     * @return mixed
     */
    public static function fieldGridMask($valor, $tipo = null, $formato = null)
    {
        $data = null;
        $retorno = $valor;
        switch (strtoupper($tipo)) {
            case "DATE": {
                    if (preg_match("/^([0-9]{4})[-,\/]([0-9]{1,2})[-,\/]([0-9]{1,2})/", $valor)) {
                        $retorno = date($formato, strtotime($valor)) . substr($valor, (strrpos($valor, " ") !== false ? strrpos($valor, " ") : 10), 9); //substr($valor, 10);
                    } elseif (preg_match("/^([0-9]{1,2})[-,\/]([0-9]{1,2})[-,\/]([0-9]{4})/", $valor, $data)) {
                        $retorno = date($formato, strtotime($data[3] . '-' . $data[2] . '-' . $data[1])) . substr($valor, (strrpos($valor, " ") !== false ? strrpos($valor, " ") : 10), 9); //substr($valor, 10);
                    }
                    break;
                }
            case "STATUS": {
                    $arrayMap = Array('I' => 'Excluido', 'B' => 'Bloqueado', 'A' => 'Ativo', 'L' => 'Lixeira');
                    $retorno = $arrayMap[$valor];
                    break;
                }
            case "MOEDA": {
                    if (is_numeric($valor)) {
                        $retorno = number_format($valor, 2, ',', '.');
                    }
                    break;
                }
            case "FONE": {
                    $num = preg_replace('/[^0-9]/', '', $valor);
                    $len = strlen($valor);
                    if ($len == 10) {
                        $retorno = preg_replace('/([0-9]{2})([0-9]{4})([0-9]{4})/', '($1) $2-$3', $num);
                    }
                    if ($len == 11) {
                        $retorno = preg_replace('/([0-9]{2})([0-9]{5})([0-9]{4})/', '($1) $2-$3', $num);
                    }
                    break;
                }
            case "CEP": {
                    $num = preg_replace('/[^0-9]/', '', $valor);
                    $len = strlen($valor);
                    if ($len == 8) {
                        $retorno = preg_replace('/([0-9]{2})([0-9]{3})([0-9]{3})/', '$1.$2-$3', $num);
                    }
                    break;
                }
            case "CPF": {
                    $num = preg_replace('/[^0-9]/', '', $valor);
                    $len = strlen($num);
                    if ($len == 11) {
                        $retorno = preg_replace('/([0-9]{3})([0-9]{3})([0-9]{3})([0-9]{2})/', '$1.$2.$3-$4', $num);
                    }
                    break;
                }
            case "CNPJ": {
                    $num = preg_replace('/[^0-9]/', '', $valor);
                    $len = strlen($num);
                    if ($len == 14) {
                        $retorno = preg_replace('/([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{2})/', '$1.$2.$3/$4-$5', $num);
                    }
                    break;
                }
        }

        return $retorno;
    }

    /**
     * Função de mascara de valores
     * @param  mixed  $valor
     * @param  string $tipo
     * @param  string $formato
     * @return mixed
     */
    public static function fieldMask($valor, $tipo = null, $formato = null)
    {
        $data = null;
        $retorno = $valor;
        switch (strtoupper($tipo)) {
            case "DATE": {
                    if (preg_match("/^([0-9]{4})[-,\/]([0-9]{1,2})[-,\/]([0-9]{1,2})/", $valor)) {
                        $retorno = date($formato, strtotime($valor));
                    } elseif (preg_match("/^([0-9]{1,2})[-,\/]([0-9]{1,2})[-,\/]([0-9]{4})/", $valor, $data)) {
                        $retorno = date($formato, strtotime($data[3] . '-' . $data[2] . '-' . $data[1]));
                    }
                    break;
                }
            case "DATETIME": {
                    if (preg_match("/^([0-9]{4})[-,\/]([0-9]{1,2})[-,\/]([0-9]{1,2})/", $valor)) {
                        $retorno = date($formato, strtotime($valor)) . substr($valor, (strrpos($valor, " ") !== false ? strrpos($valor, " ") : 10), 9); //substr($valor, 10);
                    } elseif (preg_match("/^([0-9]{1,2})[-,\/]([0-9]{1,2})[-,\/]([0-9]{4})/", $valor, $data)) {
                        $retorno = date($formato, strtotime($data[3] . '-' . $data[2] . '-' . $data[1])) . substr($valor, (strrpos($valor, " ") !== false ? strrpos($valor, " ") : 10), 9); //substr($valor, 10);
                    }
                    break;
                }
            case "TIME": {
                    $retorno = substr($valor, (strrpos($valor, " ") !== false ? strrpos($valor, " ") : 10), 9); //substr($valor, 10);
                    break;
                }
            case "DATA_HORA":
            case "DATA": {
                    if (preg_match("/^([0-9]{4})[-,\/]([0-9]{1,2})[-,\/]([0-9]{1,2})/", $valor)) {
                        $retorno = date($formato, strtotime($valor)) . substr($valor, (strrpos($valor, " ") !== false ? strrpos($valor, " ") : 10), 5); //substr($valor, 10);
                    } elseif (preg_match("/^([0-9]{1,2})[-,\/]([0-9]{1,2})[-,\/]([0-9]{4})/", $valor, $data)) {
                        $retorno = date($formato, strtotime($data[3] . '-' . $data[2] . '-' . $data[1])) . substr($valor, (strrpos($valor, " ") !== false ? strrpos($valor, " ") : 10), 5); //substr($valor, 10);
                    }
                    break;
                }
            case "MOEDA": {
                    if (is_numeric($valor)) {
                        $retorno = number_format($valor, 2, ',', '.');
                    }
                    break;
                }
            case "STATUS": {
                    $arrayMap = Array('I' => 'Excluido', 'L' => 'Lixeira', 'B' => 'Bloqueado', 'A' => 'Ativo');
                    $retorno = $arrayMap[$valor];
                    break;
                }
        }

        return $retorno;
    }

}
