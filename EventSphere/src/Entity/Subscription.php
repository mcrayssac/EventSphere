<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SubscriptionRepository")
 * @ORM\Table(name="subscriptions")
 */
class Subscription
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="subscriptions")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Event", inversedBy="subscriptions")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank
     */
    private $event;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     */
    private $subscribedAt;

    public function __construct()
    {
        $this->subscribedAt = new \DateTime();
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

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getSubscribedAt(): ?\DateTimeInterface
    {
        return $this->subscribedAt;
    }

    public function setSubscribedAt(\DateTimeInterface $subscribedAt): self
    {
        $this->subscribedAt = $subscribedAt;

        return $this;
    }
}
