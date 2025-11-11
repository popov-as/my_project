<?php
namespace App\Model;


class PageResult
{
    public function __construct(
        public array $content, 
        public int $pageNumber, 
        public int $pageSize,
        public int $totalElements, 
        public int $totalPages
    ) {
    }
}