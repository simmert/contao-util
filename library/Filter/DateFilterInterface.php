<?php

namespace Util;

interface DateFilterInterface extends \Util\FilterInterface
{
    public function setStartDate(\DateTime $startDate);
    public function getStartDate();
    public function setEndDate(\DateTime $endDate);
    public function getEndDate();
    public function setDateReference($dateReference);
    public function getDateReference();
}
