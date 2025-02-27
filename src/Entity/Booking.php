<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking

{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'registrations')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\ManyToOne(targetEntity: Ride::class, inversedBy: 'registrations')]
    #[ORM\JoinColumn(nullable: false)]
    private $ride;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $CreatedAt = null;

    #[ORM\Column]
    private ?int $seatsBooked = null;

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        if (!$this->CreatedAt) {
            $this->CreatedAt = new \DateTimeImmutable();
        }
    }

    public function getId(): ?int { return $this->id; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): self { $this->user = $user; return $this; }
    public function getRide(): ?Ride { return $this->ride; }
    public function setRide(?Ride $ride): self { $this->ride = $ride; return $this; }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->CreatedAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): void
{
    if (!$this->CreatedAt) {
        $this->CreatedAt = new \DateTimeInterface('today'); // Stocke uniquement la date sans l'heure
    }
}

    public function getSeatsBooked(): ?int
    {
        return $this->seatsBooked;
    }

    public function setSeatsBooked(int $seatsBooked): static
    {
        $this->seatsBooked = $seatsBooked;

        return $this;
    }
    public function __construct()
    {
        $this->CreatedAt = new \DateTime();
    }

   
   
}
