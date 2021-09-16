<?php

namespace App\Entity;

use App\Repository\RaidCharacterRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * @ORM\Entity(repositoryClass=RaidCharacterRepository::class)
 */
class RaidCharacter
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="json")
     */
    private $currentAttrData = [];

    /**
     * @ORM\Column(type="json")
     */
    private $baseAttrData = [];

    /**
     * @Ignore()
     * @ORM\ManyToOne(targetEntity=RaidTeam::class, inversedBy="characterList")
     */
    private $raidTeam;

    /**
     * @ORM\ManyToOne(targetEntity=CharPlayer::class)
     */
    private $charPlayer;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $orderNum;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurrentAttrData(): ?array
    {
        return $this->currentAttrData;
    }

    public function setCurrentAttrData(array $currentAttrData): self
    {
        $this->currentAttrData = $currentAttrData;

        return $this;
    }

    public function getBaseAttrData(): ?array
    {
        return $this->baseAttrData;
    }

    public function setBaseAttrData(array $baseAttrData): self
    {
        $this->baseAttrData = $baseAttrData;

        return $this;
    }

    public function getRaidTeam(): ?RaidTeam
    {
        return $this->raidTeam;
    }

    public function setRaidTeam(?RaidTeam $raidTeam): self
    {
        $this->raidTeam = $raidTeam;

        return $this;
    }

    public function getCharPlayer(): ?CharPlayer
    {
        return $this->charPlayer;
    }

    public function setCharPlayer(?CharPlayer $charPlayer): self
    {
        $this->charPlayer = $charPlayer;

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
