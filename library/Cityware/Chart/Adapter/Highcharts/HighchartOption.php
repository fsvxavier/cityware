<?php

namespace Cityware\Chart\Adapter\Highcharts;

/**
 *
 * Copyright 2012-2012 Portugalmail Comunicações S.A (http://www.portugalmail.net/)
 *
 * See the enclosed file LICENCE for license information (GPLv3). If you
 * did not receive this file, see http://www.gnu.org/licenses/gpl-3.0.html.
 *
 * @author Gonçalo Queirós <mail@goncaloqueiros.net>
 */
class HighchartOption implements \ArrayAccess {

    /**
     * An array of HighchartOptions
     *
     * @var array
     */
    private $childs = array();

    /**
     * The option value
     *
     * @var mixed
     */
    private $value;

    /**
     * Clone HighchartOption object
     */
    public function __clone() {
        foreach ($this->childs as $key => $value) {
            $this->childs[$key] = clone $value;
        }
    }

    /**
     * The HighchartOption constructor
     *
     * @param mixed $value The option value
     */
    public function __construct($value = null) {
        if (is_string($value)) {
            //Avoid json-encode errors latter on
            $this->value = iconv(mb_detect_encoding($value), "UTF-8", $value);
        } else if (!is_array($value)) {
            $this->value = $value;
        } else {
            foreach ($value as $key => $val) {
                $this->offsetSet($key, $val);
            }
        }
    }

    /**
     * Returns the value of the current option
     *
     * @return mixed The option value
     */
    public function getValue() {
        if (isset($this->value)) {
            //This is a final option
            return $this->value;
        } elseif (!empty($this->childs)) {
            //The option value is an array
            $result = array();
            foreach ($this->childs as $key => $value) {
                $result[$key] = $value->getValue();
            }
            return $result;
        }
        return null;
    }

    public function __set($offset, $value) {
        $this->offsetSet($offset, $value);
    }

    public function __get($offset) {
        return $this->offsetGet($offset);
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->childs[] = new self($value);
        } else {
            $this->childs[$offset] = new self($value);
        }
        //If the option has at least one child, then it won't
        //have a final value
        unset($this->value);
    }

    public function offsetExists($offset) {
        return isset($this->childs[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->childs[$offset]);
    }

    public function offsetGet($offset) {
        //Unset the value, because we will always
        //have at least one child at the end of
        //this method
        unset($this->value);
        if (is_null($offset)) {
            $this->childs[] = new self();
            return end($this->childs);
        }
        if (!isset($this->childs[$offset])) {
            $this->childs[$offset] = new self();
        }
        return $this->childs[$offset];
    }

}
