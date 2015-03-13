<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cityware\Format;

/**
 * String handling with encoding support
 *
 * PHP needs to be compiled with --enable-mbstring
 * or a fallback without encoding support is used
 */
class Str {

    public static function cleanString($text) {
        // 1) convert á ô => a o
        $text = preg_replace("/[áàâãªä]/u", "a", $text);
        $text = preg_replace("/[ÁÀÂÃÄ]/u", "A", $text);
        $text = preg_replace("/[ÍÌÎÏ]/u", "I", $text);
        $text = preg_replace("/[íìîï]/u", "i", $text);
        $text = preg_replace("/[éèêë]/u", "e", $text);
        $text = preg_replace("/[ÉÈÊË]/u", "E", $text);
        $text = preg_replace("/[óòôõºö]/u", "o", $text);
        $text = preg_replace("/[ÓÒÔÕÖ]/u", "O", $text);
        $text = preg_replace("/[úùûü]/u", "u", $text);
        $text = preg_replace("/[ÚÙÛÜ]/u", "U", $text);
        $text = preg_replace("/[’‘‹›‚]/u", "'", $text);
        $text = preg_replace("/[“”«»„]/u", '"', $text);
        $text = str_replace("–", "-", $text);
        $text = str_replace(" ", " ", $text);
        $text = str_replace("ç", "c", $text);
        $text = str_replace("Ç", "C", $text);
        $text = str_replace("ñ", "n", $text);
        $text = str_replace("Ñ", "N", $text);

        //2) Translation CP1252. &ndash; => -
        $trans = get_html_translation_table(HTML_ENTITIES);
        $trans[chr(130)] = '&sbquo;';    // Single Low-9 Quotation Mark
        $trans[chr(131)] = '&fnof;';    // Latin Small Letter F With Hook
        $trans[chr(132)] = '&bdquo;';    // Double Low-9 Quotation Mark
        $trans[chr(133)] = '&hellip;';    // Horizontal Ellipsis
        $trans[chr(134)] = '&dagger;';    // Dagger
        $trans[chr(135)] = '&Dagger;';    // Double Dagger
        $trans[chr(136)] = '&circ;';    // Modifier Letter Circumflex Accent
        $trans[chr(137)] = '&permil;';    // Per Mille Sign
        $trans[chr(138)] = '&Scaron;';    // Latin Capital Letter S With Caron
        $trans[chr(139)] = '&lsaquo;';    // Single Left-Pointing Angle Quotation Mark
        $trans[chr(140)] = '&OElig;';    // Latin Capital Ligature OE
        $trans[chr(145)] = '&lsquo;';    // Left Single Quotation Mark
        $trans[chr(146)] = '&rsquo;';    // Right Single Quotation Mark
        $trans[chr(147)] = '&ldquo;';    // Left Double Quotation Mark
        $trans[chr(148)] = '&rdquo;';    // Right Double Quotation Mark
        $trans[chr(149)] = '&bull;';    // Bullet
        $trans[chr(150)] = '&ndash;';    // En Dash
        $trans[chr(151)] = '&mdash;';    // Em Dash
        $trans[chr(152)] = '&tilde;';    // Small Tilde
        $trans[chr(153)] = '&trade;';    // Trade Mark Sign
        $trans[chr(154)] = '&scaron;';    // Latin Small Letter S With Caron
        $trans[chr(155)] = '&rsaquo;';    // Single Right-Pointing Angle Quotation Mark
        $trans[chr(156)] = '&oelig;';    // Latin Small Ligature OE
        $trans[chr(159)] = '&Yuml;';    // Latin Capital Letter Y With Diaeresis
        $trans['euro'] = '&euro;';    // euro currency symbol
        ksort($trans);

        foreach ($trans as $k => $v) {
            $text = str_replace($v, $k, $text);
        }

        // 3) remove <p>, <br/> ...
        $text = strip_tags($text);

        // 4) &amp; => & &quot; => '
        $text = html_entity_decode($text);

        // 5) remove Windows-1252 symbols like "TradeMark", "Euro"...
        $text = preg_replace('/[^(\x20-\x7F)]*/', '', $text);

        $targets = array('\r\n', '\n', '\r', '\t');
        $results = array(" ", " ", " ", "");
        $text = str_replace($targets, $results, $text);

        //XML compatible
        /*
          $text = str_replace("&", "and", $text);
          $text = str_replace("<", ".", $text);
          $text = str_replace(">", ".", $text);
          $text = str_replace("\\", "-", $text);
          $text = str_replace("/", "-", $text);
         */

        return ($text);
    }

