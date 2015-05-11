<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cityware\Format;

/**
 * Description of Date
 *
 * @author Fabricio
 */
final class Date {

    /**
     * Função que soma ou subtrai, dias, meses ou anos de uma data qualquer
     * - Ex:
     * $date = operations("06/01/2003", "sum", "day", "4")   // Return 10/01/2003
     * $date = operations("06/01/2003", "sub", "day", "4")   // Return 02/01/2003
     * $date = operations("06/01/2003", "sum", "month", "4") // Return 10/05/2003
     *
     * @param  date    $date
     * @param  string  $operation
     * @param  boolean $where
     * @param  integer $quant
     * @param  string  $return_format
     * @return type
     */
    public static function dateOperations($date, $operation, $where, $quant, $return_format = null) {
        // Verifica erros
        $warning = "<br>Warning! Date Operations Fail... ";
        if (!$date || !$operation) {
            return "{$warning} invalid or inexistent arguments<br>";
        } else {
            if (!($operation == "sub" || $operation == "-" || $operation == "sum" || $operation == "+")) {
                return "<br>{$warning} Invalid Operation...<br>";
            } else {
                // Separa dia, mês e ano
                list($day, $month, $year) = explode("/", $date);

                // Determina a operação (Soma ou Subtração)
                $op = ($operation == "sub" || $operation == "-") ? "-" : '';

                $sum_month = null;
                $sum_day = null;
                $sum_year = null;

                // Determina aonde será efetuada a operação (dia, mês, ano)
                if ($where == "day") {
                    $sum_day = $op . $quant;
                }
                if ($where == "month") {
                    $sum_month = $op . $quant;
                }
                if ($where == "year") {
                    $sum_year = $op . $quant;
                }

                // Gera o timestamp
                $date = mktime(0, 0, 0, $month + $sum_month, $day + $sum_day, $year + $sum_year);

                // Retorna o timestamp ou extended
                $dateReturn = ($return_format == "timestamp" || $return_format == "ts") ? $date : date("d/m/Y", "$date");

                // Retorna a data
                return $dateReturn;
            }
        }
    }

