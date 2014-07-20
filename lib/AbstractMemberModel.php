<?php

namespace Util;


/**
 * Extendes member model class providing useful capabilties
 *
 * @package Util
 * @copyright Copyright (c) 2014 André Simmert
 * @author André Simmert <contao@simmert.net>
 * @license http://opensource.org/licenses/MIT MIT 
 */
abstract class AbstractMemberModel extends \MemberModel
{
    public static function findAll(array $options=array())
    {
        if (count($options) == 0) {
            $options = array('order' => 'company, lastname, firstname');
        }
        
        return parent::findAll($options);
    }
    
    
    public function getInvoiceAddress()
    {
        return trim(
            $this->company . "\n" .
            $this->firstname . ' ' . $this->lastname . "\n" .
            $this->street . "\n" .
            $this->postal . ' ' . $this->city . "\n" .
            strtoupper($this->getCountry())
        );
    }
    
    
    public function getCountry()
    {
        if ($this->country) {
            return $GLOBALS['TL_LANG']['CNT'][$this->country];
        }
        
        return null;
    }
    
    
    public function getName()
    {
        return $this->firstname . ' ' . $this->lastname;
    }


    public function getLabel()
    {
        $label = $this->company ? $this->company : $this->lastname . ', ' . $this->firstname;
        $city = $this->city ? ' (' . $this->city . ')' : '';

        return $label . $city;
    }
    
    
    public function getDateOfBirth()
    {
        if (!$this->dateOfBirth) {
            return null;
        }

        $dateOfBirth = new \DateTime();
        $dateOfBirth->setTimestamp($this->dateOfBirth);

        return $dateOfBirth;
    }
    
    
    protected static function queryForCollection($sql, $params=array())
    {
        $objStatement = \Database::getInstance()->prepare($sql);
        $objStatement = static::preFind($objStatement);
        $objResult = $objStatement->execute($params);
        $objResult = static::postFind($objResult);
        
        return \Model\Collection::createFromDbResult($objResult, static::$strTable);
    }
}
