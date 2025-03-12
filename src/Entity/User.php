<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Ignore;


#[ORM\Entity()]
#[UniqueEntity(fields: ['email'], message: 'Un compte existe déjà avec cette adresse email.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\NotBlank(message: 'L\'email est obligatoire.')]
    #[Assert\Email(message: 'Veuillez entrer une adresse email valide.')]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private ?string $password = null;

    #[Assert\NotBlank(message: "Le mot de passe est obligatoire.")]
    #[Assert\Length(min: 6, minMessage: "Le mot de passe doit contenir au moins 6 caractères.")]
    #[Ignore] 
    private ?string $plainPassword = null;
      
    #[ORM\Column(length: 255)]
    private ?string $pseudo = null;

    #[ORM\Column]
    private ?int $credit = 20;

    #[ORM\Column]
    private ?bool $RGPD = null;

    // #[ORM\Column(type: 'string', length: 20, nullable: true)]
    // private ?string $userType = null; // Passager, Chauffeur, Chauffeur-Passager

    // #[ORM\OneToMany(mappedBy: 'user', targetEntity: Vehicle::class, cascade: ['persist', 'remove'])]
    // private Collection $vehicles;


    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Ride::class, cascade: ['persist', 'remove'])]
    private Collection $rides;

    public function __construct()
    {
        $this->rides = new ArrayCollection();
        // $this->vehicles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @return Collection<int, Ride>
     */
    public function getRides(): Collection
    {
        return $this->rides;
    }

    public function addRide(Ride $ride): self
    {
        if (!$this->rides->contains($ride)) {
            $this->rides[] = $ride;
            $ride->setUser($this);
        }

        return $this;
    }

    public function removeRide(Ride $ride): self
    {
        if ($this->rides->removeElement($ride)) {
            if ($ride->getUser() === $this) {
                $ride->setUser(null);
            }
        }

        return $this;
    }

    /**
     * Obligatoire pour UserInterface
     */
    public function eraseCredentials(): void
    {
        // Si des données sensibles sont stockées temporairement, les effacer ici.
    }

    /**
     * Retourne l'identifiant unique de l'utilisateur (généralement l'email)
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getCredit(): ?int
    {
        return $this->credit;
    }

    public function setCredit(int $credit): static
    {
        $this->credit = $credit;

        return $this;
    }

    // public function getUserType(): ?string
    // {
    //     return $this->userType;
    // }

    // public function setUserType(?string $userType): self
    // {
    //     $this->userType = $userType;
    //     return $this;
    // }

    // public function getVehicles(): Collection
    // {
    //     return $this->vehicles;
    // }

    // public function addVehicle(Vehicle $vehicle): self
    // {
    //     if (!$this->vehicles->contains($vehicle)) {
    //         $this->vehicles[] = $vehicle;
    //         $vehicle->setOwner($this);
    //     }
    //     return $this;
    // }

    // public function removeVehicle(Vehicle $vehicle): self
    // {
    //     if ($this->vehicles->removeElement($vehicle)) {
    //         if ($vehicle->getOwner() === $this) {
    //             $vehicle->setOwner(null);
    //         }
    //     }

    //     return $this;
    // }
    
    
    public function isRGPD(): ?bool
    {
        return $this->RGPD;
    }

    public function setRGPD(bool $RGPD): static
    {
        $this->RGPD = $RGPD;

        return $this;
    }

    #[ORM\Column(type: 'boolean')]
    private bool $isSuspended = false;

    public function isSuspended(): bool
    {
        return $this->isSuspended;
    }

    public function setIsSuspended(bool $isSuspended): self
    {
        $this->isSuspended = $isSuspended;
        return $this;
    }
  
}
