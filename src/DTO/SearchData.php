<?php

namespace App\DTO;

use App\Entity\Categorie;
use App\Entity\Taille;

class SearchData
{
    public ?string $query = '';
    public ?Categorie $categorie = null;
    public ?Taille $taille = null;
    public ?bool $liquidation = null;
    public ?bool $actif = null;
    public int $page = 1;
    public ?\DateTimeInterface $dateDebut = null;
    public ?\DateTimeInterface $dateFin = null;
    public ?bool $stock = null;
}