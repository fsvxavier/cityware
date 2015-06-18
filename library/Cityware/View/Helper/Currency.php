<?php

namespace Cityware\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Currency extends AbstractHelper {

    public function __invoke($value, $precision = 2, $locale = 'pt_BR') {
        return \Cityware\Format\Number::currency($value, $precision, $locale);
    }

}
