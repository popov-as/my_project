<?php

namespace App\Entity;

use App\Repository\RequestRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;

#[ORM\Entity(repositoryClass: RequestRepository::class)]
#[ORM\Table(name: 't_request')]
class Request
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $code = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Наименование должно быть задано')]
    private ?string $name = null;

    #[ORM\Column]
    #[Assert\GreaterThan('today', message: 'Дата должна быть больше текущей')]
    private ?DateTime $statusDate = null;

    #[ORM\Column]
    #[Assert\Positive(message: 'Цена должна быть больше нуля')]
    private ?float $price = null;

    #[ORM\Column]
    #[Assert\Positive(message: 'Количество должно быть больше нуля')]
    private ?int $quantity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

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

    public function getStatusDate(): ?DateTime
    {
        return $this->statusDate;
    }

    public function setStatusDate(DateTime $statusDate): static
    {
        $this->statusDate = $statusDate;

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
}
