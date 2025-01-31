<?php

namespace App\Entity;

use App\Repository\HoraireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HoraireRepository::class)]
class Horaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $slot = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlot(): ?\DateTimeInterface
    {
        return $this->slot;
    }

    public function setSlot(\DateTimeInterface $slot): static
    {
        $this->slot = $slot;

        return $this;
    }
}
