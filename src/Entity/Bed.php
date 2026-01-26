<?php

namespace App\Entity;

use App\Repository\BedRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BedRepository::class)]
class Bed
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $bedNumber = null;

    #[ORM\ManyToOne(inversedBy: 'beds')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ward $ward = null;

    #[ORM\Column(length: 20)]
    private ?string $status = 'available'; // available, occupied, maintenance

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Patient $currentPatient = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBedNumber(): ?string
    {
        return $this->bedNumber;
    }

    public function setBedNumber(string $bedNumber): self
    {
        $this->bedNumber = $bedNumber;
        return $this;
    }

    public function getWard(): ?Ward
    {
        return $this->ward;
    }

    public function setWard(?Ward $ward): self
    {
        $this->ward = $ward;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getCurrentPatient(): ?Patient
    {
        return $this->currentPatient;
    }

    public function setCurrentPatient(?Patient $currentPatient): self
    {
        $this->currentPatient = $currentPatient;
        if ($currentPatient) {
            $this->status = 'occupied';
        } else {
            $this->status = 'available';
        }
        return $this;
    }
}
