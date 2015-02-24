<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Cityware\Format;

/**
 * Description of Json
 *
 * @author Fabricio
 */
class Json
{
    /**
     *
     * @param type $indent
     * @param type $linebreak
     */
    public function __construct($indent = '    ', $linebreak = "\n")
    {
        $this->indent_char = $indent;
        $this->linebreak_char = $linebreak;
    }

    /**
     *
     * @param  type $chunk
     * @param  type $already_json
     * @return type
     */
    public function format($chunk, $already_json = false)
    {
        if ($already_json && is_string($chunk))
            $chunk = json_decode($chunk, true);

        if (is_array($chunk)) {
            if ($chunk === array_values($chunk))
                return $this->format_array($chunk);
            else
                return $this->format_hash($chunk);
        } else {
            return json_encode($chunk, true);
        }
    }

    /**
     *
     * @param  array $hash
     * @return type
     */
    protected function format_hash(array $hash)
    {
        $lines = array();
        foreach ($hash as $key => $value) {
            $lines[] = $this->format($key) . ': ' . $this->format($value);
        }

        return $this->format_multiline('{', $lines, '}');
    }

    /**
     *
     * @param  array $hash
     * @return type
     */
    protected function format_array(array $hash)
    {
        $lines = array();
        foreach ($hash as $value) {
            $lines[] = $this->format($value);
        }

        return $this->format_multiline('[', $lines, ']');
    }

    /**
     *
     * @param  type  $startchar
     * @param  array $lines
     * @param  type  $endchar
     * @return type
     */
    protected function format_multiline($startchar, array $lines, $endchar)
    {
        return $startchar . $this->indent($this->linebreak_char . implode(',' . $this->linebreak_char, $lines))
                . $this->linebreak_char . $endchar;
    }

    /**
     *
     * @param  type $text
     * @return type
     */
    protected function indent($text)
    {
        return str_replace($this->linebreak_char, $this->linebreak_char . $this->indent_char, $text);
    }

}
