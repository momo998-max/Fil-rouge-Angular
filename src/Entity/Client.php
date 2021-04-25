<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ClientRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @UniqueEntity({"telephone"})
 * @ApiResource(
 *      attributes={
 *          "denormalization_context"={"groups"={"transaction_write"},"enable_max_depth"=true}
 *      },
 * )
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 */
class Client
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"transaction_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank(message="firstName cannot be empty")
     * @Groups({"transaction_write","transaction_read"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="lastName cannot be empty")
     * @Groups({"transaction_write","transaction_read"})
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\Regex(
     *     pattern="/((7[7608][0-9]{7}$)|(3[03][98][0-9]{6}$))/",
     *     match=true,
     *     message="Invalid phone number(Ex. 771234567)"
     * )
     * @Assert\NotBlank(message="empty phone number")
     * @Groups({"transaction_write","transaction_read"})
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"transaction_write","transaction_read"})
     */
    private $IdCard;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="sendFrom")
     */
    private $transactions;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getIdCard(): ?string
    {
        return $this->IdCard;
    }

    public function setIdCard(string $IdCard): self
    {
        $this->IdCard = $IdCard;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setSendFrom($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getSendFrom() === $this) {
                $transaction->setSendFrom(null);
            }
        }

        return $this;
    }
}
