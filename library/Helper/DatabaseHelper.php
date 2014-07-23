<?php

namespace Util;

/**
 * Provides static methods for database manipulations.
 *
 * @package Util/Helper
 * @copyright Copyright (c) 2014 André Simmert
 * @author André Simmert <contao@simmert.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class DatabaseHelper
{
    public static function duplicateRows($soureTable, $destinationTable=null, array $filter=null, array $replacements=null, array $excludes=null)
    {
        if ($destinationTable === null) {
            $destinationTable = $soureTable;
        }
        
        if ($excludes === null) {
            $excludes = array('id');
        }
        
        $destinationFields = implode(', ', self::getTableFields($soureTable, $excludes));
        $sourceFields = implode(', ', self::getTableFields($soureTable, $excludes, $replacements));

        $query = sprintf(
            'INSERT INTO %s (%s) SELECT %s FROM %s %s',
            $destinationTable, $destinationFields, $sourceFields, $soureTable, self::getWhereFromFilter($filter)
        );
        
        return \Database::getInstance()->prepare($query)->execute($filter)->affectedRows;
    }
    
    
    public static function deleteRows($table, array $filter=null)
    {
        $query = sprintf(
            'DELETE FROM %s %s',
            $table, self::getWhereFromFilter($filter)
        );

        return \Database::getInstance()->prepare($query)->execute($filter)->affectedRows;
    }
    
    
    public static function getTableFields($table, array $excludes=null, array $replacements=null)
    {
        if (!isset($GLOBALS['TL_DCA'][$table]['fields']) || !is_array($GLOBALS['TL_DCA'][$table]['fields'])) {
            throw new \RuntimeException(sprintf('No DCA field definition found for table %s. Try calling $this->loadDataContainer(\'%s\');', $table, $table));
        }

        if (!is_array($excludes)) {
            $excludes = array();
        }
        
        if (!is_array($replacements)) {
            $replacements = array();
        }

        $fields = array();
        foreach ($GLOBALS['TL_DCA'][$table]['fields'] as $name => &$field) {
            if (isset($field['sql']) && trim($field['sql']) != '' && !in_array($name, $excludes)) {
                $fields[] = isset($replacements[$name]) ? $replacements[$name] . ' AS ' . $name : $name;
            }
        }
        
        return $fields;
    }
    
    
    public static function getWhereFromFilter(array $filter=null)
    {
        $where = '';

        if (!is_array($filter) || count($filter) == 0) {
            return '';
        }
        
        foreach ($filter as $field => &$value) {
            $where .= ' ' . $field . ' = ?';
        }
        
        return ' WHERE ' . $where;
    }
}
