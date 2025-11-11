<?php
namespace App\Model;

class PageRequest
{
    public function __construct(
        public int $page = 1, 
        public int $pageSize = 20
    ) {
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }
}