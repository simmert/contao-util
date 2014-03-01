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
    protected static $currencySymbol = 'EUR',
                     $vatIncluded    = false,
                     $defaultVat     = 19;


    public static function formatCurrency($value, $currencySymbol=null)
    {
        if ($value === null) {
            return null;
        }
        
        if ($currencySymbol === null) {
            $currencySymbol = static::$currencySymbol;
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
        if ($value !== null && !static::$vatIncluded) {
            return self::net2gross($value);
        }

        return $value;
    }


    public static function getNet($value)
    {
        if ($value !== null && static::$vatIncluded) {
            return self::gross2net($value);
        }

        return $value;
    }
    
    
    public static function getVat($vat=null)
    {
        if ($vat === null) {
            $vat = static::$defaultVat;
        }
        
        return intval($vat) / 100 + 1;
    }
    
    
    public static function getLabelConsideringVat(&$labelSet, $currencySymbol=null)
    {
        if (!is_array($labelSet)) {
            return $labelSet;
        }
        
        if ($currencySymbol === null) {
            $currencySymbol = static::$currencySymbol;
        }

        if (static::$vatIncluded) {
            $vatLabel = &$GLOBALS['TL_LANG']['util']['helper']['currency']['vat_included'];
        } else {
            $vatLabel = &$GLOBALS['TL_LANG']['util']['helper']['currency']['vat_excluded'];
        }
        
        foreach ($labelSet as &$label) {
            $label = sprintf($label, $currencySymbol, $vatLabel);
        }
        
        return $labelSet;
    }
}
