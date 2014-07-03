<?php

namespace Util;

/**
 * Provides static method for array manupulations.
 *
 * @package Util/Helper
 * @copyright Copyright (c) 2013-2014 André Simmert
 * @author André Simmert <contao@simmert.net>
 * @license http://opensource.org/licenses/MIT MIT 
 */
class ArrayHelper
{
    public static function insertAfter(array &$baseArray, $key, array $insertArray)
    {
        $keys = array_keys($baseArray);
        $keyPos = array_search($key, $keys);
    
        $insertPos = $keyPos === false ? count($baseArray) - 1 : $keyPos + 1;
    
        array_insert($baseArray, $insertPos, $insertArray);
    }
}
