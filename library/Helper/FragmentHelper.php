<?php

namespace Util;

/**
 * Static helper functions to assist template fragment tasks
 *
 * @package Util/Helper
 * @copyright Copyright (c) 2013-2014 André Simmert
 * @author André Simmert <contao@simmert.net>
 * @license http://opensource.org/licenses/MIT MIT 
 */
abstract class FragmentHelper
{
    public static function getBackendPreview(array $fragments, $extension, $table, $prefix)
    {
        $fieldName = $extension . '_fragments_' . $prefix;

        $content = '';
        foreach ($fragments as &$fragment) {
            $content .= '– ' . $GLOBALS['TL_LANG'][$table][$fieldName]['names'][$fragment['name']] . '<br />';
        }
        
        return $content;
    }


    public static function addFragmentsToDca($extension, $table, $prefix, $dumpL10n=false)
    {
        $fieldName = $extension . '_fragments_' . $prefix;

        $GLOBALS['TL_DCA'][$table]['fields'][$fieldName] = array(
            'label'                   => &$GLOBALS['TL_LANG'][$table][$extension . '_fragments'],
            'exclude'                 => true,
            'inputType'               => 'multiColumnWizard',
            'sql'                     => "blob NULL",
            'eval'                    => array(
                'tl_class' => 'clr',
                'columnFields' => array(
                    'name' => array(
                        'label'                 => &$GLOBALS['TL_LANG'][$table][$extension . '_fragments_name'],
                        'inputType'             => 'select',
                        'options'               => self::getDcaNames($extension, $table, $prefix, $dumpL10n),
                        'reference'             => &$GLOBALS['TL_LANG'][$table][$fieldName]['names'],
                        'eval'                  => array('style'=>'width:250px')
                    ),
                    'block' => array(
                        'label'                 => &$GLOBALS['TL_LANG'][$table][$extension . '_fragments_block'],
                        'inputType'             => 'select',
                        'options'               => self::getDcaBlocks($extension, $table, $prefix, $dumpL10n),
                        'reference'             => &$GLOBALS['TL_LANG'][$table][$fieldName]['blocks'],
                        'eval'                  => array('style'=>'width:180px')
                    )
                ),
            ),
        );
    }
    
    
    public static function getDcaNames($extension, $table, $prefix, $dumpL10n=false)
    {
        return self::getDcaOptions('name', $extension, $table, $prefix, $dumpL10n);
    }


    public static function getDcaBlocks($extension, $table, $prefix, $dumpL10n=false)
    {
        return self::getDcaOptions('block', $extension, $table, $prefix, $dumpL10n);
    }


    public static function getDcaOptions($type, $extension, $table, $prefix, $dumpL10n=false)
    {
        $fieldName = $extension . '_fragments_' . $prefix;
        $templatePrefix = (($type == 'name') ? 'fragment' : $type) . '_' . $extension . '_' . $prefix;

        $options = \TemplateLoader::getPrefixedFiles($templatePrefix);

        foreach ($options as &$option) {
            $option = substr($option, strlen($templatePrefix)+1);
            
            if ($dumpL10n) {
                $label = ucfirst(str_replace('_', ' ', $option));
                echo '$GLOBALS[\'TL_LANG\'][\'' . $table . '\'][\'' . $fieldName . '\'][\'' . $type . 's\'][\'' . $option . '\'] = \'' . $label . '\';' . "\n";
            }
        }
        
        return $options;
    }
}
