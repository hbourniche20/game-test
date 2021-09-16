<?php

namespace App\Entity;

use App\Repository\CharPlayerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CharPlayerRepository::class)
 */
class CharPlayer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $level;

    /**
     * @ORM\ManyToOne(targetEntity=CharClasse::class)
     */
    private $classe;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="charPlayerList")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getClasse(): ?CharClasse
    {
        return $this->classe;
    }

    public function setClasse(?CharClasse $classe): self
    {
        $this->classe = $classe;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
