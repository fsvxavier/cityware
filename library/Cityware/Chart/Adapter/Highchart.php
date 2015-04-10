<?php

namespace Cityware\Chart\Adapter;

use Cityware\Chart\Adapter\Highcharts\HighchartOption;
use Cityware\Chart\Adapter\Highcharts\HighchartOptionRenderer;
use Cityware\Chart\Adapter\Highcharts\HighchartsAbstract;

/**
 * Description of Adapter
 *
 * @author fabricio.xavier
 */
class Highchart extends HighchartsAbstract implements \ArrayAccess {

    //The chart type.
    //A regullar higchart
    const HIGHCHART = 0;
    //A highstock chart
    const HIGHSTOCK = 1;
    //The js engine to use
    const ENGINE_JQUERY = 10;
    const ENGINE_MOOTOOLS = 11;
    const ENGINE_PROTOTYPE = 12;

    /**
     * The chart options
     *
     * @var array
     */
    protected $options = array();

    /**
     * The chart type.
     * Either self::HIGHCHART or self::HIGHSTOCK
     *
     * @var int
     */
    protected $chartType = self::HIGHCHART;

    /**
     * The javascript library to use.
     * One of ENGINE_JQUERY, ENGINE_MOOTOOLS or ENGINE_PROTOTYPE
     *
     * @var int
     */
    protected $jsEngine = self::ENGINE_JQUERY;

    /**
     * Clone Highchart object
     */
    public function __clone() {
        foreach ($this->options as $key => $value) {
            $this->options[$key] = clone $value;
        }
    }

    /**
     * Render the chart options and returns the javascript that
     * represents them
     *
     * @return string The javascript code
     */
    public function renderOptions() {
        return HighchartOptionRenderer::render($this->options);
    }

    /**
     * Render the chart and returns the javascript that
     * must be printed to the page to create the chart
     *
     * @param string $varName The javascript chart variable name
     * @param string $callback The function callback to pass
     * to the Highcharts.Chart method
     * @param boolean $withScriptTag It renders the javascript wrapped
     * in html script tags
     *
     * @return string The javascript code
     */
    public function render($varName = null, $callback = null) {
        $result = '';
        if (!is_null($varName)) {
            $result = "$varName = ";
        }
        $result .= 'new Highcharts.';
        if ($this->chartType === self::HIGHCHART) {
            $result .= 'Chart(';
        } else {
            $result .= 'StockChart(';
        }
        $result .= $this->renderOptions();
        $result .= is_null($callback) ? '' : ", $callback";
        $result .= ');';

        return $result;
    }

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
    public static function setOptions($options) {
        //TODO: Check encoding errors
        $option = json_encode($options->getValue());
        return "Highcharts.setOptions($option);";
    }

    public function __set($offset, $value) {
        $this->offsetSet($offset, $value);
    }

    public function __get($offset) {
        return $this->offsetGet($offset);
    }

    public function offsetSet($offset, $value) {
        $this->options[$offset] = new HighchartOption($value);
    }

    public function offsetExists($offset) {
        return isset($this->options[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->options[$offset]);
    }

    public function offsetGet($offset) {
        if (!isset($this->options[$offset])) {
            $this->options[$offset] = new HighchartOption();
        }
        return $this->options[$offset];
    }

}
