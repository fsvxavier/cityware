<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cityware\Format;

/**
 * Description of Money
 *
 * @author fabricio.xavier
 */
final class Money {

    /**
     * Retorna o número de parcelas
     * @param  float   $total
     * @param  integer $maxParcelas
     * @param  float   $valor_minimo
     * @return integer
     */
    public static function getNumParcelas($total, $maxParcelas, $valor_minimo) {
        $nParcelas = $maxParcelas;

        //verifica o valor minimo permitido para cada parcela
        if (!empty($valor_minimo) && is_numeric($valor_minimo)) {
            $parcPossiveis = floor($total / $valor_minimo);

            if ($parcPossiveis < $nParcelas) {
                $nParcelas = $parcPossiveis;
            }
        }

        return $nParcelas;
    }

    /**
     * Calculo de Parcelamento
     * @param  float   $capital
     * @param  float   $taxa
     * @param  integer $tempo
     * @param  integer $semjuros
     * @return type
     */
    public static function calcularParcelamento($capital, $taxa, $tempo, $semjuros) {
        $parcelamento = array();
        for ($i = 1; $i <= $tempo; $i++) {
            $m = $capital * pow((1 + $taxa / 100), $i);
            if ($i <= $semjuros) {
                $parcelamento[$i] = array(
                    'parcela' => self::formataValor(($capital / $i), '.', 2, ',', '.'),
                    'total' => self::formataValor($capital, '.', 2, ',', '.'),
                    'status' => 'sem juros'
                );
            } else {
                $parcelamento[$i] = array(
                    'parcela' => self::formataValor(($m / $i), '.', 2, ',', '.'),
                    'total' => self::formataValor($m, '.', 2, ',', '.'),
                    'status' => 'com juros'
                );
            }
        }

        return $parcelamento;
    }

    /**
     * Retorna o valor da parcela
     * @param  float   $total
     * @param  integer $parcela
     * @param  float   $taxa
     * @return float
     */
    public static function getValorParcela($total, $parcela, $taxa) {
        if (!is_numeric($total) || $total <= 0) {
            return (false);
        }
        if ((int) $parcela != $parcela) {
            return (false);
        }
        if (!is_numeric($taxa) || $taxa < 0) {
            return (false);
        }

        $taxa = $taxa / 100;

        $denominador = 0;
        if ($parcela > 1) {
            for ($i = 1; $i <= $parcela; $i++) {
                $denominador += 1 / pow(1 + $taxa, $i);
            }
        } else {
            $denominador = 1;
        }

        //echo "<br>";
        return ($total / $denominador);
    }

    /**
     * Retorna a porcentagem sobre um valor
     * @param  float   $valor
     * @param  integer $perc
     * @return integer
     */
    public static function pegaPorcentagemValor($valor, $perc) {
        $porcent = ($perc / 100) * $valor;

        return $porcent;
    }

    public static function formataValor($valor, $decDb = '.', $casasDecimais = 2, $mrcDecimal = ',', $mrcMilhar = '.') {
        if ($valor != NULL and $valor != "" and $valor > 0) {

            $decimal = explode($decDb, $valor);

            $zeroDecimal = NULL;
            if ($decDb != $mrcDecimal) {
                if (substr_count($valor, $decDb) > 0) {
                    if (isset($decimal[1]) and strlen($decimal[1]) < $casasDecimais) {
                        for ($i = strlen($decimal[1]); $i < $casasDecimais; $i++) {
                            $zeroDecimal .= "0";
                        }
                        $decimal[0] = str_replace(",", "", $decimal[0]);
                        $decimal[0] = str_replace(".", "", $decimal[0]);
                        $decimal[1] = $decimal[1] . $zeroDecimal;
                        $valor = number_format((int) $decimal[0], 0, "", $mrcMilhar) . $mrcDecimal . $decimal[1];
                    } elseif (isset($decimal[1]) and strlen($decimal[1]) == $casasDecimais) {
                        $decimal[0] = str_replace(",", "", $decimal[0]);
                        $decimal[0] = str_replace(".", "", $decimal[0]);
                        $valor = number_format((int) $decimal[0], 0, "", $mrcMilhar) . $mrcDecimal . $decimal[1];
                    } elseif (isset($decimal[1]) and strlen($decimal[1]) > $casasDecimais) {
                        $decimal[0] = str_replace(",", "", $decimal[0]);
                        $decimal[0] = str_replace(".", "", $decimal[0]);
                        $decimais = "";
                        for ($i = 0; $i < $casasDecimais; $i++) {
                            $decimais .= $decimal[1][$i];
                        }
                        $valor = number_format((int) $decimal[0], 0, "", $mrcMilhar) . $mrcDecimal . $decimais;
                    }
                } else {
                    for ($i = 0; $i < $casasDecimais; $i++) {
                        $zeroDecimal .= "0";
                    }
                    $valor = number_format((int) $decimal[0], 0, "", $mrcMilhar) . $mrcDecimal . $zeroDecimal;
                }
            } else {
                if (substr_count($valor, $decDb) > 0) {

                    if (isset($decimal[1]) and strlen($decimal[1]) < $casasDecimais) {
                        $decimal[0] = str_replace(",", "", $decimal[0]);
                        $decimal[0] = str_replace(".", "", $decimal[0]);
                        for ($i = strlen($decimal[1]); $i < $casasDecimais; $i++) {
                            $zeroDecimal .= "0";
                        }
                        $decimal[1] = $decimal[1] . $zeroDecimal;
                        $valor = number_format((int) $decimal[0], 0, "", $mrcMilhar) . $mrcDecimal . $decimal[1];
                    } elseif (isset($decimal[1]) and strlen($decimal[1]) == $casasDecimais) {
                        $decimal[0] = str_replace(",", "", $decimal[0]);
                        $decimal[0] = str_replace(".", "", $decimal[0]);
                        $valor = number_format((int) $decimal[0], 0, "", $mrcMilhar) . $mrcDecimal . $decimal[1];
                    } elseif (isset($decimal[1]) and strlen($decimal[1]) > $casasDecimais) {
                        $decimal[0] = str_replace(",", "", $decimal[0]);
                        $decimal[0] = str_replace(".", "", $decimal[0]);
                        $decimais = "";
                        for ($i = 0; $i < $casasDecimais; $i++) {
                            $decimais .= $decimal[1][$i];
                        }
                        $valor = number_format((int) $decimal[0], 0, "", $mrcMilhar) . $mrcDecimal . $decimais;
                    } elseif (!isset($decimal[1])) {
                        $decimal[0] = str_replace(",", "", $decimal[0]);
                        $decimal[0] = str_replace(".", "", $decimal[0]);
                        $decimais = "";
                        for ($i = 0; $i < $casasDecimais; $i++) {
                            $decimais .= "0";
                        }
                        $valor = number_format((int) $decimal[0], 0, "", $mrcMilhar) . $mrcDecimal . $decimais;
                    }
                } else {
                    for ($i = 0; $i < $casasDecimais; $i++) {
                        $zeroDecimal .= "0";
                    }
                    $valor = number_format((int) $decimal[0], 0, "", $mrcMilhar) . $mrcDecimal . $zeroDecimal;
                }
            }
        } else {
            $valor = number_format(0, 2, $mrcDecimal, $mrcMilhar);
        }

        return $valor;
    }

