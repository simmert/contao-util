<?php

namespace Util;

/**
 * Extended content element with useful helper methods
 *
 * @package Util
 * @copyright Copyright (c) 2013-2014 André Simmert
 * @author André Simmert <contao@simmert.net>
 * @license http://opensource.org/licenses/MIT MIT 
 */
abstract class ContentElement extends \ContentElement
{
    protected $session          = null,
              $placeholderImage = '';


    public function generate()
    {
        $this->session = \Session::getInstance();

        return parent::generate();
    }
    
    
    protected function parseCollection(\Contao\Model\Collection $collection=null, array $callback=null)
    {
        $parsedElements = array();
        
        while ($collection !== null && $collection->next()) {
            if ($callback === null) {
                $parsedElements[] = $collection->current()->toArray();
            } else {
                $parsedElements[] = call_user_func($callback, $collection->current());
            }
        }
        
        GeneralHelper::addCssClassToListItems($parsedElements);
        
        return $parsedElements;
    }


    protected function parseImage($fileId, $size=null)
    {
        $imageData = $this->loadImage($fileId, $size);
        
        if ($imageData === null) {
            return null;
        }

        return $this->getImageContainer($imageData);
    }


    protected function parseImages($fileIds, $size=null)
    {
        $imageData = $this->loadImages($fileIds, $size);

        $images = array();
        foreach ($imageData as $num => &$image) {
            $images[] = $this->getImageContainer($image, $num);
        }

        return $images;
    }
    

    protected function loadImage($fileId, $size)
    {
        global $objPage;

        // Set placeholder image if any and empty
        if (!$fileId && $this->placeholderImage == '') {
            return null;
        } else if (!$fileId && $this->placeholderImage != '') {
            $fileId = $this->placeholderImage;
        }
        
        $image = \FilesModel::findByPk($fileId);

        if ($image === null) {
            return null;
        }

        if ($image->type != 'file' || !file_exists(TL_ROOT . '/' . $image->path)) {
            return null;
        }

        $imageFile = new \File($image->path, true);

        if (!$imageFile->isGdImage) {
            return null;
        }

        $arrMeta = $this->getMetaData($image->meta, $objPage->language);

        // Use the file name as title if none is given
        if ($arrMeta['title'] == '') {
            $arrMeta['title'] = specialchars(str_replace('_', ' ', preg_replace('/^[0-9]+_/', '', $imageFile->filename)));
        }

        // Add the image
        $imageData = array(
            'id'        => $image->id,
            'name'      => $imageFile->basename,
            'singleSRC' => $image->path,
            'alt'       => $arrMeta['title'],
            'imageUrl'  => $arrMeta['link'],
            'caption'   => $arrMeta['caption'],
            'size'      => $size === null ? $this->size : $size,
            'fullsize'  => $this->fullsize
        );
        
        return $imageData;
    }

    
    protected function loadImages($fileIds, $size)
    {
        global $objPage;

        $images = array();
        $meta = null;

        // Set placeholder image if any and empty
        if (!$fileIds && $this->placeholderImage == '') {
            return $images;
        } else if (!$fileIds && $this->placeholderImage != '') {
            $imageIds = array($this->placeholderImage);
        } else {
            $imageIds = deserialize($fileIds);
        }

        // Fetch meta data if any (due to MultiImageManagementWidget)
        if (isset($imageIds['meta'])) {
            $meta = $imageIds['meta'];
            unset($imageIds['meta']);
        }

        $imageCollection = \FilesModel::findMultipleByIds($imageIds);
        
        if ($imageCollection === null) {
            return $images;
        }

        // Get all images
        while ($imageCollection->next())
        {
            // Continue if the files has been processed or does not exist
            if (isset($images[$imageCollection->path]) || $imageCollection->type != 'file' || !file_exists(TL_ROOT . '/' . $imageCollection->path)) {
                continue;
            }

            $image = new \File($imageCollection->path, true);

            if (!$image->isGdImage) {
                continue;
            }

            if ($meta !== null && isset($meta[$imageCollection->id])) {
                // Meta data from MultiImageManagementWidget
                $arrMeta = $meta[$imageCollection->id];
            } else {
                // Default Meta data
                $arrMeta = $this->getMetaData($imageCollection->meta, $objPage->language);
            }

            // Use the file name as title if none is given
            if ($arrMeta['title'] == '') {
                $arrMeta['title'] = specialchars(str_replace('_', ' ', preg_replace('/^[0-9]+_/', '', $image->filename)));
            }

            // Add the image
            $images[$image->path] = array(
                'id'        => $imageCollection->id,
                'name'      => $image->basename,
                'singleSRC' => $imageCollection->path,
                'alt'       => $arrMeta['title'],
                'imageUrl'  => $arrMeta['link'],
                'caption'   => $arrMeta['caption'],
                'size'      => $size === null ? $this->size : $size,
                'fullsize'  => $this->fullsize
            );
        }
        
        return array_values($images);
    }


    protected function getImageContainer(array $image, $num=0)
    {
        $strLightboxId = 'lightbox[lb' . $this->id . ']';
        $imageContainer = new \stdClass();
        $imageContainer->class = 'image_' . $num;

        $this->addImageToTemplate($imageContainer, $image, $GLOBALS['TL_CONFIG']['maxImageWidth'], $strLightboxId);
        
        return $imageContainer;
    }

    
    protected function setMetaData(array &$object, array $insertTags=null, $customTitle)
    {
        global $objPage;

        // Set the page’s title
        if ($insertTags !== null && $customTitle) {
            $objPage->pageTitle = GeneralHelper::replaceInsertTags($insertTags, $customTitle);
        } else {
            $objPage->pageTitle = strip_tags(strip_insert_tags($object['title']));
        }

        $GLOBALS['TL_KEYWORDS'] = $object['meta_keywords'];
        $objPage->description = $object['meta_description'];
    }
    
    
    protected function buildUrl(array $item=null, array $params=null, $itemLabel='item', \PageModel $page=null)
    {
        global $objPage;

        if ($page === null && intval($this->jumpTo)) {
            $page = $this->getPageDetails(intval($this->jumpTo));
        } else if ($page === null) {
            $page = $objPage;
        }
        
        $itemQueryString = null;
        if ($item !== null) {
            $itemQueryString = ($GLOBALS['TL_CONFIG']['useAutoItem'] ?  '/' : '/' . $itemLabel . '/') .
                ((!$GLOBALS['TL_CONFIG']['disableAlias'] && $item['alias'] != '') ? $item['alias'] : $item['id']);
        }
        
        $paramQueryString = ($params === null) ? '' : '?' . http_build_query($params);

        return ampersand($this->generateFrontendUrl($page->row(), $itemQueryString) . $paramQueryString);
    }
    
    
    protected function getWidgetValues(array &$widgets)
    {
        $values = array();
        foreach ($widgets as $name => &$widget) {
            $values[$name] = $widget->value;
        }
        
        return $values;
    }


    protected function setWidgetValues(array &$widgets, array &$values)
    {
        foreach ($values as $name => $value) {
            if (isset($widgets[$name])) {
                $_POST[$name] = $value;
                $widgets[$name]->value = $value;
            }
        }
    }


    protected function getFormWidgets(array $fields)
    {
        $widgets = array();
        foreach ($fields as $fieldName => &$field) {
            $strClass = $GLOBALS['TL_FFL'][$field['inputType']];

            // Continue if the class is not defined
            if (!class_exists($strClass)) {
                continue;
            }

            $widgets[$fieldName] = new $strClass($this->prepareForWidget($field, $fieldName, $field['value']));
        }

        return $widgets;
    }
}
