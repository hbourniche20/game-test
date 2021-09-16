<?php

namespace App\Entity;

use App\Repository\RaidGameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * @ORM\Entity(repositoryClass=RaidGameRepository::class)
 */
class RaidGame
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
    private $background_url;

    /**
     * @Ignore
     * @ORM\ManyToOne(targetEntity=RaidQuest::class, inversedBy="raidGameList")
     */
    private $raidQuest;

    /**
     * @ORM\OneToMany(targetEntity=RaidTeam::class, mappedBy="raidGame")
     * @OrderBy({"orderNum" = "ASC"})
     */
    private $raidTeamList;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    public function __construct()
    {
        $this->raidTeamList = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBackgroundUrl(): ?string
    {
        return $this->background_url;
    }

    public function setBackgroundUrl(string $background_url): self
    {
        $this->background_url = $background_url;

        return $this;
    }

    public function getRaidQuest(): ?RaidQuest
    {
        return $this->raidQuest;
    }

    public function setRaidQuest(?RaidQuest $raidQuest): self
    {
        $this->raidQuest = $raidQuest;

        return $this;
    }

    /**
     * @return Collection|RaidTeam[]
     */
    public function getRaidTeamList(): Collection
    {
        return $this->raidTeamList;
    }

    public function addRaidTeamList(RaidTeam $raidTeamList): self
    {
        if (!$this->raidTeamList->contains($raidTeamList)) {
            $this->raidTeamList[] = $raidTeamList;
            $raidTeamList->setRaidGame($this);
        }

        return $this;
    }

    public function removeRaidTeamList(RaidTeam $raidTeamList): self
    {
        if ($this->raidTeamList->removeElement($raidTeamList)) {
            // set the owning side to null (unless already changed)
            if ($raidTeamList->getRaidGame() === $this) {
                $raidTeamList->setRaidGame(null);
            }
        }

        return $this;
    }
    /**
     * @return RaidTeam
     */
    public function getRaidTeamByKind($kind): RaidTeam
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq("kind", $kind));
        return $this->getRaidTeamList()->matching($criteria)[0];
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

}
