<?php

namespace App\Entity;

use App\Entity\Agence;
use App\Entity\Transaction;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Unique;
use Symfony\Component\Serializer\Annotation\SerializedName;


/**
 * @UniqueEntity({"telephone"})
 * @ApiResource()
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\Column(type="json")
     * @Groups({"agence_add_user"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="empty password")
     */
    private $password;

    /**
     * @SerializedName("password")
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=255 )
     * @Groups({"agence_write","agence_add_user"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"agence_write","agence_add_user"})
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"agence_write","agence_add_user"})
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=30,nullable=false)
     * @Assert\Regex(
     *     pattern="/((\+221|00221)?)((7[7608][0-9]{7}$)|(3[03][98][0-9]{6}$))/",
     *     match=true,
     *     message="Invalid phone number(Ex. 771234567)"
     * )
     * @Assert\NotBlank(message="empty phone number")
     * @Assert\Unique(message="le telephone existe dans la base de données")
     * @Groups({"agence_write","agence_add_user"})
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"agence_write"})
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $statut="actif";

    /**
     * @ORM\ManyToOne(targetEntity=Agence::class, inversedBy="utilisateurs")
     */
    private $agence;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="sender")
     */
    private $transactions;

    public function __construct()
    {
        if (!$this->password) {
            $this->password = $this->password = password_hash('passe',PASSWORD_ARGON2ID);// a générer automatiquement;
        }
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->telephone;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(?string $role): self
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = password_hash($password,PASSWORD_ARGON2ID);
        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

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
            $transaction->setSender($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getSender() === $this) {
                $transaction->setSender(null);
            }
        }

        return $this;
    }

    // public function __toString(): string{
    //     return $this->prenom?$this->prenom.' '.$this->nom:$this->telephone;
    // }

}
