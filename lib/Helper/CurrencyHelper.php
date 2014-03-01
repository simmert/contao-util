<?php

namespace Util;

/**
 * Provides static method for currency transformations.
 *
 * @package Util/Helper
 * @copyright Copyright (c) 2013-2014 André Simmert
 * @author André Simmert <contao@simmert.net>
 * @license http://opensource.org/licenses/MIT MIT 
 */
class CurrencyHelper
{
    public static function formatCurrency($value, $currencySymbol='')
    {
        if ($value === null) {
            return null;
        }
        
        if (!trim($currencySymbol)) {
            $currencySymbol = $GLOBALS['TL_CONFIG']['trip_currency_symbol'];
        }

        return $currencySymbol . ' ' . number_format($value, 2, ',' , '.');
    }
    

    public static function net2gross($value, $vat=null)
    {
        return floatval($value) * self::getVat($vat);
    }


    public static function gross2net($value, $vat=null)
    {
        return floatval($value) / self::getVat($vat);
    }
    
    
    public static function getGross($value)
    {
        if ($value !== null && $GLOBALS['TL_CONFIG']['trip_vat_included'] == 0) {
            return self::net2gross($value);
        }

        return $value;
    }


    public static function getNet($value)
    {
        if ($value !== null && $GLOBALS['TL_CONFIG']['trip_vat_included'] == 1) {
            return self::gross2net($value);
        }

        return $value;
    }
    
    
    public static function getVat($vat=null)
    {
        if ($vat === null) {
            $vat = $GLOBALS['TL_CONFIG']['trip_default_vat'];
        }
        
        return intval($vat) / 100 + 1;
    }
}
