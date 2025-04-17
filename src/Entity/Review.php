<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $passenger = null;

    #[ORM\ManyToOne(targetEntity: Ride::class, inversedBy: "reviews")]
    private ?Ride $ride = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(type: 'integer')]
    private ?int $rating = null; // Note entre 1 et 5

    #[ORM\Column(type: 'boolean', options: ["default" => false])]
    private bool $validated = false; // Doit être validé si avis négatif



    public function getId(): ?int { return $this->id; }
    public function getPassenger(): ?User { return $this->passenger; }
    public function setPassenger(?User $passenger): self { $this->passenger = $passenger; return $this; }
    public function getRide(): ?Ride { return $this->ride; }
    public function setRide(?Ride $ride): self { $this->ride = $ride; return $this; }
    public function getComment(): ?string { return $this->comment; }
    public function setComment(?string $comment): self { $this->comment = $comment; return $this; }
    public function getRating(): ?int { return $this->rating; }
    public function setRating(int $rating): self { $this->rating = $rating; return $this; }
    public function isValidated(): bool { return $this->validated; }
    public function setValidated(bool $validated): self { $this->validated = $validated; return $this; }
    

}

