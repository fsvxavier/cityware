<?php

namespace Cityware\View\Helper;

use Zend\View\Helper\AbstractHelper;

class NumberFormat extends AbstractHelper {

    public function __invoke($value, $precision = 2, $locale = 'pt_BR') {
        return \Cityware\Format\Number::decimalNumber($value, $precision, $locale);
    }

}

