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
              $multiple         = false,
              $items            = array(),
              $itemWrapCssClass = '',
              $itemScope        = null,
              $fragments        = array(),
              $blockContent     = '',
              $fragmentContent  = '';


    public function parse($parseFragments=true)
    {
        if ($parseFragments) {
            if ($this->multiple) {
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
        
        // Wrap item if multiple items are rendered
        if ($item !== null) {
            $this->fragmentContent .= '<article';
        }

        if (trim($cssClass) != '') {
            $this->fragmentContent .= ' class="' . trim($cssClass) . '"';
        }
        
        if (isset($item['scope'])) {
            $this->fragmentContent .= ' itemscope itemtype="' . trim($item['scope']) . '"';
        } else if ($this->itemScope !== null) {
            $this->fragmentContent .= ' itemscope itemtype="' . trim($this->itemScope) . '"';
        }
        
        // Close article tag
        if ($item !== null) {
            $this->fragmentContent .= '>';
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
        if ($item !== null) {
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
    
    
    public function setMultipleItems(array $items, $itemWrapCssClass='', $itemScope=null)
    {
        $this->multiple = true;
        $this->items = $items;
        $this->itemWrapCssClass = $itemWrapCssClass;
        $this->itemScope = $itemScope;
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
