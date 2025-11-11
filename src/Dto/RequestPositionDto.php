<?php

namespace App\Dto;

use DateTime;

class RequestPositionDto
{
    private ?int $id = null;
    private ?int $requestId = null;
    private ?string $requestCode = null;
    private ?string $requestName = null;
    private ?string $name = null;
    private ?DateTime $deliveryDate = null;
    private ?float $price = null;
    private ?int $quantity = null;
    private ?float $totalPrice = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getRequestId(): ?int
    {
        return $this->requestId;
    }

    public function setRequestId(int $requestId): static
    {
        $this->requestId = $requestId;

        return $this;
    }

    public function getRequestCode(): ?int
    {
        return $this->requestCode;
    }

    public function setRequestCode(int $requestCode): static
    {
        $this->requestCode = $requestCode;

        return $this;
    }

    public function getRequestName(): ?int
    {
        return $this->requestName;
    }

    public function setRequestName(int $requestName): static
    {
        $this->requestName = $requestName;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDeliveryDate(): ?DateTime
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(DateTime $deliveryDate): static
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): static
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }
}
