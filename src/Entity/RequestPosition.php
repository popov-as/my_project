<?php

namespace App\Entity;

use App\Repository\RequestPositionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;

#[ORM\Entity(repositoryClass: RequestPositionRepository::class)]
#[ORM\Table(name: 't_request_position')]
class RequestPosition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Id заявки должно быть задано')]
    private ?int $requestId = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Наименование должно быть задано')]
    private ?string $name = null;

    #[ORM\Column]
    #[Assert\GreaterThan('today', message: 'Дата должна быть больше текущей')]
    private ?DateTime $deliveryDate = null;

    #[ORM\Column]
    #[Assert\Positive(message: 'Цена должна быть больше нуля')]
    private ?float $price = null;

    #[ORM\Column]
    #[Assert\Positive(message: 'Количество должно быть больше нуля')]
    private ?int $quantity = null;

    #[ORM\Column]
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
