<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cityware\Format;

/**
 * Description of FieldGrid
 *
 * @author Fabricio
 */
final class FileFolder
{
    public static function filePermission($path)
    {
        $perms = fileperms($path);

        if (($perms & 0xC000) == 0xC000) {
            $info = 's';
        } elseif (($perms & 0xA000) == 0xA000) {
            $info = 'l';
        } elseif (($perms & 0x8000) == 0x8000) {
            $info = '-';
        } elseif (($perms & 0x6000) == 0x6000) {
            $info = 'b';
        } elseif (($perms & 0x4000) == 0x4000) {
            $info = 'd';
        } elseif (($perms & 0x2000) == 0x2000) {
            $info = 'c';
        } elseif (($perms & 0x1000) == 0x1000) {
            $info = 'p';
        } else {
            $info = 'u';
        }

        $info .= (($perms & 0x0100) ? 'r' : '-');
        $info .= (($perms & 0x0080) ? 'w' : '-');
        $info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x' ) : (($perms & 0x0800) ? 'S' : '-'));

        $info .= (($perms & 0x0020) ? 'r' : '-');
        $info .= (($perms & 0x0010) ? 'w' : '-');
        $info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x' ) : (($perms & 0x0400) ? 'S' : '-'));

        $info .= (($perms & 0x0004) ? 'r' : '-');
        $info .= (($perms & 0x0002) ? 'w' : '-');
        $info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x' ) : (($perms & 0x0200) ? 'T' : '-'));

        return $info;
    }

    /**
     * Função para criação das pastas de arquivo no sistema
     * @param  string  $path
     * @param  boolean $recursive
     * @return boolean
     */
    public static function createFolder($path, $chmod = 0755, $recursive = true)
    {
        if (file_exists($path) and is_dir($path)) {
            return true;
        } else {
            return mkdir($path, $chmod, $recursive);
        }
    }

    /**
     * Função para exclusão das pastas de arquivo no sistema
     * @param  string     $path
     * @param  boolean    $recursive
     * @throws \Exception
     */
    public static function removeFolder($path, $recursive = true)
    {
        if (is_dir($path)) {
            if ($recursive) {
                $objects = scandir($path);
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if (filetype($path . DS . $object) == "dir") {
                            self::removeFolder($path . DS . $object);
                        } else {
                            unlink($path . DS . $object);
                        }
                    }
                }
                reset($objects);
                rmdir($path);
            } else {
                rmdir($path);
            }
        }
    }

}
