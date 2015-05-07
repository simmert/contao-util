<?php

namespace Util;

/*
 * An interface for models.
 */
interface ModelInterface
{
    public function __construct(\Database\Result $objResult);
    public function toArray();
    public function getLabel();
    public function update();
}
