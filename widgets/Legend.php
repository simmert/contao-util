<?php

namespace Util;

/**
 * Renders a fake legend separator in backend forms
 *
 * @package Util/Widget
 * @copyright Copyright (c) 2013-2014 André Simmert
 * @author André Simmert <contao@simmert.net>
 * @license http://opensource.org/licenses/MIT MIT 
 */
class Legend extends \Widget
{
    protected $strTemplate = 'be_widget_chk', // Template without label
              $legend;


    public function generate()
    {
        $template = new \BackendTemplate('be_widget_legend');
        $template->legend = $this->legend;

        return $template->parse();
    }
}
