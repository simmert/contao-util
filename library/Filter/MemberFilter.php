<?php

namespace Util;


/**
 * Filter for members
 *
 * @package Util
 * @copyright Copyright (c) 2015 André Simmert
 * @author André Simmert <contao@simmert.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class MemberFilter extends \Util\DateFilter implements \Util\MemberFilterInterface
{
    protected $orderBy       = 'lastname, firstname',
              $dateReference = 'dateOfBirth';

    // TODO: Add more filter options for members
}
