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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mainImage = null;

    #[ORM\Column(length: 255)]
    private ?string $carouselImage1 = null;

    #[ORM\Column(length: 255)]
    private ?string $carouselImage2 = null;

    #[ORM\Column(length: 255)]
    private ?string $carouselImage3 = null;

    #[ORM\Column(length: 255)]
    private ?string $imageHistoire = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMainImage(): ?string
    {
        return $this->mainImage;
    }
    
    public function setMainImage(?string $mainImage): self
    {
        $this->mainImage = $mainImage;
        return $this;
    }

    public function getCarouselImage1(): ?string
    {
        return $this->carouselImage1;
    }

    public function setCarouselImage1(string $carouselImage1): static
    {
        $this->carouselImage1 = $carouselImage1;

        return $this;
    }

    public function getCarouselImage2(): ?string
    {
        return $this->carouselImage2;
    }

    public function setCarouselImage2(string $carouselImage2): static
    {
        $this->carouselImage2 = $carouselImage2;

        return $this;
    }

    public function getCarouselImage3(): ?string
    {
        return $this->carouselImage3;
    }

    public function setCarouselImage3(string $carouselImage3): static
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
}
