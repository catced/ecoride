<?php

namespace App\Entity;

use App\Repository\WinCreditRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WinCreditRepository::class)]
class WinCredit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $Monday = null;

    #[ORM\Column(nullable: true)]
    private ?int $Tuesday = null;

    #[ORM\Column(nullable: true)]
    private ?int $Wednesday = null;

    #[ORM\Column(nullable: true)]
    private ?int $Thursday = null;

    #[ORM\Column(nullable: true)]
    private ?int $Friday = null;

    #[ORM\Column(nullable: true)]
    private ?int $Saturday = null;

    #[ORM\Column(nullable: true)]
    private ?int $Sunday = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMonday(): ?int
    {
        return $this->Monday;
    }

    public function setMonday(?int $Monday): static
    {
        $this->Monday = $Monday;

        return $this;
    }

    public function getTuesday(): ?int
    {
        return $this->Tuesday;
    }

    public function setTuesday(?int $Tuesday): static
    {
        $this->Tuesday = $Tuesday;

        return $this;
    }

    public function getWednesday(): ?int
    {
        return $this->Wednesday;
    }

    public function setWednesday(?int $Wednesday): static
    {
        $this->Wednesday = $Wednesday;

        return $this;
    }

    public function getThursday(): ?int
    {
        return $this->Thursday;
    }

    public function setThursday(?int $Thursday): static
    {
        $this->Thursday = $Thursday;

        return $this;
    }

    public function getFriday(): ?int
    {
        return $this->Friday;
    }

    public function setFriday(?int $Friday): static
    {
        $this->Friday = $Friday;

        return $this;
    }

    public function getSaturday(): ?int
    {
        return $this->Saturday;
    }

    public function setSaturday(?int $Saturday): static
    {
        $this->Saturday = $Saturday;

        return $this;
    }

    public function getSunday(): ?int
    {
        return $this->Sunday;
    }

    public function setSunday(?int $Sunday): static
    {
        $this->Sunday = $Sunday;

        return $this;
    }
}
