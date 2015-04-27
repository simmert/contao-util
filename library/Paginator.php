<?php

namespace Util;

/**
* Manages paginated collections
*/
class Paginator
{
    protected $pageParam   = 'page',
              $pageSize    = 20,
              $itemCount   = 0;


    public function __construct($pageSize=null)
    {
        if ($pageSize !== null) {
            $this->setPageSize($pageSize);
        }
    }


    public function getPagination()
    {
        return new \Contao\Pagination($this->getItemCount(), $this->getPageSize());
    }


    public function getSqlLimit()
    {
        return $this->getPageSize() > 0 ? sprintf(' LIMIT %u, %u', $this->getOffset(), $this->getPageSize()) : '';
    }


    public function getOffset()
    {
        return ($this->getCurrentPage() - 1) * $this->getPageSize();
    }


    public function getCurrentPage()
    {
        return min($this->getRequestedPage(), $this->getPageCount());
    }


    public function getPageCount()
    {
        return $this->getPageSize() > 0 ? max(ceil($this->getItemCount() / $this->getPageSize()), 1) : 1;
    }


    public function getRequestedPage()
    {
        $requestedPage = \Input::get($this->getPageParam());

        if ($requestedPage === null) {
            return 1;
        }

        return max(abs(intval($requestedPage)), 1);
    }


    public function setPageSize($pageSize)
    {
        $this->pageSize = intval($pageSize);
    }


    public function getPageSize()
    {
        return $this->pageSize;
    }


    public function setItemCount($itemCount)
    {
        $this->itemCount = intval($itemCount);
    }


    public function getItemCount()
    {
        return $this->itemCount;
    }


    public function setPageParam($pageParam)
    {
        $this->pageParam = $pageParam;
    }


    public function getPageParam()
    {
        return $this->pageParam;
    }
}
