<?php

namespace App\Entity;

use App\Repository\SponsorsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SponsorsRepository::class)]
class Sponsors
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: team::class, inversedBy: 'teamSponsors')]
    private Collection $teams;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $commercialZone = null;

    public function __construct()
    {
        $this->teams = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, team>
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(team $team): static
    {
        if (!$this->teams->contains($team)) {
            $this->teams->add($team);
        }

        return $this;
    }

    public function removeTeam(team $team): static
    {
        $this->teams->removeElement($team);

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCommercialZone(): ?string
    {
        return $this->commercialZone;
    }

    public function setCommercialZone(string $commercialZone): static
    {
        $this->commercialZone = $commercialZone;

        return $this;
    }
}