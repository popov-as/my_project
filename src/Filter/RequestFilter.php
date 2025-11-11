<?php
namespace App\Filter;

class RequestFilter
{
    public function __construct(
        public ?string $code, 
        public ?string $name, 
        public ?int $priceFrom,
        public ?int $priceTo
    ) {
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getPriceFrom(): ?int
    {
        return $this->priceFrom;
    }

    public function getPriceTo(): ?int
    {
        return $this->priceTo;
    }

}