<?php

namespace App\Entity;

use App\Repository\RaidTeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * @ORM\Entity(repositoryClass=RaidTeamRepository::class)
 */
class RaidTeam
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=RaidCharacter::class, mappedBy="raidTeam")
     */
    private $characterList;

    /**
     * 
     * @ORM\ManyToOne(targetEntity=RaidGame::class, inversedBy="raidTeamList")
     */
    private $raidGame;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $kind;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $orderNum;

    public function __construct()
    {
        $this->characterList = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|RaidCharacter[]
     * @OrderBy({"orderNum" = "ASC"})
     */
    public function getCharacterList(): Collection
    {
        return $this->characterList;
    }

    public function addCharacterList(RaidCharacter $characterList): self
    {
        if (!$this->characterList->contains($characterList)) {
            $this->characterList[] = $characterList;
            $characterList->setRaidTeam($this);
        }

        return $this;
    }

    public function removeCharacterList(RaidCharacter $characterList): self
    {
        if ($this->characterList->removeElement($characterList)) {
            // set the owning side to null (unless already changed)
            if ($characterList->getRaidTeam() === $this) {
                $characterList->setRaidTeam(null);
            }
        }

        return $this;
    }

    public function getRaidGame(): ?RaidGame
    {
        return $this->raidGame;
    }

    public function setRaidGame(?RaidGame $raidGame): self
    {
        $this->raidGame = $raidGame;

        return $this;
    }

    public function getKind(): ?string
    {
        return $this->kind;
    }

    public function setKind(string $kind): self
    {
        $this->kind = $kind;

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
}
