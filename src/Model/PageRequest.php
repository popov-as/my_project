<?php
namespace App\Model;

class PageRequest
{
    public function __construct(
        public int $page = 1,   // Номер страницы
        public int $size = 20,  // Количество строк на странице
        public ?string $sort,   // Столбец для сортировки
        public ?string $order,  // Порядок сортировки
    ) {
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getSort(): ?string
    {
        return $this->sort;
    }

    public function getOrder(): ?string
    {
        return $this->order;
    }
}