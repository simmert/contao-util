<?php

namespace Util;

/**
* Various helper functions for strings
*/
class StringHelper
{
    public static function startsWith($haystack, $needle)
    {
        // search backwards starting from haystack length characters from the end
        return $needle === '' || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }

    public static function endsWith($haystack, $needle)
    {
        // search forward starting from end minus needle length characters
        return $needle === '' || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
    }
    
    
    public static function sanitizeForFileSystem($string)
    {
        // Remove anything which isn't a word, whitespace, number
        // or any of the following caracters -_~[]().
        $string = preg_replace("([^\w\s\d\-_~\[\]\(\).])", '', $string);
        
        // Remove any runs of periods
        return preg_replace("([\.]{2,})", '', $string);
    }
}
