<?php

namespace Util;

/**
 * Renders values of parent record field, e.g. for use in translation records
 *
 * @package Util/Widget
 * @copyright Copyright (c) 2013-2014 André Simmert
 * @author André Simmert <contao@simmert.net>
 * @license http://opensource.org/licenses/MIT MIT 
 */
class ParentValue extends \Widget
{
    protected $strTemplate = 'be_widget_chk', // Template without label
              $field,
              $nl2br = false;


    public function generate()
    {
        $template = new \BackendTemplate('be_widget_parentvalue');
        $template->value = $this->getValue();
        
        if ($template->value === null) {
            return '';
        }
        
        if ($this->nl2br) {
            $template->value = nl2br($template->value);
        }

        return $template->parse();
    }
    
    
    protected function getValue()
    {
        $statement = \Database::getInstance()->prepare('SELECT ' . $this->field . ' FROM ' . $this->objDca->parentTable . ' WHERE id = ?');
        $result = $statement->execute($this->activeRecord->pid);
        
        if ($result->numRows == 0) {
            return null;
        }
        
        $record = $result->row();

        return $record[$this->field];
    }
}
