<?php

namespace App\Entity;

use App\Entity\Agence;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CompteRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *      attributes={
 *          "normalization_context"={"enable_max_depth"=true}
 *      },
 *      collectionOperations={
 *         "get"={
 *              "security"="is_granted('ROLE_ADMINAGENCE')", 
 *              "security_message"="permission denied.",
 *              "path"="/admin/comptes",
 *          }
 *      },
 *      itemOperations={
 *          "patch"={
 *              "path"="/admin/comptes/{id}",
 *              "requirements"={"id"="\d+"},
 *              "security"="is_granted('ROLE_ADMINAGENCE')", 
 *              "security_message"="permission denied.",
 *              "denormalization_context"={"groups"={"compte_statut"}},
 *         },
 *          "get"={
 *              "path"="/admin/comptes/{id}",
 *              "requirements"={"id"="\d+"},
 *              "normalization_context"={"groups"={"compte_details"},"enable_max_depth"=true},
 *              "security"="is_granted('ROLE_ADMINAGENCE')", 
 *              "security_message"="permission denied.",
 *          },
 *          "rechargement"={
 *              "method"="PUT",
 *              "path"="/caissier/comptes/{id}/recharge",
 *              "requirements"={"id"="\d+"},
 *              "security"="is_granted('ROLE_CAISSIER')", 
 *              "security_message"="permission denied.",
 *              "denormalization_context"={"groups"={"compte_recharge"}},
 *          },
 *      }
 * )
 * @ORM\Entity(repositoryClass=CompteRepository::class)
 */
class Compte
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"compte_details"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"compte_details"})
     * @Assert\NotBlank(message="account number is required")
     */
    private $numero;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"compte_details"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float", length=255)
     * @Assert\NotBlank(message="sold is required")
     *  @Assert\Expression(
     *     "this.getId() === null",
     *     message="A l'ouverture, le solde doit atteindre au moins 700.000CFA!"
     * )
     * @Assert\Positive(message="le solde ne peut pas etre negatif")
     * @Assert\Type(
     *     type="numeric",
     *     message="sold not valid."
     * )
     * @Groups({"compte_details","compte_recharge"})
     * @Groups({"agence_write"})
     */
    private $solde;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Groups({"compte_details","compte_statut"})
     */
    private $statut="actif";

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="compte")
     * @Groups({"compte_details"})
     * 
     * Dépot
     */
    private $transactions;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="compteRetrait")
     */
    private $retraits;

    /**
     * @ORM\OneToOne(targetEntity=Agence::class, mappedBy="compte", cascade={"persist", "remove"})
     */
    private $agence;

    public function __construct(){
        $this->createdAt = new \DateTime();
        $this->numero = substr(str_shuffle(str_repeat($x='0123456789ABC', ceil(6/strlen($x)) )),1,6);
        $this->transactions = new ArrayCollection();
        $this->retraits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getSolde(): ?float
    {
        return $this->solde;
    }

    public function setSolde(float $solde): self
    {
        $this->solde = $this->solde + abs($solde);

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getAgence(): ?Agence
    {
        return $this->agence;
    }

    public function setAgence(?Agence $agence): self
    {
        $this->agence = $agence;

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
            $transaction->setCompte($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getCompte() === $this) {
                $transaction->setCompte(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getRetraits(): Collection
    {
        return $this->retraits;
    }

    public function addRetrait(Transaction $retrait): self
    {
        if (!$this->retraits->contains($retrait)) {
            $this->retraits[] = $retrait;
            $retrait->setCompteRetrait($this);
        }

        return $this;
    }

    public function removeRetrait(Transaction $retrait): self
    {
        if ($this->retraits->removeElement($retrait)) {
            // set the owning side to null (unless already changed)
            if ($retrait->getCompteRetrait() === $this) {
                $retrait->setCompteRetrait(null);
            }
        }

        return $this;
    }
}