    /**
     * Função de tratamento de campos com data
     * @param  mixed  $varValue  (Array de Valores ou Nome de Variável Default [GET/POST])
     * @param  string $varFormat (Formato da data no padrão PHP)
     * @return mixed
     */
    public static function formatDate($varValue, $varFormat) {
        if (is_array($varValue)) {
            $temp = $varValue;
        } else {
            if (strtolower($varValue) == 'get' or strtolower($varValue) == 'post') {
                eval('$temp = &$_' . strtoupper($varValue) . ';');
            } else {
                $temp = array($varValue);
            }
        }
        foreach ($temp as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $key2 => $value2) {
                    $datetime = null;
                    $date = null;
                    if (preg_match('/^([0-9]{4})[-,\/]([0-9]{1,2})[-,\/]([0-9]{1,2})/', $value2)) {
                        $datetime = explode(' ', $value2);
                        if (is_array($datetime) and count($datetime) > 1) {
                            $temp[$key][$key2] = date($varFormat, strtotime($datetime[0])) . ' ' . $datetime[1];
                        } else {
                            $temp[$key][$key2] = date($varFormat, strtotime($datetime[0]));
                        }
                    } elseif (preg_match('/^([0-9]{1,2})[-,\/]([0-9]{1,2})[-,\/]([0-9]{4})/', $value2, $date)) {
                        $datetime = explode(' ', $value2);
                        if (is_array($datetime) and count($datetime) > 1) {
                            $temp[$key][$key2] = date($varFormat, strtotime($date[3] . '-' . $date[2] . '-' . $date[1])) . ' ' . $datetime[1];
                        } else {
                            $temp[$key][$key2] = date($varFormat, strtotime($date[3] . '-' . $date[2] . '-' . $date[1]));
                        }
                    }
                }
            } else {
                $datetime = null;
                $date = null;
                if (preg_match('/^([0-9]{4})[-,\/]([0-9]{1,2})[-,\/]([0-9]{1,2})/', $value)) {
                    $datetime = explode(' ', $value);
                    if (is_array($datetime) and count($datetime) > 1) {
                        $temp[$key] = date($varFormat, strtotime($datetime[0])) . ' ' . $datetime[1];
                    } else {
                        $temp[$key] = date($varFormat, strtotime($datetime[0]));
                    }
                } elseif (preg_match('/^([0-9]{1,2})[-,\/]([0-9]{1,2})[-,\/]([0-9]{4})/', $value, $date)) {
                    $datetime = explode(' ', $value);
                    
                    
                    if (is_array($datetime) and count($datetime) > 1) {
                        $temp[$key] = date($varFormat, strtotime($date[3] . '-' . $date[2] . '-' . $date[1])) . ' ' . $datetime[1];
                    } else {
                        $temp[$key] = date($varFormat, strtotime($date[3] . '-' . $date[2] . '-' . $date[1]));
                    }
                }
            }
        }
        return $temp;
    }

    /**
     * Função de conversão de segundos para Array(Hora, Minuto, Segundo)
     * @param  integer $seconds
     * @return array
     */
    public static function secondsToTime($seconds) {
        // extract hours
        $hours = floor($seconds / (60 * 60));

        // extract minutes
        $divisor_for_minutes = $seconds % (60 * 60);
        $minutes = floor($divisor_for_minutes / 60);

        // extract the remaining seconds
        $divisor_for_seconds = $divisor_for_minutes % 60;
        $seconds = ceil($divisor_for_seconds);

        // return the final array
        $arrayReturn = array(
            "h" => (int) $hours,
            "m" => (int) $minutes,
            "s" => (int) $seconds,
        );

        return $arrayReturn;
    }

    /**
     * Conversão de hora para o formato que definir (Hora, Minuto, Segundo)
     * @param  time    $time
     * @param  string  $conversion
     * @param  boolean $debub
     * @return double
     */
    public static function convertTime($time, $conversion, $debub = false) {
        list ($hora, $minuto, $segundo) = explode(":", $time);
        switch (strtolower($conversion)) {
            case 'h':
                $resultH = $hora * 1;
                $resultM = $minuto / 60;
                $resultS = $segundo / 60;
                break;
            case 'm':
                $resultH = $hora * 60;
                $resultM = $minuto * 1;
                $resultS = $segundo / 60;
                break;
            case 's':
                $resultH = $hora * 60;
                $resultM = $minuto * 60;
                $resultS = $segundo * 1;
                break;
        }

        $result = Cityware_Format_Mask::formataValor(($resultH + $resultM + $resultS), ".", 2, ".", "");

        if ($debub) {
            echo "Hora: " . $resultH . " Minuto: " . $resultM . " Segundo: " . $resultS . " Total: " . $result . "<br>";
        }

        return $result;
    }

    /**
     * Cria um intervalo de Meses de acordo com a data inicial e final
     * @param  date  $startData
     * @param  date  $endDate
     * @return array
     */
    public static function getMonthsRange($startData, $endDate) {
        $time1 = strtotime($startData); //absolute date comparison needs to be done here, because PHP doesn't do date comparisons
        $time2 = strtotime($endDate);
        //$my1 = date('mY', $time1); //need these to compare dates at 'month' granularity
        //$my2 = date('mY', $time2);
        $year1 = date('Y', $time1);
        $year2 = date('Y', $time2);
        $years = range($year1, $year2);
        $months = Array();
        foreach ($years as $year) {
            $months[$year] = array();
            while ($time1 < $time2) {
                if (date('Y', $time1) == $year) {
                    $months[$year][] = date('m', $time1);
                    $time1 = strtotime(date('Y-m-d', $time1) . ' +1 month');
                } else {
                    break;
                }
            }
            continue;
        }

        return $months;
    }

    /**
     * Função de geração de intervalo de semana
     * @param  type $datestr
     * @return type
     */
    public static function rangeWeek($datestr) {
        date_default_timezone_set(date_default_timezone_get());
        $dt = strtotime($datestr);
        $res['start'] = date('N', $dt) == 1 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('last monday', $dt));
        $res['end'] = date('N', $dt) == 7 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('next sunday', $dt));

        return $res;
    }

    /**
     * Função para criação de intervalo de datas
     * @param  type  $strDateFrom
     * @param  type  $strDateTo
     * @return array
     */
    public static function createDateRangeArray($strDateFrom, $strDateTo) {
        // takes two dates formatted as YYYY-MM-DD and creates an
        // inclusive array of the dates between the from and to dates.
        // could test validity of dates here but I'm already doing
        // that in the main script

        $aryRange = array();

        $iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2), substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
        $iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2), substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));

        if ($iDateTo >= $iDateFrom) {
            array_push($aryRange, date('Y-m-d', $iDateFrom)); // first entry

            while ($iDateFrom < $iDateTo) {
                $iDateFrom+=86400; // add 24 hours
                array_push($aryRange, date('Y-m-d', $iDateFrom));
            }
        }

        return $aryRange;
    }

    public static function extensionDate() {
        $hoje = getdate();

        // Nessa parte do código foi criada a variável $hoje, que receberá os valores da data.
        switch ($hoje['wday']) {
            case 0:
                $diaSemana = "Domingo, ";
                break;
            case 1:
                $diaSemana = "Segunda-Feira, ";
                break;
            case 2:
                $diaSemana = "Terça-Feira, ";
                break;
            case 3:
                $diaSemana = "Quarta-Feira, ";
                break;
            case 4:
                $diaSemana = "Quinta-Feira, ";
                break;
            case 5:
                $diaSemana = "Sexta-Feira, ";
                break;
            case 6:
                $diaSemana = "Sábado, ";
                break;
        }

        // Acima foi utilizada a instrução switch para que o dia da semana possa ser apresentado por
        // extenso, já que o PHP retorna em números. Perceba que dentro de cada instrução case tem uma
        // instrução echo que escreve o dia da semana na tela.

        $dia = $hoje['mday'];

        // A instrução echo $hoje[‘mday’]; escreve na tela o data em número,
        // conforme retorna o PHP, não precisando de conversão.

        switch ($hoje['mon']) {
            case 1:
                $mes = " de Janeiro de ";
                break;
            case 2:
                $mes = " de Fevereiro de ";
                break;
            case 3:
                $mes = " de Março de ";
                break;
            case 4:
                $mes = " de Abril de ";
                break;
            case 5:
                $mes = " de Maio de ";
                break;
            case 6:
                $mes = " de Junho de ";
                break;
            case 7:
                $mes = " de Julho de ";
                break;
            case 8:
                $mes = " de Agosto de ";
                break;
            case 9:
                $mes = " de Setembro de ";
                break;
            case 10:
                $mes = " de Outubro de ";
                break;
            case 11:
                $mes = " de Novembro de ";
                break;
            case 12:
                $mes = " de Dezembro de ";
                break;
        }

        // A parte do código acima tem a mesma função que o primeiro switch utilizado,
        // só que agora ele é usado para apresentar o mês.

        $ano = $hoje['year'];

        return $diaSemana . $dia . $mes . $ano;
    }

    /**
     * Retorna o mês por extenso
     * @param  integer $month
     * @return string
     */
    public static function extensionMonth($month) {
        $mes = null;
        switch ($month) {
            case '01':
            case 1:
                $mes = "Janeiro";
                break;
            case '02':
            case 2:
                $mes = "Fevereiro";
                break;
            case '03':
            case 3:
                $mes = "Março";
                break;
            case '04':
            case 4:
                $mes = "Abril";
                break;
            case '05':
            case 5:
                $mes = "Maio";
                break;
            case '06':
            case 6:
                $mes = "Junho";
                break;
            case '07':
            case 7:
                $mes = "Julho";
                break;
            case '08':
            case 8:
                $mes = "Agosto";
                break;
            case '09':
            case 9:
                $mes = "Setembro";
                break;
            case '10':
            case 10:
                $mes = "Outubro";
                break;
            case '11':
            case 11:
                $mes = "Novembro";
                break;
            case '12':
            case 12:
                $mes = "Dezembro";
                break;
        }

        return $mes;
    }
    
    /**
     * Retorna o mês por extenso em formato reduzido
     * @param  integer $month
     * @return string
     */
    public static function extensionShortMonth($month) {
        $mes = null;
        switch ($month) {
            case '01':
            case 1:
                $mes = "Jan";
                break;
            case '02':
            case 2:
                $mes = "Fev";
                break;
            case '03':
            case 3:
                $mes = "Mar";
                break;
            case '04':
            case 4:
                $mes = "Abr";
                break;
            case '05':
            case 5:
                $mes = "Mai";
                break;
            case '06':
            case 6:
                $mes = "Jun";
                break;
            case '07':
            case 7:
                $mes = "Jul";
                break;
            case '08':
            case 8:
                $mes = "Ago";
                break;
            case '09':
            case 9:
                $mes = "Set";
                break;
            case '10':
            case 10:
                $mes = "Out";
                break;
            case '11':
            case 11:
                $mes = "Nov";
                break;
            case '12':
            case 12:
                $mes = "Dez";
                break;
        }

        return $mes;
    }

}
