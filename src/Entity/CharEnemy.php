<?php

namespace App\Entity;

use App\Repository\CharEnemyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CharEnemyRepository::class)
 */
class CharEnemy
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=CharClasse::class)
     */
    private $charClasse;

    /**
     * @ORM\Column(type="json")
     */
    private $baseAttribute = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCharClasse(): ?CharClasse
    {
        return $this->charClasse;
    }

    public function setCharClasse(?CharClasse $charClasse): self
    {
        $this->charClasse = $charClasse;

        return $this;
    }

    public function getBaseAttribute(): ?array
    {
        return $this->baseAttribute;
    }

    public function setBaseAttribute(array $baseAttribute): self
    {
        $this->baseAttribute = $baseAttribute;

        return $this;
    }
}
