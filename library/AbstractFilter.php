<?php

namespace Util;


/**
 * Bundles fields of a filter within a form
 *
 * @package Util
 * @copyright Copyright (c) 2014 André Simmert
 * @author André Simmert <contao@simmert.net>
 * @license http://opensource.org/licenses/MIT MIT 
 */
abstract class AbstractFilter extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        
        $this->apply();
    }
    
    
    public function getFrontendWidgets()
    {
        return $this->getWidgets();
    }
    
    
    public function getBackendWidgets()
    {
        return $this->getWidgets('BE_FFL');
    }
    
    
    public function getWidgets($context='TL_FFL')
    {
        // Form fields
        $fields = $this->getFields();

        // Initialize the widgets
        $widgets = array();
        foreach ($fields as &$field)
        {
            $class = $GLOBALS[$context][$field['inputType']];

            // Continue if the class is not defined
            if (!class_exists($class)) {
                continue;
            }

            $field['eval']['required'] = $field['eval']['mandatory'];
            $widget = new $class($this->prepareForWidget($field, $field['name'], $field['value']));

            $widgets[$field['name']] = $widget;
        }

        return $widgets;
    }

    
    abstract public function getFields();
    abstract public function getUrlParams($forceOutput=false);
    abstract public function isApplied();
    abstract protected function apply();
}
