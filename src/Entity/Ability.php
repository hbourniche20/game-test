<?php

namespace App\Entity;

use App\Repository\AbilityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AbilityRepository::class)
 */
class Ability
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $icon;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $data = [];

    /**
     * @ORM\ManyToMany(targetEntity=CharClasse::class, mappedBy="abilityList")
     */
    private $charClasseList;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $orderNum;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $kind;

    public function __construct()
    {
        $this->charClasseList = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(?array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return Collection|CharClasse[]
     */
    public function getCharClasseList(): Collection
    {
        return $this->charClasseList;
    }

    public function addCharClasseList(CharClasse $charClasseList): self
    {
        if (!$this->charClasseList->contains($charClasseList)) {
            $this->charClasseList[] = $charClasseList;
            $charClasseList->addAbilityList($this);
        }

        return $this;
    }

    public function removeCharClasseList(CharClasse $charClasseList): self
    {
        if ($this->charClasseList->removeElement($charClasseList)) {
            $charClasseList->removeAbilityList($this);
        }

        return $this;
    }

    public function getOrderNum(): ?int
    {
        return $this->orderNum;
    }

    public function setOrderNum(?int $orderNum): self
    {
        $this->orderNum = $orderNum;

        return $this;
    }

    public function getKind(): ?string
    {
        return $this->kind;
    }

    public function setKind(?string $kind): self
    {
        $this->kind = $kind;

        return $this;
    }
}
