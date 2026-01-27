<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['shopping-list', 'item'])]
    private ?int $id = null;

    #[ORM\Column(length: 128, options: ["default" => "Unnamed Item"])]
    #[Groups(['shopping-list', 'item'])]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'items', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['item'])]
    private ?ShoppingList $shopping_list = null;

    #[ORM\Column(options: ["default" => false])]
    #[Groups(['shopping-list', 'item'])]
    private ?bool $acquired = false;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getShoppingList(): ?ShoppingList
    {
        return $this->shopping_list;
    }

    public function setShoppingList(?ShoppingList $shopping_list): static
    {
        $this->shopping_list = $shopping_list;

        return $this;
    }

    public function isAcquired(): ?bool
    {
        return $this->acquired;
    }

    public function setAcquired(bool $acquired): static
    {
        $this->acquired = $acquired;

        return $this;
    }
}