    public static function cleanNonAsciiCharactersInString($str) {
        
        $breaks = array("\r\n","\n\r","\r","\n");
        $breaksNew = array('\r\n','\n\r','\r','\n');
        $str1 = str_ireplace($breaks, $breaksNew, $str);
        
        $text1 = preg_replace('/[[:^print:]]/', '', $str1);
        
        $text = str_ireplace($breaksNew, $breaks, $text1);
        
        return $text;
    }

    /**
     * Verifica se na where contem variável PHP e prepara o mesmo ou somente define a where
     * @param string $value
     * @return string
     */
    public static function preparePhpTag($value, $isWhere = true) {

        if (strpos($value, "{") and strpos($value, "}")) {
            /* Pega por expressão regular as veriáveis PHP */
            $return = $arr = $arr2 = array();
            preg_match_all("/'\{(.*)\}'/U", $value, $arr);
            foreach ($arr[1] as $key2 => $value2) {
                $replace = null;
                eval('$replace = ' . $value2 . ';');
                $arr2[$key2] = ($isWhere) ? "'" . $replace . "'" : $replace;
            }
            if (count($arr[0]) > 1) {
                $valueTemp = $value;
                /* Monta a definição da where */
                foreach ($arr[0] as $key3 => $value3) {
                    $valueTemp = str_replace($value3, $arr2[$key3], $valueTemp);
                }
                $return = $valueTemp;
            } else {
                /* Monta a definição da where */
                foreach ($arr[0] as $key3 => $value3) {
                    $return = str_replace($value3, $arr2[$key3], $value);
                }
            }
            return $return;
        } else {
            return $value;
        }
    }

