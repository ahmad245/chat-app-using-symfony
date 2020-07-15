<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ParticipantRepository::class)
 */
class Participant
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="participants")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Conversation", inversedBy="participants")
     */
    private $conversation;

    /**
     * @ORM\OneToMany(targetEntity=BlockList::class, mappedBy="participant")
     */
    private $blockLists;

    public function __construct()
    {
        $this->blockLists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(?Conversation $conversation): self
    {
        $this->conversation = $conversation;

        return $this;
    }

    /**
     * @return Collection|BlockList[]
     */
    public function getBlockLists(): Collection
    {
        return $this->blockLists;
    }

    public function addBlockList(BlockList $blockList): self
    {
        if (!$this->blockLists->contains($blockList)) {
            $this->blockLists[] = $blockList;
            $blockList->setParticipantId($this);
        }

        return $this;
    }

    public function removeBlockList(BlockList $blockList): self
    {
        if ($this->blockLists->contains($blockList)) {
            $this->blockLists->removeElement($blockList);
            // set the owning side to null (unless already changed)
            if ($blockList->getParticipantId() === $this) {
                $blockList->setParticipantId(null);
            }
        }

        return $this;
    }
}