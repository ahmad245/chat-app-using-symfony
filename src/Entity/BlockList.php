<?php

namespace App\Entity;

use App\Repository\BlockListRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * @ORM\Entity(repositoryClass=BlockListRepository::class)
 *  @ORM\HasLifecycleCallbacks()
 */
class BlockList
{
    use Timestamp;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("users")
     * @Groups("block")
     */
    private $id;

    

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="blockLists")
     * @Groups("users")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Participant::class, inversedBy="blockLists")
     * @Groups("users")
     */
    private $participant;

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
        $this->user= $user;

        return $this;
    }

    public function getParticipant(): ?Participant
    {
        return $this->participant;
    }

    public function setParticipant(?Participant $participant): self
    {
        $this->participant = $participant;

        return $this;
    }
}