    /**
     * Truncates a string to the given length.  It will optionally preserve
     * HTML tags if $is_html is set to true.
     *
     * @param   string  $string        the string to truncate
     * @param   int     $limit         the number of characters to truncate too
     * @param   string  $continuation  the string to use to denote it was truncated
     * @param   bool    $is_html       whether the string has HTML
     * @return  string  the truncated string
     */
    public static function truncate($string, $limit, $continuation = '...', $is_html = false) {
        $offset = 0;
        $tags = array();
        if ($is_html) {
            // Handle special characters.
            preg_match_all('/&[a-z]+;/i', strip_tags($string), $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
            foreach ($matches as $match) {
                if ($match[0][1] >= $limit) {
                    break;
                }
                $limit += (static::length($match[0][0]) - 1);
            }

            // Handle all the html tags.
            preg_match_all('/<[^>]+>([^<]*)/', $string, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
            foreach ($matches as $match) {
                if ($match[0][1] - $offset >= $limit) {
                    break;
                }
                $tag = static::sub(strtok($match[0][0], " \t\n\r\0\x0B>"), 1);
                if ($tag[0] != '/') {
                    $tags[] = $tag;
                } elseif (end($tags) == static::sub($tag, 1)) {
                    array_pop($tags);
                }
                $offset += $match[1][1] - $match[0][1];
            }
        }
        $new_string = static::sub($string, 0, $limit = min(static::length($string), $limit + $offset));
        $new_string .= (static::length($string) > $limit ? $continuation : '');
        $new_string .= (count($tags = array_reverse($tags)) ? '</' . implode('></', $tags) . '>' : '');
        return $new_string;
    }

    /**
     * Add's _1 to a string or increment the ending number to allow _2, _3, etc
     *
     * @param   string  $str  required
     * @return  string
     */
    public static function increment($str, $first = 1, $separator = '_') {
        preg_match('/(.+)' . $separator . '([0-9]+)$/', $str, $match);
        return isset($match[2]) ? $match[1] . $separator . ($match[2] + 1) : $str . $separator . $first;
    }

    /**
     * Checks wether a string has a precific beginning.
     *
     * @param   string   $str          string to check
     * @param   string   $start        beginning to check for
     * @param   boolean  $ignore_case  wether to ignore the case
     * @return  boolean  wether a string starts with a specified beginning
     */
    public static function starts_with($str, $start, $ignore_case = false) {
        return (bool) preg_match('/^' . preg_quote($start, '/') . '/m' . ($ignore_case ? 'i' : ''), $str);
    }

    /**
     * Checks wether a string has a precific ending.
     *
     * @param   string   $str          string to check
     * @param   string   $end          ending to check for
     * @param   boolean  $ignore_case  wether to ignore the case
     * @return  boolean  wether a string ends with a specified ending
     */
    public static function ends_with($str, $end, $ignore_case = false) {
        return (bool) preg_match('/' . preg_quote($end, '/') . '$/m' . ($ignore_case ? 'i' : ''), $str);
    }

    /**
     * substr
     *
     * @param   string    $str       required
     * @param   int       $start     required
     * @param   int|null  $length
     * @param   string    $encoding  default UTF-8
     * @return  string
     */
    public static function sub($str, $start, $length = null, $encoding = null) {
        $encoding or $encoding = \Fuel::$encoding;
        // substr functions don't parse null correctly
        $length = is_null($length) ? (function_exists('mb_substr') ? mb_strlen($str, $encoding) : strlen($str)) - $start : $length;
        return function_exists('mb_substr') ? mb_substr($str, $start, $length, $encoding) : substr($str, $start, $length);
    }

    /**
     * strlen
     *
     * @param   string  $str       required
     * @param   string  $encoding  default UTF-8
     * @return  int
     */
    public static function length($str, $encoding = null) {
        $encoding or $encoding = \Fuel::$encoding;
        return function_exists('mb_strlen') ? mb_strlen($str, $encoding) : strlen($str);
    }

    /**
     * lower
     *
     * @param   string  $str       required
     * @param   string  $encoding  default UTF-8
     * @return  string
     */
    public static function lower($str, $encoding = null) {
        $encoding or $encoding = \Fuel::$encoding;
        return function_exists('mb_strtolower') ? mb_strtolower($str, $encoding) : strtolower($str);
    }

    /**
     * upper
     *
     * @param   string  $str       required
     * @param   string  $encoding  default UTF-8
     * @return  string
     */
    public static function upper($str, $encoding = null) {
        $encoding or $encoding = \Fuel::$encoding;
        return function_exists('mb_strtoupper') ? mb_strtoupper($str, $encoding) : strtoupper($str);
    }

    /**
     * lcfirst
     *
     * Does not strtoupper first
     *
     * @param   string  $str       required
     * @param   string  $encoding  default UTF-8
     * @return  string
     */
    public static function lcfirst($str, $encoding = null) {
        $encoding or $encoding = \Fuel::$encoding;
        return function_exists('mb_strtolower') ? mb_strtolower(mb_substr($str, 0, 1, $encoding), $encoding) . mb_substr($str, 1, mb_strlen($str, $encoding), $encoding) : lcfirst($str);
    }

    /**
     * ucfirst
     *
     * Does not strtolower first
     *
     * @param   string $str       required
     * @param   string $encoding  default UTF-8
     * @return   string
     */
    public static function ucfirst($str, $encoding = 'UTF-8') {
        $encoding or $encoding = 'UTF-8';
        return function_exists('mb_strtoupper') ? mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding) . mb_substr($str, 1, mb_strlen($str, $encoding), $encoding) : ucfirst($str);
    }

    /**
     * ucwords
     *
     * First strtolower then ucwords
     *
     * ucwords normally doesn't strtolower first
     * but MB_CASE_TITLE does, so ucwords now too
     *
     * @param   string   $str       required
     * @param   string   $encoding  default UTF-8
     * @return  string
     */
    public static function ucwords($str, $encoding = 'UTF-8') {
        $encoding or $encoding = 'UTF-8';
        return function_exists('mb_convert_case') ? mb_convert_case($str, MB_CASE_TITLE, $encoding) : ucwords(strtolower($str));
    }

    /**
     * Creates a random string of characters
     *
     * @param   string  the type of string
     * @param   int     the number of characters
     * @return  string  the random string
     */
    public static function random($type = 'alnum', $length = 16) {
        switch ($type) {
            case 'basic':
                return mt_rand();
                break;

            default:
            case 'alnum':
            case 'numeric':
            case 'nozero':
            case 'alpha':
            case 'distinct':
            case 'hexdec':
                switch ($type) {
                    case 'alpha':
                        $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;

                    default:
                    case 'alnum':
                        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;

                    case 'numeric':
                        $pool = '0123456789';
                        break;

                    case 'nozero':
                        $pool = '123456789';
                        break;

                    case 'distinct':
                        $pool = '2345679ACDEFHJKLMNPRSTUVWXYZ';
                        break;

                    case 'hexdec':
                        $pool = '0123456789abcdef';
                        break;
                }

                $str = '';
                for ($i = 0; $i < $length; $i++) {
                    $str .= substr($pool, mt_rand(0, strlen($pool) - 1), 1);
                }
                return $str;
                break;

            case 'unique':
                return md5(uniqid(mt_rand()));
                break;

            case 'sha1' :
                return sha1(uniqid(mt_rand(), true));
                break;

            case 'uuid':
                $pool = array('8', '9', 'a', 'b');
                return sprintf('%s-%s-4%s-%s%s-%s', static::random('hexdec', 8), static::random('hexdec', 4), static::random('hexdec', 3), $pool[array_rand($pool)], static::random('hexdec', 3), static::random('hexdec', 12));
                break;
        }
    }

    /**
     * Returns a closure that will alternate between the args which to return.
     * If you call the closure with false as the arg it will return the value without
     * alternating the next time.
     *
     * @return  Closure
     */
    public static function alternator() {
        // the args are the values to alternate
        $args = func_get_args();

        return function ($next = true) use ($args) {
            static $i = 0;
            return $args[($next ? $i++ : $i) % count($args)];
        };
    }

    /**
     * Parse the params from a string using strtr()
     *
     * @param   string  string to parse
     * @param   array   params to str_replace
     * @return  string
     */
    public static function tr($string, $array = array()) {
        if (is_string($string)) {
            $tr_arr = array();

            foreach ($array as $from => $to) {
                substr($from, 0, 1) !== ':' and $from = ':' . $from;
                $tr_arr[$from] = $to;
            }
            unset($array);

            return strtr($string, $tr_arr);
        } else {
            return $string;
        }
    }

    /**
     * Check if a string is json encoded
     *
     * @param  string $string string to check
     * @return bool
     */
    public static function is_json($string) {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Check if a string is a valid XML
     *
     * @param  string $string string to check
     * @return bool
     */
    public static function is_xml($string) {
        if (!defined('LIBXML_COMPACT')) {
            throw new \Exception('libxml is required to use Str::is_xml()');
        }

        $internal_errors = libxml_use_internal_errors();
        libxml_use_internal_errors(true);
        $result = simplexml_load_string($string) !== false;
        libxml_use_internal_errors($internal_errors);

        return $result;
    }

    /**
     * Check if a string is serialized
     *
     * @param  string $string string to check
     * @return bool
     */
    public static function is_serialized($string) {
        $array = @unserialize($string);
        return !($array === false and $string !== 'b:0;');
    }

    /**
     * Check if a string is html
     *
     * @param  string $string string to check
     * @return bool
     */
    public static function is_html($string) {
        return strlen(strip_tags($string)) < strlen($string);
    }

}
