<?php

namespace Cityware\Security;

/**
 * Description of AntiInjection
 *
 * @author fabricio.xavier
 */
class AntiInjection
{
    public static function antiSqlInjection1($string)
    {
        // We'll first get rid of any special characters using a simple regex statement.
        // After that, we'll get rid of any SQL command words using a string replacment.
        $banlist = array(
            "insert ", "select ", "update ", "delete ", "distinct ", "having ", "truncate ", "replace",
            "handler", "like ", " as ", " or ", "procedure ", "limit ", "order by ", "group by ", " asc", " desc"
        );
        
        $banlist2 = array(
            "INSERT ", "SELECT ", "UPDATE ", "DELETE ", "DISTINCT ", "HAVING ", "TRUNCATE ", "REPLACE",
            "HANDLER", "LIKE ", " AS ", " OR ", "PROCEDURE ", " LIMIT ", "ORDER BY ", "GROUP BY ", " ASC", " DESC"
        );
        // ---------------------------------------------
        $string2 = trim(str_replace($banlist, '', $string));
        $string3 = trim(str_replace($banlist2, '', $string2));

        return $string3;
    }

    public static function antiSqlInjection2($campo, $adicionaBarras = false)
    {
        // remove palavras que contenham sintaxe sql
        $campo = preg_replace("/(from|alter table|select|insert|delete|update|where|drop table|show tables|#|\*|--|\\\\)/i", "", $campo);
        $campo = trim($campo); //limpa espaços vazio
        $campo = strip_tags($campo); //tira tags html e php
        if ($adicionaBarras || !get_magic_quotes_gpc()){
            $campo = addslashes($campo); //Adiciona barras invertidas a uma string
        }

        return $campo;
    }

    public static function antiSqlInjection3($str)
    {
        $len = strlen($str);
        $escapeCount = 0;
        $targetString = '';
        for ($offset = 0; $offset < $len; $offset++) {
            switch ($c = $str{$offset}) {
                case "'":
                    // Escapes this quote only if its not preceded by an unescaped backslash
                    if ($escapeCount % 2 == 0)
                        $targetString.="\\";
                    $escapeCount = 0;
                    $targetString.=$c;
                    break;
                case '"':
                    // Escapes this quote only if its not preceded by an unescaped backslash
                    if ($escapeCount % 2 == 0)
                        $targetString.="\\";
                    $escapeCount = 0;
                    $targetString.=$c;
                    break;
                case '\\':
                    $escapeCount++;
                    $targetString.=$c;
                    break;
                default:
                    $escapeCount = 0;
                    $targetString.=$c;
            }
        }

        return $targetString;
    }

    public static function antiSqlInjection4($str)
    {
        # Remove palavras suspeitas de injection.
        $str = preg_replace(sql_regcase("/(\n|\r|%0a|%0d|Content-Type:|bcc:|to:|cc:|Autoreply:|from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/"), "", $str);
        $str = trim($str); # Remove espaços vazios.
        $str = strip_tags($str); # Remove tags HTML e PHP.
        $str = addslashes($str); # Adiciona barras invertidas à uma string.

        return $str;
    }

    public static function antiSqlInjection5($str)
    {
        if (get_magic_quotes_gpc()) {
            $clean = mysql_real_escape_string(stripslashes($str));
        } else {
            $clean = mysql_real_escape_string($str);
        }

        return $clean;
    }

    public static function antiSqlInjection6($str)
    {
        $isAttack = false;

        if (preg_match('/[\'"]/', $str)) {
            $isAttack = true;
            // no quotes
        } elseif (preg_match('/[\/\\\\]/', $str)) {
            $isAttack = true;
            // no slashes
        } elseif (preg_match('/(and|or|null|not)/i', $str)) {
            $isAttack = true;
            // no sqli boolean keywords
        } elseif (preg_match('/(union|select|from|where)/i', $str)) {
            $isAttack = true;
            // no sqli select keywords
        } elseif (preg_match('/(group|order|having|limit)/i', $str)) {
            $isAttack = true;
            //  no sqli select keywords
        } elseif (preg_match('/(into|file|case|LOAD_FILE|DUMPFILE|char|schema|AES_DECRYPT|AES_ENCRYPT)/i', $str)) {
            $isAttack = true;
            // no sqli operators
        } elseif (preg_match('/(--|#|\/\*)/', $str)) {
            $isAttack = true;
            // no sqli comments
        } elseif (preg_match('/(=|&|\|)/', $str)) {
            $isAttack = true;
            // no boolean operators
        } elseif (preg_match('/(UNI\*\*ON|1 OR 1=1|1 AND 1=1|1 EXEC XP_)/', $str)) {
            $isAttack = true;
        } elseif (preg_match('/(&#x31;|&#x27;|&#x20;|&#x4F;|&#x52;|&#x3D;|&#49&#39&#32&#79&#82&#32&#39&#49&#39&#61&#39&#49|%31%27%20%4F%52%20%27%31%27%3D%27%31)/', $str)) {
            $isAttack = true;
        } elseif (preg_match('/(SELECT\s[\w\*\)\(\,\s]+\sFROM\s[\w]+)| (UPDATE\s[\w]+\sSET\s[\w\,\'\=]+)| (INSERT\sINTO\s[\d\w]+[\s\w\d\)\(\,]*\sVALUES\s\([\d\w\'\,\)]+)| (DELETE\sFROM\s[\d\w\'\=]+)/', $str)) {
            $isAttack = true;
        } elseif (preg_match('/(script)|(&lt;)|(&gt;)|(%3c)|(%3e)|(SELECT) |(UPDATE) |(INSERT) |(DELETE)|(GRANT) |(REVOKE)|(UNION)|(&amp;lt;)|(&amp;gt;)/', $str)) {
            $isAttack = true;
        } elseif (!preg_match('/^["a-zA-Z0-9\040]+$/', $str)) {
            $isAttack = true;
        }

        if (!$isAttack) {
            return $str;
        } else {
            echo '<pre>';
            print_r('Attack deny');
            exit;
        }
    }

}
