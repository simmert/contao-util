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
                     $vat            = 19;


    public static function formatCurrency($value, $currencySymbol=null)
    {
        if ($value === null) {
            return null;
        }
        
        if ($currencySymbol === null) {
            $currencySymbol = static::$currencySymbol;
        }

        return $currencySymbol . '&nbsp;' . number_format($value, 2, ',' , '.');
    }
    

    public static function net2gross($value, $vat=null)
    {
        return floatval($value) * self::getVat($vat);
    }


    public static function gross2net($value, $vat=null)
    {
        return floatval($value) / self::getVat($vat);
    }
    
    
    public static function getGross($value, $vat=null, $vatIncluded=null)
    {
        if ($vatIncluded === null) {
            $vatIncluded = static::$vatIncluded;
        }

        if ($value !== null && !$vatIncluded) {
            return self::net2gross($value, $vat);
        }

        return $value;
    }


    public static function getNet($value, $vat=null, $vatIncluded=null)
    {
        if ($vatIncluded === null) {
            $vatIncluded = static::$vatIncluded;
        }

        if ($value !== null && $vatIncluded) {
            return self::gross2net($value, $vat);
        }

        return $value;
    }
    
    
    public static function getVat($vat=null)
    {
        if ($vat === null) {
            $vat = static::$vat;
        }
        
        return intval($vat) / 100 + 1;
    }
    
    
    public static function getLabelConsideringVat(&$labelSet, $currencySymbol=null, $vatIncluded=null)
    {
        if (!is_array($labelSet)) {
            return $labelSet;
        }
        
        if ($currencySymbol === null) {
            $currencySymbol = static::$currencySymbol;
        }
        
        if ($vatIncluded === null) {
            $vatIncluded = static::$vatIncluded;
        }

        if ($vatIncluded) {
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
