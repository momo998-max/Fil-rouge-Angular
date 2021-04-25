<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Services\TransactionCode;
use App\Controller\TransactionController;
use App\Repository\TransactionRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ApiResource(
 *    attributes={
 *          "denormalization_context"={"groups"={"transaction_write"},"enable_max_depth"=true}
 *      },
 *     collectionOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_UTILISATEUR')", 
 *              "security_message"="permission denied.",
 *              "path"="user/transactions",
 *          },
 *         "depot"={
 *              "method"="POST",
 *              "path"="/user/compte/depot",
 *              "security"="is_granted('ROLE_UTILISATEUR')", 
 *              "security_message"="permission denied.",
 *          },
 *         "getByCode"={
 *              "method"="GET",
 *              "security"="is_granted('ROLE_UTILISATEUR')", 
 *              "security_message"="permission denied.",
 *              "path"="/user/transaction/{code}",
 *              "normalization_context"={"groups"={"transaction_read"},"enable_max_depth"=true}
 *          },
 *          "calcul_frais"={
 *                  "method"="GET",
 *                  "path"="/user/{montant}/frais",
 *                  "security"="is_granted('ROLE_UTILISATEUR')", 
 *                  "security_message"="permission denied.",
 *          },
 *          "user_client_nci"={
 *              "method"="GET",
 *              "path"="/user/client/nci/{nci}",
 *              "security"="is_granted('ROLE_UTILISATEUR')", 
 *              "security_message"="permission denied.",
 *          },
 *          "user_client_phone"={
 *              "method"="GET",
 *              "path"="/user/client/phone/{phone}",
 *              "security"="is_granted('ROLE_UTILISATEUR')", 
 *              "security_message"="permission denied.",
 *          },
 *          "user_agence_compte"={
 *              "method"="GET",
 *              "path"="/user/agence/compte",
 *              "security"="is_granted('ROLE_UTILISATEUR')", 
 *              "security_message"="permission denied.",
 *          },
 *          "user_compte_transactions"={
 *              "method"="GET",
 *              "path"="/user/compte/transactions",
 *              "security"="is_granted('ROLE_UTILISATEUR')", 
 *              "security_message"="permission denied.",
 *          },
 *          "compte_mes_depots"={
 *              "method"="GET",
 *              "path"="/user/compte/depots",
 *              "security"="is_granted('ROLE_UTILISATEUR')", 
 *              "security_message"="permission denied.",
 *          },
 *          "compte_mes_retraits"={
 *              "method"="GET",
 *              "path"="/user/compte/retraits",
 *              "security"="is_granted('ROLE_UTILISATEUR')", 
 *              "security_message"="permission denied.",
 *          },
 *          "compte_all_transactions"={
 *              "method"="GET",
 *              "path"="/admin/compte/transactions",
 *              "security"="is_granted('ROLE_ADMINAGENCE')", 
 *              "security_message"="permission denied.",
 *          },
 *          "compte_all_depots"={
 *              "method"="GET",
 *              "path"="/admin/compte/alldepots",
 *              "security"="is_granted('ROLE_ADMINAGENCE')", 
 *              "security_message"="permission denied.",
 *          },
 *          "compte_all_retraits"={
 *              "method"="GET",
 *              "path"="/admin/compte/allretraits",
 *              "security"="is_granted('ROLE_ADMINAGENCE')", 
 *              "security_message"="permission denied.",
 *          },
 *     },
 *     
 *     itemOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_UTILISATEUR')", 
 *              "security_message"="Vous n'avez pas ces privileges.",
 *              "path"="admin/transactions/{id}",
 *              "requirements"={"id"="\d+"},
 *          },
 *          "cancel_transaction"={
 *              "method"="PUT",
 *              "path"="/user/transaction/{id}/cancel",
 *              "requirements"={"id"="\d+"},
 *              "security"="is_granted('ROLE_UTILISATEUR')", 
 *              "security_message"="permission denied.",
 *          },
 *          "retrait"={
 *              "method"="PUT",
 *              "controller"=TransactionController::class,
 *              "path"="/user/retrait/{id}",
 *              "security"="is_granted('ROLE_UTILISATEUR')", 
 *              "security_message"="permission denied.",
 *          }
 *     },
 * )
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "code":"exact", "sendAt":"partial", "retiredAt":"partial", "etat":"exact", "sendFrom.telephone":"exact", "sender.id":"exact", "montant":"exact"})
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 */
class Transaction extends TransactionCode
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"compte_details","transaction_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"compte_details","transaction_read"})
     */
    private $code;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message="amount is required")
     * @Assert\Type(
     *     type="numeric",
     *     message="value not valid."
     * )
     * @Groups({"transaction_write"})
     * @Groups({"compte_details","transaction_read"})
     */
    private $montant;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"compte_details","transaction_read"})
     */
    private $sendAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"compte_details","transaction_read"})
     */
    private $retiredAt;

    /**
     * @ORM\Column(type="float")
     * @Groups({"compte_details","transaction_read"})
     */
    private $frais;

    /**
     * @ORM\Column(type="float")
     */
    private $partDepot;

    /**
     * @ORM\Column(type="float")
     */
    private $partRetrait;

    /**
     * @ORM\Column(type="float")
     */
    private $partEtat;

    /**
     * @ORM\Column(type="float")
     */
    private $partSysteme;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $etat="not completed";

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="transactions", cascade={"persist"})
     * @Groups({"transaction_read"})
     * 
     * l'utilisateur qui réalise l'envoi
     */
    private $sender;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="transactions", cascade={"persist"})
     * @Groups({"transaction_read"})
     * 
     * l'utilisateur qui réalise le retrait
     */
    private $withdrawer;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="transactions", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="sender is required")
     * @Groups({"transaction_write","transaction_read"})
     */
    private $sendFrom;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="transactions", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="receiver is required")
     * @Groups({"transaction_write","transaction_read"})
     */
    private $sendTo;

    /**
     * @ORM\ManyToOne(targetEntity=Compte::class, inversedBy="transactions", cascade={"persist"})
     */
    private $compte;

    /**
     * @ORM\ManyToOne(targetEntity=Compte::class, inversedBy="retraits", cascade={"persist"})
     */
    private $compteRetrait;

    public function __construct(){
        $this->sendAt = new \DateTime();
        $this->code = $this->generatedCode();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getMontant(): ?float
    {
        return abs($this->montant);
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getSendAt(): ?\DateTimeInterface
    {
        return $this->sendAt;
    }

    public function setSendAt(\DateTimeInterface $sendAt): self
    {
        $this->sendAt = $sendAt;

        return $this;
    }

    public function getRetiredAt(): ?\DateTimeInterface
    {
        return $this->retiredAt;
    }

    public function setRetiredAt(\DateTimeInterface $retiredAt): self
    {
        $this->retiredAt = $retiredAt;

        return $this;
    }

    public function getFrais(): ?float
    {
        return $this->frais;
    }

    public function setFrais(float $frais): self
    {
        $this->frais = $frais;

        return $this;
    }

    public function getPartDepot(): ?float
    {
        return $this->partDepot;
    }

    public function setPartDepot(float $partDepot): self
    {
        $this->partDepot = $partDepot;

        return $this;
    }

    public function getPartRetrait(): ?float
    {
        return $this->partRetrait;
    }

    public function setPartRetrait(float $partRetrait): self
    {
        $this->partRetrait = $partRetrait;

        return $this;
    }

    public function getPartEtat(): ?float
    {
        return $this->partEtat;
    }

    public function setPartEtat(float $partEtat): self
    {
        $this->partEtat = $partEtat;

        return $this;
    }

    public function getPartSysteme(): ?float
    {
        return $this->partSysteme;
    }

    public function setPartSysteme(float $partSysteme): self
    {
        $this->partSysteme = $partSysteme;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getWithdrawer(): ?User
    {
        return $this->withdrawer;
    }

    public function setWithdrawer(?User $withdrawer): self
    {
        $this->withdrawer = $withdrawer;

        return $this;
    }

    public function getSendFrom(): ?Client
    {
        return $this->sendFrom;
    }

    public function setSendFrom(?Client $sendFrom): self
    {
        $this->sendFrom = $sendFrom;

        return $this;
    }

    public function getSendTo(): ?Client
    {
        return $this->sendTo;
    }

    public function setSendTo(?Client $sendTo): self
    {
        $this->sendTo = $sendTo;

        return $this;
    }

    public function getCompte(): ?Compte
    {
        return $this->compte;
    }

    public function setCompte(?Compte $compte): self
    {
        $this->compte = $compte;

        return $this;
    }

    public function getCompteRetrait(): ?Compte
    {
        return $this->compteRetrait;
    }

    public function setCompteRetrait(?Compte $compteRetrait): self
    {
        $this->compteRetrait = $compteRetrait;

        return $this;
    }
}
