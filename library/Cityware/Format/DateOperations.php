<?php

namespace Cityware\Format;

/**
 * Description of Date2
 *
 * @author fabricio.xavier
 */
class DateOperations {

    private $dateTime, $dateTimeDiff;

    public function __construct() {
        $this->dateTime = new \DateTime();
        $this->dateTimeDiff = new \DateTime();
    }

    /**
     * Função de envio de data baseado em string no formado Y-m-d
     * @param string $date
     * @return \Cityware\Format\DateOperations
     */
    public function setDate($date, $diff = false) {
        $dateTemp = str_replace('/', '-', $date);
        $formatedDate =  date('Y-m-d', strtotime($dateTemp));
        list($year, $month, $day) = explode("-", $formatedDate);
        if($diff){
            $this->dateTimeDiff->setDate($year, $month, $day);
        } else {
            $this->dateTime->setDate($year, $month, $day);
        }
        return $this;
    }
    
    /**
     * 
     * @return type
     */
    public function parseDate() {
        return \date_parse($this->format());
    }
    
    /**
     * 
     * @param type $modify
     * @return type
     */
    public function dateModify($modify) {
        $modified = (array)$this->dateTime->modify($modify);
        $date = explode(' ', $modified['date']);
        
        return $this->setDate($date[0])->parseDate();
    }
    
    /**
     * Pega o primeiro dia do mês corrente
     * @return \Cityware\Format\DateOperations
     */
    public function firstDayOfThisMonth() {
        $this->dateTime->modify('first day of this month');
        return $this;
    }
    
    /**
     * Pega o ultimo dia do mês corrente
     * @return \Cityware\Format\DateOperations
     */
    public function lastDayOfThisMonth() {
        $this->dateTime->modify('last day of this month');
        return $this;
    }

    /**
     * 
     * @param type $date
     * @return string
     */
    private function convertDate($date) {
        $return = Array();
        $return['dateFormated'] = str_replace(Array('-', '/', '.', '_'), '-', $date);
        if (preg_match('/^(19|20)\d\d[\-\/.](0[1-9]|1[012])[\-\/.](0[1-9]|[12][0-9]|3[01])$/', $return['dateFormated'])) {
            $return['format'] = 'Y-m-d';
        } else if (preg_match('/^(0[1-9]|[12][0-9]|3[01])[\-\/.](0[1-9]|1[012])[\-\/.](19|20)\d\d$/', $return['dateFormated'])) {
            $return['format'] = 'd-m-Y';
        } else {
            throw new \Exception('Necessário usilizar data nos formatos ddmmYYYY ou YYYYmmdd e separadores podendo ser ".", "-", "/", "_" sem as aspas');
        }
        return $return;
    }

    /**
     * Função de conversão de tipo
     * @param string $type
     * @return string
     */
    private function converType($type) {
        switch (strtolower($type)) {
            case 'd':
                $typeSum = 'day';
                break;
            case 'm':
                $typeSum = 'month';
                break;
            case 'y':
                $typeSum = 'year';
                break;
        }
        return $typeSum;
    }

    /**
     * Função de soma com datas
     * @param integer $num Número que será adicionado
     * @param string $type Tipo de adição (Dia = d, Mês = m, Ano = y)
     * @return \Cityware\Format\DateOperations
     */
    public function sum($num, $type = 'd') {
        $typeSum = $this->converType($type);
        $this->dateTime->modify('+' . $num . ' ' . \Cityware\Format\Inflector::pluralize($typeSum));
        return $this;
    }

    /**
     * Função de subtração com datas
     * @param integer $num Número que será adicionado
     * @param string $type Tipo de adição (Dia = d, Mês = m, Ano = y)
     * @return \Cityware\Format\DateOperations
     */
    public function sub($num, $type = 'd') {
        $typeSum = $this->converType($type);
        $this->dateTime->modify('-' . $num . ' ' . \Cityware\Format\Inflector::pluralize($typeSum));
        return $this;
    }

    /**
     * Função de calculo de diferença entre datas
     * @param string $date Data baseado em string no formado Y-m-d
     * @return integer
     */
    public function difference($date = null) {
        if (!empty($date)) {
            $this->setDate($date, true);
        }
        $interval = (array) $this->dateTime->diff($this->dateTimeDiff);
        return $interval['days'];
    }

    /**
     * Função de renderização do resultado
     * @param string $format
     */
    public function render($format = 'Y-m-d') {
        echo $this->dateTime->format($format);
    }
    
    /**
     * Função de formatação do resultado
     * @param string $format
     */
    public function format($format = 'Y-m-d') {
        return $this->dateTime->format($format);
    }

}
