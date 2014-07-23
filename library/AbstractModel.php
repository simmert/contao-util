<?php

namespace Util;

/**
 * Extendes model class providing translation capabilties
 *
 * @package Util
 * @copyright Copyright (c) 2013-2014 André Simmert
 * @author André Simmert <contao@simmert.net>
 * @license http://opensource.org/licenses/MIT MIT 
 */
abstract class AbstractModel extends \Model
{
    protected static $translatable = false,
                     $translations = array();
    
    
    public function __construct(\Database\Result $objResult=null)
    {        
        parent::__construct($objResult);
        
        if (static::$translatable && isset(static::$translations[$this->id])) {
            $this->translate(static::$translations[$this->id]);
        }
    }


    public function toArray()
    {
        return $this->row();
    }
    
    
    public function getLabel()
    {
        return $this->id;
    }
    
    
    public function generateHash()
    {
        $row = $this->row();
        
        // Remove fields that do not represent data changes
        $metaFields = $this->getMetaFields();
        
        foreach ($metaFields as &$field) {
            if (isset($row[$field])) {
                unset($row[$field]);
            }
        }
        
        return crc32(serialize($row));
    }
    
    
    /**
     * Return array of fields that do not represent model changes
     */
    protected function getMetaFields()
    {
        return array('tstamp', 'hash');
    }
    
    
    protected function translate(array &$translation)
    {
        foreach ($translation as $field => $value) {
            if ($field == 'fallback') {
                continue;
            }

            if (!$translation['fallback'] || trim($value) != '') {
                $this->$field = $value;
            }
        }
    }
    
    
	protected function preSave(array $arrSet)
	{
        if (TL_MODE == 'FE' && static::$translatable) {
            throw new \LogicException('Trying to save a translatable model. This may result in overwriting the default language.');
        }

		return $arrSet;
	}
    
    
    protected static function postFind(\Database\Result $objResult)
    {
        if (TL_MODE == 'FE' && static::$translatable) {
            return static::fetchTranslations($objResult);
        }
        
        return $objResult;
    }
    
    
    protected static function fetchTranslations(\Database\Result $result)
    {
        if ($result->count() == 0 || $GLOBALS['TL_LANGUAGE'] == $GLOBALS['TL_CONFIG']['default_language']) {
            return $result;
        }
        
        $ids = $result->fetchEach('id');
        $result->reset();
        
        $translationStatement = \Database::getInstance()->prepare('
            SELECT *
            FROM ' . static::$strTable . '_translation
            WHERE pid IN (' . implode(',', $ids) . ') AND language = ?
        ');

        $translationResult = $translationStatement->execute(array(
            $GLOBALS['TL_LANGUAGE']
        ));
        
        if ($translationResult->count() == 0) {
            return $result;
        }

        while (($row = $translationResult->fetchAssoc()) !== false) {
            static::$translations[$row['pid']] = $row;
            unset(static::$translations[$row['pid']]['id']);
            unset(static::$translations[$row['pid']]['pid']);
            unset(static::$translations[$row['pid']]['tstamp']);
            unset(static::$translations[$row['pid']]['language']);
        }

        return $result;
    }
    
    
    protected static function queryForCollection($sql, $params=array())
    {
        $objResult = self::queryForResult($sql, $params);

        return \Model\Collection::createFromDbResult($objResult, static::$strTable);
    }
    
    
    protected static function queryForArray($sql, $params=array())
    {
        $objResult = self::queryForResult($sql, $params);

        return $objResult->fetchAllAssoc();
    }
    
    
    protected static function queryForResult($sql, $params=array())
    {
        $objStatement = \Database::getInstance()->prepare($sql);
        $objStatement = static::preFind($objStatement);
        $objResult = $objStatement->execute($params);
        
        return static::postFind($objResult);
    }
}
