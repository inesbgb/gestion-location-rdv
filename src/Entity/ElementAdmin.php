<?php

namespace App\Entity;

use App\Repository\ElementAdminRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ElementAdminRepository::class)]
class ElementAdmin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

  

    #[ORM\ManyToOne(targetEntity: Produit::class)]
    private ?Produit $carouselImage1 = null;

    #[ORM\ManyToOne(targetEntity: Produit::class)]
    private ?Produit $carouselImage2 = null;

    #[ORM\ManyToOne(targetEntity: Produit::class)]
    private ?Produit $carouselImage3 = null;

    #[ORM\Column(length: 255)]
    private ?string $imageHistoire = null;

    #[ORM\ManyToOne(targetEntity: Produit::class)]
    private ?Produit $carouselImage4 = null;

    #[ORM\ManyToOne(targetEntity: Produit::class)]
    private ?Produit $carouselImage5 = null;

    #[ORM\ManyToOne(targetEntity: Produit::class)]
    private ?Produit $carouselImage6 = null;

    #[ORM\Column(length: 255)]
    private ?string $homevideo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $videoFilename = null;

    public function getId(): ?int
    {
        return $this->id;
    }

   

    public function getCarouselImage1(): ?Produit
    {
        return $this->carouselImage1;
    }

    public function setCarouselImage1(?Produit $carouselImage1): self
    {
        $this->carouselImage1 = $carouselImage1;
        return $this;
    }

    public function getCarouselImage2(): ?Produit
    {
        return $this->carouselImage2;
    }

    public function setCarouselImage2(?Produit $carouselImage2): self
    {
        $this->carouselImage2 = $carouselImage2;
        return $this;
    }

    public function getCarouselImage3(): ?Produit
    {
        return $this->carouselImage3;
    }

    public function setCarouselImage3(?Produit $carouselImage3): self
    {
        $this->carouselImage3 = $carouselImage3;
        return $this;
    }

    public function getImageHistoire(): ?string
    {
        return $this->imageHistoire;
    }

    public function setImageHistoire(string $imageHistoire): static
    {
        $this->imageHistoire = $imageHistoire;

        return $this;
    }

    public function getCarouselImage4(): ?Produit
    {
        return $this->carouselImage4;
    }

    public function setCarouselImage4(?Produit $carouselImage4): static
    {
        $this->carouselImage4 = $carouselImage4;

        return $this;
    }

    public function getCarouselImage5(): ?Produit
    {
        return $this->carouselImage5;
    }

    public function setCarouselImage5(?Produit $carouselImage5): static
    {
        $this->carouselImage5 = $carouselImage5;
        return $this;
    }

    public function getCarouselImage6(): ?Produit
    {
        return $this->carouselImage6;
    }

    public function setCarouselImage6(?Produit $carouselImage6): static
    {
        $this->carouselImage6 = $carouselImage6;

        return $this;
    }

    public function getHomevideo(): ?string
    {
        return $this->homevideo;
    }

    public function setHomevideo(string $homevideo): static
    {
        $this->homevideo = $homevideo;

        return $this;
    }

    public function getVideoFilename(): ?string
    {
        return $this->videoFilename;
    }

    public function setVideoFilename(?string $videoFilename): self
    {
        $this->videoFilename = $videoFilename;
        return $this;
    }
}
