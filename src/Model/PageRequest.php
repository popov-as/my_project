<?php
namespace App\Model;

class PageRequest
{
    public function __construct(
        public ?int $page,      // Номер страницы
        public ?int $size,      // Количество строк на странице
        public ?string $sort,   // Столбец для сортировки
        public ?string $order,  // Порядок сортировки
    ) {
        // Значения по умолчанию
        if (!isset($page))
            $this->page = 1;

        if (!isset($size))
            $this->size = 20;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function getSize(): ?int
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