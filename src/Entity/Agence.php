<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AgenceRepository;
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
 *          "normalization_context"={"enable_max_depth"=true}
 *      },
 *      collectionOperations={
 *          "get",
 *          "creer_agence"={
 *              "method"="POST",
 *              "path"="api/admin/agences/new",
 *              "security"="is_granted('ROLE_ADMINSYSTEME')",
 *              "security_message"="permission denied",
 *              "denormalization_context"={"groups"={"agence_write"}}
 *          }
 *      },
 *      itemOperations={
 *          "agence_get_user"={
 *              "method"="GET",
 *              "path"="/admin/agences/{id}",
 *              "requirements"={"id"="\d+"},
 *              "security"="is_granted('ROLE_ADMINAGENCE')", 
 *              "security_message"="permission denied.",
 *              "normalization_context"={"groups"={"agence_get_user"}},
 *         },
 *          "bloquer_agence"={
 *              "method"="PUT",
 *              "path"="/admin/agences/{id}/block",
 *              "requirements"={"id"="\d+"},
 *              "security"="is_granted('ROLE_ADMINSYSTEME')", 
 *              "security_message"="permission denied.",
 *              "denormalization_context"={"groups"={"agence_statut"}},
 *         },
 *          "debloquer_agence"={
 *              "method"="PUT",
 *              "path"="/admin/agences/{id}/enable",
 *              "requirements"={"id"="\d+"},
 *              "security"="is_granted('ROLE_ADMINSYSTEME')", 
 *              "security_message"="permission denied.",
 *              "denormalization_context"={"groups"={"agence_statut"}},
 *         },
 *          "agence_add_user"={
 *              "method"="PUT",
 *              "path"="/admin/agences/{id}/addUser",
 *              "requirements"={"id"="\d+"},
 *              "security"="is_granted('ROLE_ADMINAGENCE')", 
 *              "security_message"="permission denied.",
 *              "denormalization_context"={"groups"={"agence_add_user"}},
 *         },
 *      }
 * )
 * @ORM\Entity(repositoryClass=AgenceRepository::class)
 */
class Agence
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
     * @Assert\NotBlank(message="agence address is required")
     * @Groups({"compte_details"})
     * @Groups({"agence_write"})
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=255)
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     *     pattern="/((\+221|00221)?)((7[7608][0-9]{7}$)|(3[03][98][0-9]{6}$))/",
     *     match=true,
     *     message="Invalid phone number(Ex. 771234567)"
     * )
     * @Assert\NotBlank(message="agence number is required")
     * @Groups({"agence_write"})
     */
    private $telephone;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $lattitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $longitude;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Groups({"compte_details"})
     * @Groups({"agence_statut"})
     */
    private $statut="actif";

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="agence", cascade={"persist"})
     * @Groups({"agence_add_user","agence_get_user"})
     */
    private $utilisateurs;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="agence name is required")
     * @Groups({"compte_details"})
     * @Groups({"agence_write"})
     */
    private $nom;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="agences", cascade={"persist"})
     * @Assert\NotBlank(message="agence admin is required")
     * @Groups({"agence_write"})
     */
    private $administrateur;

    /**
     * @ORM\OneToOne(targetEntity=Compte::class, inversedBy="agence", cascade={"persist", "remove"})
     * @Assert\NotBlank(message="agence account is required")
     * @Groups({"agence_write"})
     */
    private $compte;

    public function __construct()
    {
        $this->admins = new ArrayCollection();
        $this->utilisateurs = new ArrayCollection();
        $this->comptes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

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

    public function getLattitude(): ?float
    {
        return $this->lattitude;
    }

    public function setLattitude(?float $lattitude): self
    {
        $this->lattitude = $lattitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

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

    /**
     * @return Collection|User[]
     */
    public function getUtilisateurs(): Collection
    {
        return $this->utilisateurs;
    }

    public function addUtilisateur(User $utilisateur): self
    {
        if (!$this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs[] = $utilisateur;
            $utilisateur->setAgence($this);
        }

        return $this;
    }

    public function removeUtilisateur(User $utilisateur): self
    {
        if ($this->utilisateurs->removeElement($utilisateur)) {
            // set the owning side to null (unless already changed)
            if ($utilisateur->getAgence() === $this) {
                $utilisateur->setAgence(null);
            }
        }

        return $this;
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

    // public function __toString(): string{
    //     return $this->nom.' '.$this->adresse;
    // }

    public function getAdministrateur(): ?User
    {
        return $this->administrateur;
    }

    public function setAdministrateur(?User $administrateur): self
    {
        $this->administrateur = $administrateur;

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
}
