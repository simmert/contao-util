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
abstract class AbstractFilter extends \Controller implements \Util\FilterInterface
{
    protected $session = null,
              $orderBy = 'pid, id';


    public function __construct()
    {
        parent::__construct();

        $this->session = new \Util\NamespacedSession($this);

        if ($this->isApplied()) {
            $this->apply();
        } else {
            $this->reset();
        }
    }


    public function get($key)
    {
        if (\Input::get($key)) {
            return \Input::get($key);
        }

        return $this->session->get($key);
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
        foreach ($fields as $name => &$field)
        {
            $field['name'] = $name;
            $class = $GLOBALS[$context][$field['inputType']];

            // Continue if the class is not defined
            if (!class_exists($class)) {
                continue;
            }

            $field['eval']['required'] = $field['eval']['mandatory'];
            $widget = new $class($this->prepareForWidget($field, $name, $field['value']));

            if (isset($field['default'])) {
                $widget->default = $field['default'];
            }

            $widgets[$name] = $widget;
        }

        return $widgets;
    }


    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;
    }


    public function getOrderBy()
    {
        return $this->orderBy;
    }


    public function reset()
    {
        $this->session->reset();
    }

    abstract public function getFields();
    abstract public function getUrlParams($forceOutput=false);
    abstract public function isApplied();
    abstract protected function apply();
}
