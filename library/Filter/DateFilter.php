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
class DateFilter extends \Util\AbstractFilter implements \Util\DateFilterInterface
{
    protected $startDate     = null,
              $endDate       = null,
              $dateReference = 'date';


    public function isApplied()
    {
        return ($this->get('startDate') && $this->get('endDate'));
    }


    protected function apply()
    {
        $startDate = new \DateTime($this->get('startDate'));
        $endDate = new \DateTime($this->get('endDate'));

        if ($startDate !== false && $endDate !== false && $startDate <= $endDate) {
            $this->setStartDate($startDate);
            $this->setEndDate($endDate);
        }
    }


    public function getUrlParams($forceOutput=false)
    {
        if (!$forceOutput && !$this->isApplied()) {
            return null;
        }

        return array(
            'startDate' => $this->getStartDate() !== null ? $this->getStartDate()->format($GLOBALS['TL_CONFIG']['dateFormat']) : '',
            'endDate'   => $this->getEndDate() !== null ? $this->getEndDate()->format($GLOBALS['TL_CONFIG']['dateFormat']) : '',
        );
    }


    public function getFields()
    {
        return array(
            'startDate' => array(
                'label'     => &$GLOBALS['TL_LANG']['util']['filter']['startDate'],
                'inputType' => 'text',
                'value'     => $this->getStartDate()->format($GLOBALS['TL_CONFIG']['dateFormat']),
            ),
            'endDate' => array(
                'label'     => &$GLOBALS['TL_LANG']['util']['filter']['endDate'],
                'inputType' => 'text',
                'value'     => $this->getEndDate()->format($GLOBALS['TL_CONFIG']['dateFormat']),
            ),
        );
    }


    public function reset()
    {
        parent::reset();

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
        $this->session->set('startDate', $startDate->format($GLOBALS['TL_CONFIG']['dateFormat']));
    }


    public function getStartDate()
    {
        return $this->startDate;
    }


    public function setEndDate(\DateTime $endDate)
    {
        $this->endDate = $endDate;
        $this->session->set('endDate', $endDate->format($GLOBALS['TL_CONFIG']['dateFormat']));
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
