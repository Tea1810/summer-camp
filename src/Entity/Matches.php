<?php

namespace App\Entity;

use App\Repository\MatchesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MatchesRepository::class)]
class Matches
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'HomeGames')]
    #[ORM\JoinColumn(nullable: false)]
    private ?team $team1 = null;

    #[ORM\ManyToOne(inversedBy: 'AwayGames')]
    #[ORM\JoinColumn(nullable: false)]
    private ?team $team2 = null;

    #[ORM\Column]
    private ?int $score1 = null;

    #[ORM\Column]
    private ?int $score2 = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeam1(): ?team
    {
        return $this->team1;
    }

    public function setTeam1(?team $team1): static
    {
        $this->team1 = $team1;

        return $this;
    }

    public function getTeam2(): ?team
    {
        return $this->team2;
    }

    public function setTeam2(?team $team2): static
    {
        $this->team2 = $team2;

        return $this;
    }

    public function getScore1(): ?int
    {
        return $this->score1;
    }

    public function setScore1(int $score1): static
    {
        $this->score1 = $score1;

        return $this;
    }

    public function getScore2(): ?int
    {
        return $this->score2;
    }

    public function setScore2(int $score2): static
    {
        $this->score2 = $score2;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }
}