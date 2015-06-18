<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cityware\Format;

/**
 * Description of Number
 *
 * @author fabricio.xavier
 */
class Number {

    /**
     * Formatação e numero inteiro
     * @param float/integer $value
     * @param string $language
     * @return integer
     */
    public static function integerNumber($value, $language = 'pt_BR') {
        $valInteger = new \NumberFormatter($language, \NumberFormatter::DECIMAL);
        $valInteger->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, 0);
        $valInteger->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, 0);
        return $valInteger->format((float) $value, \NumberFormatter::TYPE_INT64);
    }
    
    /**
     * Formatação de numero formato moeda
     * @param float/integer $value
     * @param integer $precision
     * @param string $language
     * @return float
     */
    public static function currency($value, $precision = 2, $language = 'pt_BR') {
        $valCurrency = new \NumberFormatter($language, \NumberFormatter::CURRENCY);
        $valCurrency->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $precision);
        $valCurrency->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $precision);
        return $valCurrency->format((float) $value, \NumberFormatter::TYPE_DOUBLE);
    }
    
    /**
     * Formatação de numero decimal
     * @param float/integer $value
     * @param integer $precision
     * @param string $language
     * @return float
     */
    public static function decimalNumber($value, $precision = 2, $language = 'pt_BR') {
        $valDecimal = new \NumberFormatter($language, \NumberFormatter::DECIMAL);
        $valDecimal->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $precision);
        $valDecimal->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $precision);
        return $valDecimal->format((float) $value, \NumberFormatter::DECIMAL);
    }

}
