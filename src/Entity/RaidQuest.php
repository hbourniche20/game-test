<?php

namespace App\Entity;

use App\Repository\RaidQuestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * @ORM\Entity(repositoryClass=RaidQuestRepository::class)
 */
class RaidQuest
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
     * @ORM\Column(type="json")
     */
    private $dataConf = [];

    /**
     * @Ignore()
     * @ORM\OneToMany(targetEntity=RaidGame::class, mappedBy="raidQuest")
     */
    private $raidGameList;

    public function __construct()
    {
        $this->raidGameList = new ArrayCollection();
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

    public function getDataConf(): ?array
    {
        return $this->dataConf;
    }

    public function setDataConf(array $dataConf): self
    {
        $this->dataConf = $dataConf;

        return $this;
    }

    /**
     * @return Collection|RaidGame[]
     */
    public function getRaidGameList(): Collection
    {
        return $this->raidGameList;
    }

    public function addRaidGameList(RaidGame $raidGameList): self
    {
        if (!$this->raidGameList->contains($raidGameList)) {
            $this->raidGameList[] = $raidGameList;
            $raidGameList->setRaidQuest($this);
        }

        return $this;
    }

    public function removeRaidGameList(RaidGame $raidGameList): self
    {
        if ($this->raidGameList->removeElement($raidGameList)) {
            // set the owning side to null (unless already changed)
            if ($raidGameList->getRaidQuest() === $this) {
                $raidGameList->setRaidQuest(null);
            }
        }

        return $this;
    }
}
