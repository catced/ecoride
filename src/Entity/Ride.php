<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\RideRepository;

#[ORM\Entity()]
class Ride
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $departure = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $destination = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $departureDay = null;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private float $price;

    // #[ORM\Column(type: 'integer')]
    // #[Assert\NotBlank]
    // private int $duration;
    #[ORM\Column(type: 'time', nullable: false)]
    private ?\DateTime $duration = null;


    #[ORM\Column(type: "integer")]
    private $availableSeats;

    #[ORM\ManyToOne(targetEntity: Vehicle::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Vehicle $vehicle;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $driver;

    #[ORM\Column(type: 'time')]
    private ?\DateTimeInterface $departureTime = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeparture(): ?string
    {
        return $this->departure;
    }

    public function setDeparture(string $departure): self
    {
        $this->departure = $departure;
        return $this;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(string $destination): self
    {
        $this->destination = $destination;
        return $this;
    }

    public function getDepartureDay(): ?\DateTimeInterface
    {
        return $this->departureDay;
    }

    public function setDepartureDay(\DateTimeInterface $departureDay): self
    {
        $this->departureDay = $departureDay;
        return $this;
    }

    public function getPrice(): float { return $this->price; }
    public function setPrice(float $price): self { $this->price = $price; return $this; }
    // public function getDuration(): int { return $this->duration; }
    // public function setDuration(int $duration): self { $this->duration = $duration; return $this; }
    public function getVehicle(): Vehicle { return $this->vehicle; }
    public function setVehicle(Vehicle $vehicle): self { $this->vehicle = $vehicle; return $this; }
    public function getDriver(): User { return $this->driver; }
    public function setDriver(User $driver): self { $this->driver = $driver; return $this; }

    public function getDuration(): ?\DateTime
    {
        return $this->duration;
    }

    public function setDuration(?\DateTime $duration): self
    {
        $this->duration = $duration;
        return $this;
    }

    public function getAvailableSeats(): ?int
    {
        return $this->availableSeats;
    }

    public function setAvailableSeats(int $availableSeats): self
    {
        $this->availableSeats = $availableSeats;
        return $this;
    }

    public function reserveSeats(int $seats): bool
    {
        if ($this->availableSeats >= $seats) {
            $this->availableSeats -= $seats;
            return true;
        }
        return false;
    }

    public function getDepartureTime(): ?\DateTimeInterface
    {
        return $this->departureTime;
    }

    public function setDepartureTime(\DateTimeInterface $departureTime): static
    {
        $this->departureTime = $departureTime;

        return $this;
    }
}
