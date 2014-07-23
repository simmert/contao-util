<?php

namespace Util;

/**
 * Provides static methods for DCA transformations.
 *
 * @package Util/Helper
 * @copyright Copyright (c) 2013-2014 André Simmert
 * @author André Simmert <contao@simmert.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class DcaHelper
{
    public static function generateSelectOptions(\Model\Collection $recordCollection=null, \Model\Collection $categoryCollection=null)
    {
        $categories = $options = array();
        
        if ($recordCollection === null) {
            return $options;
        }
        
        while ($categoryCollection !== null && $categoryCollection->next()) {
            $categories[$categoryCollection->id] = $categoryCollection->current()->getLabel();
        }

        if ($categoryCollection === null) {
            while ($recordCollection->next()) {
                $options[$recordCollection->id] = $recordCollection->current()->getLabel();
            }
        } else {
            while ($recordCollection->next()) {
                $record = $recordCollection->current();
                
                if (!isset($options[$categories[$record->pid]])) {
                    $options[$categories[$record->pid]] = array();
                }
                
                $options[$categories[$record->pid]][$record->id] = $record->getLabel();
            }
        }

        return $options;
    }
}