    /**
     * Função de formatação de numero para valor monetario
     * @param  string  $format
     * @param  double  $number
     * @param  boolean $useSimbol
     * @return string
     */
    public static function currencyFormat($format, $number, $useSimbol = false) {
        $regex = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?' .
                '(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/';
        if (setlocale(LC_MONETARY, 0) == 'C') {
            setlocale(LC_MONETARY, '');
        }
        $locale = localeconv();
        preg_match_all($regex, $format, $matches, PREG_SET_ORDER);
        foreach ($matches as $fmatch) {
            $value = floatval($number);
            $flags = array(
                'fillchar' => preg_match('/\=(.)/', $fmatch[1], $match) ?
                        $match[1] : ' ',
                'nogroup' => preg_match('/\^/', $fmatch[1]) > 0,
                'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ?
                        $match[0] : '+',
                'nosimbol' => preg_match('/\!/', $fmatch[1]) > 0,
                'isleft' => preg_match('/\-/', $fmatch[1]) > 0
            );
            $width = trim($fmatch[2]) ? (int) $fmatch[2] : 0;
            $left = trim($fmatch[3]) ? (int) $fmatch[3] : 0;
            $right = trim($fmatch[4]) ? (int) $fmatch[4] : $locale['int_frac_digits'];
            $conversion = $fmatch[5];

            $positive = true;
            if ($value < 0) {
                $positive = false;
                $value *= - 1;
            }
            $letter = $positive ? 'p' : 'n';

            $prefix = $suffix = $cprefix = $csuffix = $signal = '';

            $signal = $positive ? $locale['positive_sign'] : $locale['negative_sign'];
            switch (true) {
                case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+':
                    $prefix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+':
                    $suffix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+':
                    $cprefix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+':
                    $csuffix = $signal;
                    break;
                case $flags['usesignal'] == '(':
                case $locale["{$letter}_sign_posn"] == 0:
                    $prefix = '(';
                    $suffix = ')';
                    break;
            }
            if (!$flags['nosimbol']) {
                $currency = $cprefix .
                        ($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']) .
                        $csuffix;
            } else {
                $currency = '';
            }
            $space = $locale["{$letter}_sep_by_space"] ? ' ' : '';

            $value = number_format($value, $right, $locale['mon_decimal_point'], $flags['nogroup'] ? '' : $locale['mon_thousands_sep']);
            $value = @explode($locale['mon_decimal_point'], $value);

            $n = strlen($prefix) + strlen($currency) + strlen($value[0]);
            if ($left > 0 && $left > $n) {
                $value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0];
            }
            $value = implode($locale['mon_decimal_point'], $value);

            if ($useSimbol) {

                if ($locale["{$letter}_cs_precedes"]) {
                    $value = $prefix . $currency . $space . $value . $suffix;
                } else {
                    $value = $prefix . $value . $space . $currency . $suffix;
                }
                if ($width > 0) {
                    $value = str_pad($value, $width, $flags['fillchar'], $flags['isleft'] ?
                                    STR_PAD_RIGHT : STR_PAD_LEFT);
                }
            }
            $format = str_replace($fmatch[0], $value, $format);
        }

        return $format;
    }

}
