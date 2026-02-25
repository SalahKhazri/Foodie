<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Collection;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: Menu::class)]
    private Collection $menus;

    #[ORM\Column(length: 255)]
    private ?string $total = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type:"json")]
    private array $quantity = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMenus(): ?string
    {
        return $this->menus;
    }

    public function addMenu(Menu $menu): void {
        if (!$this->menus->contains($menu)) {
            $this->menus->add($menu);
        }
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getQuantity(): array
    {
        return $this->quantity;
    }

    public function setQuantity(array $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }
}
