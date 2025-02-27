<?php

namespace App\Entity;

use App\Repository\VehicleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VehicleRepository::class)]
class Vehicle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $brand;

    #[ORM\Column(type: 'string', length: 255)]
    private $model;

    #[ORM\Column(type: 'string', length: 255)]
    private $color;

    #[ORM\Column(type: 'string', length: 20, unique: true)]
    private $licensePlate;

    #[ORM\Column(type: 'integer')]
    private $seatsCount;

    #[ORM\Column(type: 'string', length: 255)]
    private $energy;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $preferences = [];

    // #[ORM\ManyToOne(targetEntity: User::class)]
    // #[ORM\JoinColumn(nullable: false)]
    // private $owner;
    // #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'vehicles')]
    // #[ORM\JoinColumn(nullable: false)]
    // private ?User $owner = null;

    // #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "vehicles")]
    // #[ORM\JoinColumn(nullable: false)]
    // private User $user;

    // #[ORM\ManyToOne(targetEntity: User::class)] // Relation vers User SANS mappedBy
    // #[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'id', nullable: false)]
    // private User $owner;
    #[ORM\ManyToOne(targetEntity: User::class)]
#[ORM\JoinColumn(nullable: false)]
private User $owner;


    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateFirstUse = null;

  

    // /**
    //  * @var Collection<int, User>
    //  */
    // #[ORM\ManyToOne(targetEntity: User::class, mappedBy: 'vehicle')]
    // private Collection $user;

    public function __construct()
    {
       // $this->user = new ArrayCollection();
       $this->preferences = [];
    }

    public function getId(): ?int { return $this->id; }
    public function getBrand(): ?string { return $this->brand; }
    public function setBrand(string $brand): self { $this->brand = $brand; return $this; }
    public function getModel(): ?string { return $this->model; }
    public function setModel(string $model): self { $this->model = $model; return $this; }
    public function getColor(): ?string { return $this->color; }
    public function setColor(string $color): self { $this->color = $color; return $this; }
    public function getLicensePlate(): ?string { return $this->licensePlate; }
    public function setLicensePlate(string $licensePlate): self { $this->licensePlate = $licensePlate; return $this; }
    public function getSeatsCount(): ?int { return $this->seatsCount; }
    public function setSeatsCount(int $seatsCount): self { $this->seatsCount = $seatsCount; return $this; }
    public function getEnergy(): ?string { return $this->energy; }
    public function setEnergy(string $energy): self { $this->energy = $energy; return $this; }
    public function getOwner(): ?User { return $this->owner; }
    public function setOwner(?User $owner): self { $this->owner = $owner; return $this; }
    public function getPreferences(): ?array
    {
        return $this->preferences;
    }

    public function setPreferences(?array $preferences): self
    {
        $this->preferences = $preferences;
        return $this;
    }

    public function getDateFirstUse(): ?\DateTimeInterface
    {
        return $this->dateFirstUse;
    }

    public function setDateFirstUse(?\DateTimeInterface $dateFirstUse): static
    {
        $this->dateFirstUse = $dateFirstUse;

        return $this;
    }

    // /**
    //  * @return Collection<int, User>
    //  */
    // public function getOwner(): Collection
    // {
    //     return $this->owner;
    // }

    // public function addUser(User $user): static
    // {
    //     if (!$this->user->contains($user)) {
    //         $this->user->add($user);
    //         $user->setVehicle($this);
    //     }

    //     return $this;
    // }

    // public function removeUser(User $user): static
    // {
    //     if ($this->user->removeElement($user)) {
    //         // set the owning side to null (unless already changed)
    //         if ($user->getVehicle() === $this) {
    //             $user->setVehicle(null);
    //         }
    //     }

    //     return $this;
    // }


   

   

}
