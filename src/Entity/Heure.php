<?php

namespace App\Entity;

use App\Repository\HeureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HeureRepository::class)]
class Heure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'time')]
    private ?\DateTimeInterface $libelle = null;

    #[ORM\Column]
    private ?bool $actif = null;

    #[ORM\ManyToOne(targetEntity: Jour::class, inversedBy: 'heures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Jour $jour = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?\DateTimeInterface
    {
        return $this->libelle;
    }

    public function setLibelle(\DateTimeInterface $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }

    public function getJour(): ?Jour
    {
        return $this->jour;
    }

    public function setJour(?Jour $jour): static
    {
        $this->jour = $jour;

        return $this;
    }
}