<?php

namespace Util;

/**
 * Static helper functions to assist with various stuff
 *
 * @package Util/Helper
 * @copyright Copyright (c) 2013-2014 André Simmert
 * @author André Simmert <contao@simmert.net>
 * @license http://opensource.org/licenses/MIT MIT 
 */
class GeneralHelper
{
    public static function replaceInsertTags(array $tagsValues, $string)
    {
        $tags = $values = array();
        foreach ($tagsValues as $tag => $value) {
            $tags[] = '|' . $tag . '|';
            $values[] = $value;
        }
        
        return str_replace($tags, $values, $string);
    }
    
    
    public static function parseCollection(\Contao\Model\Collection $collection=null, array $callback=null)
    {
        $parsedElements = array();
        
        while ($collection !== null && $collection->next()) {
            if ($callback === null) {
                $parsedElements[] = $collection->current()->toArray();
            } else {
                $parsedElements[] = call_user_func($callback, $collection->current());
            }
        }
        
        self::addCssClassToListItems($parsedElements);
        
        return $parsedElements;
    }
    
    
    public static function parseTableWizdardData($data)
    {
        if (trim($data) == '') {
            return null;
        }

        $data = deserialize($data);

        if (!is_array($data) || !isset($data[0]) || trim($data[0][0]) == '') {
            return null;
        }
        
        $rowCount = count($data);
        $colCount = count($data[0]);
        
        $parsedData = array();
        for ($i=0; $i<$rowCount; $i++) {
            $parsedData[$i] = array(
                'class' => 'row_' . $i,
                'cols' => array()
            );
            
            if ($i == 0) {
                $parsedData[$i]['class'] .= ' row_first';
            }
            
            if ($i == $rowCount-1) {
                $parsedData[$i]['class'] .= ' row_last';
            }
            
            $parsedData[$i]['class'] .= ($i%2) ? ' odd' : ' even';
            
            for ($j=0; $j<$colCount; $j++) {
                $parsedData[$i]['cols'][$j] = array(
                    'class' => 'col_' . $j,
                    'content' => $data[$i][$j]
                );

                if ($j == 0) {
                    $parsedData[$i]['cols'][$j]['class'] .= ' col_first';
                }

                if ($j == $colCount-1) {
                    $parsedData[$i]['cols'][$j]['class'] .= ' col_last';
                }
            }
        }
        
        return $parsedData;
    }
    
    
    public static function generateNavigationMarkup(array $items, $activeId=null, $level=1)
    {
        $itemCount = count($items);

        if ($itemCount == 0) {
            return '';
        }

        $markup = '<ul class="level_' . $level . '">';

        for ($i=0; $i<$itemCount; $i++) {
            $item = &$items[$i];
            $classes = array();

            if ($item['id'] == $activeId) {
                $classes[] = 'active';
            }

            if (isset($item['children'])) {
                $classes[] = 'submenu';
            }
            
            if (self::childIsActive($item, $activeId)) {
                $classes[] = 'trail';
            }

            if ($i == 0) {
                $classes[] = 'first';
            }
            
            if ($i == $itemCount-1) {
                $classes[] = 'last';
            }
            
            $classString = implode(' ', $classes);

            $markup .= '<li class="' . $classString . '">';
            
            if ($item['id'] == $activeId) {
                $markup .= '<span class="' . $classString . '">' . $item['label'] . '</span>';
            } else {
                $markup .= '<a class="' . $classString . '" href="' . $item['url'] . '" title="' . $item['label'] . '">' . $item['label'] . '</a>';
            }

            if (isset($item['children'])) {
                $markup .= self::generateNavigationMarkup($item['children'], $activeId, $level+1);
            }

            $markup .= '</li>';
        }

        $markup .= '</ul>';

        return $markup;
    }
    
    
    protected static function childIsActive(array &$item, $activeId)
    {
        if (!isset($item['children']) || !is_array($item['children'])) {
            return false;
        }
        
        foreach ($item['children'] as &$child) {
            if ($child['id'] == $activeId || self::childIsActive($child, $activeId)) {
                return true;
            }
        }
        
        return false;
    }
    
    
    public static function addCssClassToListItems(array &$items, $prefix='', $fieldName='cssClass')
    {
        $itemCount = count($items);
        $i = 0;
        foreach ($items as &$item) {
            $item[$fieldName] = ($i++ % 2 == 0) ? $prefix . 'odd' : $prefix . 'even';

            if ($i == 1) {
                $item[$fieldName] .= ' ' . $prefix . 'first';
            }
            
            if ($i == $itemCount) {
                $item[$fieldName] .= ' ' . $prefix . 'last';
            }
        }
    }
}
