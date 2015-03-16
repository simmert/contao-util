<?php

namespace Util;


/**
 * Filter for dates
 *
 * @package Util
 * @copyright Copyright (c) 2015 André Simmert
 * @author André Simmert <contao@simmert.net>
 * @license http://opensource.org/licenses/MIT MIT 
 */
class DateFilter extends \Util\AbstractFilter
{
    protected $startDate     = null,
              $endDate       = null,
              $dateReference = 'date';


    protected function apply()
    {
        $this->reset();

        if (!$this->isApplied()) {
            return;
        }

        $startDate = new \DateTime(\Contao\Input::get('startDate'));
        $endDate = new \DateTime(\Contao\Input::get('endDate'));

        if ($startDate !== false && $endDate !== false && $startDate <= $endDate) {
            $this->startDate = $startDate;
            $this->endDate = $endDate;
        }
    }
    
    
    public function isApplied()
    {
        return (\Input::get('startDate') && \Input::get('endDate'));
    }
    
    
    public function getUrlParams($forceOutput=false)
    {
        if (!$forceOutput && !$this->isApplied()) {
            return null;
        }

        return array(
            'startDate' => $this->startDate !== null ? $this->startDate->format($GLOBALS['TL_CONFIG']['dateFormat']) : '',
            'endDate'   => $this->endDate !== null ? $this->endDate->format($GLOBALS['TL_CONFIG']['dateFormat']) : '',
        );
    }


    public function getFields()
    {
        return array(
            'startDate' => array(
                'label'     => &$GLOBALS['TL_LANG']['util']['filter']['startDate'],
                'inputType' => 'text',
                'value'     => $this->startDate->format($GLOBALS['TL_CONFIG']['dateFormat']),
            ),
            'endDate' => array(
                'label'     => &$GLOBALS['TL_LANG']['util']['filter']['endDate'],
                'inputType' => 'text',
                'value'     => $this->endDate->format($GLOBALS['TL_CONFIG']['dateFormat']),
            ),
        );
    }
    
    
    public function reset()
    {
        $startDate = new \DateTime();
        $endDate = clone $startDate;
        
        $range = new \DateInterval('P1D'); // Range of one day
        $endDate->add($range);

        $this->setStartDate($startDate);
        $this->setEndDate($endDate);
    }
    
    
    public function setStartDate(\DateTime $startDate)
    {
        $this->startDate = $startDate;
    }
    

    public function getStartDate()
    {
        return $this->startDate;
    }


    public function setEndDate(\DateTime $endDate)
    {
        $this->endDate = $endDate;
    }
    
    
    public function getEndDate()
    {
        return $this->endDate;
    }


    public function setDateReference($dateReference)
    {
        $this->dateReference = $dateReference;
    }
    
    
    public function getDateReference()
    {
        return $this->dateReference;
    }
}
