<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column(length: 255)]
    private ?string $coach = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nationalTitle = null;


    #[ORM\OneToMany(mappedBy: 'team1', targetEntity: Matches::class, orphanRemoval: true)]
    private Collection $HomeGames;

    #[ORM\OneToMany(mappedBy: 'team2', targetEntity: Matches::class)]
    private Collection $AwayGames;

    #[ORM\ManyToMany(targetEntity: Sponsors::class, mappedBy: 'teams')]
    private Collection $teamSponsors;

    #[ORM\OneToMany(mappedBy: 'team', targetEntity: Player::class)]
    private Collection $players;

    #[ORM\Column(nullable: true)]
    private ?int $point = null;
    public function __construct()
    {
        $this->Members = new ArrayCollection();
        $this->HomeGames = new ArrayCollection();
        $this->AwayGames = new ArrayCollection();
        $this->teamSponsors = new ArrayCollection();
        $this->players = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
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

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getCoach(): ?string
    {
        return $this->coach;
    }

    public function setCoach(string $coach): static
    {
        $this->coach = $coach;

        return $this;
    }

    public function getNationalTitle(): ?string
    {
        return $this->nationalTitle;
    }

    public function setNationalTitle(?string $nationalTitle): static
    {
        $this->nationalTitle = $nationalTitle;

        return $this;
    }
    public function __toString()
    {
        return $this->getName();
    }
    /**
     * @return Collection<int, Matches>
     */
    public function getHomeGames(): Collection
    {
        return $this->HomeGames;
    }

    public function addHomeGame(Matches $homeGame): static
    {
        if (!$this->HomeGames->contains($homeGame)) {
            $this->HomeGames->add($homeGame);
            $homeGame->setTeam1($this);
        }

        return $this;
    }

    public function removeHomeGame(Matches $homeGame): static
    {
        if ($this->HomeGames->removeElement($homeGame)) {
            // set the owning side to null (unless already changed)
            if ($homeGame->getTeam1() === $this) {
                $homeGame->setTeam1(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Matches>
     */
    public function getAwayGames(): Collection
    {
        return $this->AwayGames;
    }

    public function addAwayGame(Matches $awayGame): static
    {
        if (!$this->AwayGames->contains($awayGame)) {
            $this->AwayGames->add($awayGame);
            $awayGame->setTeam2($this);
        }

        return $this;
    }

    public function removeAwayGame(Matches $awayGame): static
    {
        if ($this->AwayGames->removeElement($awayGame)) {
            // set the owning side to null (unless already changed)
            if ($awayGame->getTeam2() === $this) {
                $awayGame->setTeam2(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sponsors>
     */
    public function getTeamSponsors(): Collection
    {
        return $this->teamSponsors;
    }

    public function addTeamSponsor(Sponsors $teamSponsor): static
    {
        if (!$this->teamSponsors->contains($teamSponsor)) {
            $this->teamSponsors->add($teamSponsor);
            $teamSponsor->addTeam($this);
        }

        return $this;
    }

    public function removeTeamSponsor(Sponsors $teamSponsor): static
    {
        if ($this->teamSponsors->removeElement($teamSponsor)) {
            $teamSponsor->removeTeam($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Player>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(Player $player): static
    {
        if (!$this->players->contains($player)) {
            $this->players->add($player);
            $player->setTeam($this);
        }

        return $this;
    }

    public function removePlayer(Player $player): static
    {
        if ($this->players->removeElement($player)) {
            // set the owning side to null (unless already changed)
            if ($player->getTeam() === $this) {
                $player->setTeam(null);
            }
        }

        return $this;
    }

    public function getPoint(): ?int
    {
        return $this->point;
    }

    public function setPoint(?int $point): static
    {
        $this->point = $point;

        return $this;
    }



}
