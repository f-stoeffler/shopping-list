<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['shopping_list:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 128, options: ["default" => "Unnamed Item"])]
    #[Groups(['shopping_list:read'])]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ShoppingList $shopping_list = null;

    #[ORM\Column(options: ["default" => false])]
    #[Groups(['shopping_list:read'])]
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
