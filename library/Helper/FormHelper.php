<?php

namespace Util;

/**
 * Provides static methods for form generation/validation
 *
 * @package Util/Helper
 * @copyright Copyright (c) 2013-2014 André Simmert
 * @author André Simmert <contao@simmert.net>
 * @license http://opensource.org/licenses/MIT MIT 
 */
class FormHelper
{
    public static function validateFormWidgets(array &$widgets)
    {
        $validationSuccessful = true;

        foreach ($widgets as $name => $widget) {
    		$widget->validate();
    		if ($widget->hasErrors()) {
    			$validationSuccessful = false;
    		}
        }
        
        return $validationSuccessful;
    }
    
    
    public static function generateSelectOptions(\Model\Collection $recordCollection=null, \Model\Collection $categoryCollection=null, $addEmptyOption=false)
    {
        $selectOptions = array();
        $dcaOptions = DcaHelper::generateSelectOptions($recordCollection, $categoryCollection);
        
        if ($addEmptyOption) {
            $selectOptions[] = array('group' => false, 'value' => '', 'label' => '–');
        }

        foreach ($dcaOptions as $category => &$options) {
            $selectOptions[] = array('group' => true, 'label' => $category);
            
            foreach ($options as $value => &$label) {
                $selectOptions[] = array('group' => false, 'value' => $value, 'label' => $label);
            }
        }
        
        return $selectOptions;
    }
}
