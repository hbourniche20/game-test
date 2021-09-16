<?php

namespace App\Entity;

use App\Repository\CharClasseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CharClasseRepository::class)
 */
class CharClasse
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=Ability::class, inversedBy="charClasseList")
     */
    private $abilityList;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $libData = [];

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $identifier;

    public function __construct()
    {
        $this->abilityList = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Ability[]
     * @OrderBy({"orderNum" = "ASC"})
     */
    public function getAbilityList(): Collection
    {
        return $this->abilityList;
    }

    public function addAbilityList(Ability $abilityList): self
    {
        if (!$this->abilityList->contains($abilityList)) {
            $this->abilityList[] = $abilityList;
        }

        return $this;
    }

    public function getAbilityListByKind($kind){
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq("kind", $kind));
        return $this->getAbilityList()->matching($criteria);
    }

    public function removeAbilityList(Ability $abilityList): self
    {
        $this->abilityList->removeElement($abilityList);

        return $this;
    }

    public function getLibData(): ?array
    {
        return $this->libData;
    }

    public function setLibData(?array $libData): self
    {
        $this->libData = $libData;

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

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }
}
