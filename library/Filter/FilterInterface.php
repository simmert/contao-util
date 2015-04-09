<?php

namespace Util;

interface FilterInterface
{
    public function get($key);
    public function getFrontendWidgets();
    public function getBackendWidgets();
    public function getWidgets($context);
    public function setOrderBy($orderBy);
    public function getOrderBy();
    public function reset();
    public function getFields();
    public function getUrlParams($forceOutput);
    public function isApplied();
}
