<?php

namespace Cityware\Chart\Adapter\Highcharts;

abstract class HighchartsAbstract
{
    /**
     * Render the chart options and returns the javascript that
     * represents them
     *
     * @return string The javascript code
     */
    abstract public function renderOptions();

    /**
     * Render the chart and returns the javascript that
     * must be printed to the page to create the chart
     *
     * @param string $varName  The javascript chart variable name
     * @param string $callback The function callback to pass
     *                         to the Highcharts.Chart method
     *
     * @return string The javascript code
     */
    abstract public function render($varName, $callback = null);

    /**
     * Global options that don't apply to each chart like lang and global
     * must be set using the Highcharts.setOptions javascript method.
     * This method receives a set of HighchartOption and returns the
     * javascript string needed to set those options globally
     *
     * @param HighchartOption The options to create
     *
     * @return string The javascript needed to set the global options
     */

    abstract public function __set($offset, $value);

    abstract public function __get($offset);

    abstract public function offsetSet($offset, $value);

    abstract public function offsetExists($offset);

    abstract public function offsetUnset($offset);

    abstract public function offsetGet($offset);

}
