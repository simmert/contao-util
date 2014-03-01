<?php

namespace Util;

/**
 * A frontend template consisting of multiple sub-templates i.e. fragments
 *
 * @package Util
 * @copyright Copyright (c) 2013-2014 André Simmert
 * @author André Simmert <contao@simmert.net>
 * @license http://opensource.org/licenses/MIT MIT 
 */
class FragmentTemplate extends \FrontendTemplate
{
    protected $extension        = '',
              $prefix           = '',
              $items            = array(),
              $itemWrapCssClass = '',
              $fragments        = array(),
              $blockContent     = '',
              $fragmentContent  = '';


    public function parse($parseFragments=true)
    {
        if ($parseFragments) {
            if (count($this->items) != 0) {
                $parentData = $this->arrData;
                foreach ($this->items as &$item) {
                    $this->parseFragments($item);
                }
                $this->arrData = $parentData;
            } else {
                $this->parseFragments();
            }
        }

        return parent::parse();
    }
    
    
    protected function parseFragments(array $item = null)
    {
        $mainTemplate = $this->strTemplate;
        $cssClass = $this->itemWrapCssClass;

        if ($item === null) {
            $this->fragmentContent = '';
        } else {
            $this->setData($item);
            
            if (isset($item['cssClass'])) {
                $cssClass .= ' ' . $item['cssClass'];
            }
        }
        
        // Wrap item in class
        if (trim($cssClass) != '') {
            $this->fragmentContent .= '<article class="' . trim($cssClass) . '">';
        }

        foreach ($this->fragments as $block => &$fields) {
            $this->blockContent = '';
            foreach ($fields as &$field) {
                $this->setName('fragment_' . $this->extension . '_' . $this->prefix . '_' . $field);
                $this->blockContent .= $this->parse(false);
            }
            
            $this->setName('block_' . $this->extension . '_' . $this->prefix . '_' . $block);
            $this->fragmentContent .= $this->parse(false);
            
            $this->setName($mainTemplate);
        }
        
        // Close item wrap
        if (trim($cssClass) != '') {
            $this->fragmentContent .= '</article>';
        }
    }
    

    public function setFragments(array $fragments)
    {
        foreach ($fragments as &$fragment) {
            $this->addFragment($fragment['name'], $fragment['block']);
        }
    }
    
    
    public function addFragment($name, $block='')
    {
        if (!trim($block)) {
            $block = 'default';
        }
        
        if (!isset($this->fragments[$block])) {
            $this->fragments[$block] = array();
        }
        
        $this->fragments[$block][] = $name;
    }
    
    
    public function setMultipleItems(array $items, $itemWrapCssClass='')
    {
        $this->items = $items;
        $this->itemWrapCssClass = $itemWrapCssClass;
    }


    public function setExtension($extension)
    {
        $this->extension = $extension;
    }


    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }
}
