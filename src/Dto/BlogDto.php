<?php
namespace App\Dto;

class BlogDto
{
    public function __construct(
        public int $id, 
        public string $name, 
        public string $description
    ) {
    }
}