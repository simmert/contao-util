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
class MemberModel extends \Contao\MemberModel implements \Util\ModelInterface
{
    public static function findAll(array $options=array())
    {
        if (count($options) == 0) {
            $options = array('order' => 'lastname, firstname');
        }
        
        $GLOBALS['TL_MODELS'][static::getTable()] = get_class();
        return parent::findAll($options);
    }
    
    
    public static function findByFilter(\Util\MemberFilter $filter)
    {
        $where = '';
        $params = array();

        static::appendWhereForDate($filter->getStartDate(), $filter->getEndDate(), $filter->getDateReference(), $where, $params);

        $GLOBALS['TL_MODELS'][static::getTable()] = get_class();
        return static::findBy(array($where), $params, array('order' => $filter->getOrderBy()));
    }
    
    
    public static function findByBirthdayFilter(\Util\DateFilter $filter)
    {
        $where = '';
        $params = array();

        static::appendWhereForBirthday($filter->getStartDate(), $filter->getEndDate(), $where, $params);

        $GLOBALS['TL_MODELS'][static::getTable()] = get_class();
        return static::findBy(array($where), $params, array('order' => 'MONTH(FROM_UNIXTIME(dateOfBirth)), DAY(FROM_UNIXTIME(dateOfBirth))'));
    }

    
    protected static function appendWhereForDate(\DateTime $startDate, \DateTime $endDate, $field, &$where, array &$params)
    {        
        $where .= ($where != '') ? ' AND ' : '';
        $where .= sprintf('(%s >= ? AND %s <= ?)', $field, $field);

        $params[] = $startDate->getTimestamp();
        $params[] = $endDate->getTimestamp();

        return true;
    }
    
    
    protected static function appendWhereForBirthday(\DateTime $startDate, \DateTime $endDate, &$where, array &$params)
    {        
        $where .= ($where != '') ? ' AND ' : '';
        $where .= '(
            dateOfBirth > 0 AND 
            (FLOOR(DATEDIFF(FROM_UNIXTIME(?), FROM_UNIXTIME(dateOfBirth)) / 365.25)) - (FLOOR(DATEDIFF(FROM_UNIXTIME(?), FROM_UNIXTIME(dateOfBirth)) / 365.25)) = 1
        )';

        $params[] = $endDate->getTimestamp();
        $params[] = $startDate->getTimestamp();

        return true;
    }
    
    
    public function toArray()
    {
        $row = $this->row();

        if (!isset($row['label'])) {
            $row['label'] = $this->getLabel();
        }
        
        $row['name']        = $this->getName();
        $row['salutation']  = $this->getSalutation();
        $row['address']     = $this->getInvoiceAddress();
        $row['country']     = $this->getCountry();
        
        return $row;
    }
    
    
    public function getInsertTags()
    {
        return array(
            'user.username'     => $this->username,
            'user.email'        => $this->email,
            'user.name'         => $this->getName(),
            'user.salutation'   => $this->getSalutation(),
            'user.firstname'    => $this->firstname,
            'user.lastname'     => $this->lastname,
            'user.address'      => $this->getInvoiceAddress(),
            'user.country'      => $this->getCountry(),
        );
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
    
    
    public function getSalutation()
    {
        if (intval($this->salutation) == 0) {
            $name = $this->lastname;
        } else {
            $name = $this->firstname;
        }
        
        $gender = ($this->gender) ? $this->gender : 'undefined';
        
        return sprintf($GLOBALS['TL_LANG']['util']['member_model']['salutation'][intval($this->salutation)][$gender], $name);
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
    
    
    public function getAge()
    {
        $dateOfBirth = $this->getDateOfBirth();
        
        if ($dateOfBirth === null) {
            return null;
        }
        
        return $dateOfBirth->diff(new \DateTime());
    }
    
    
    public function getAgeInYears()
    {
        $age = $this->getAge();
        
        if ($age !== null) {
            return intval($age->format('%y'));
        }
        
        return null;
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
    
    
    /**
     * Triggers the preSave method manually if needed.
     */
    public function update()
    {
        $this->setRow($this->preSave($this->row()));
    }
}
