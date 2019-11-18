<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VisiteurRepository")
 */
class Visiteur
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=100)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $login;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mdp;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cp;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ville;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateEmbauche;

    /**
     * @ORM\Column(type="boolean")
     */
    private $comptable;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FicheFrais", mappedBy="idVisiteur")
     */
    private $ficheFrais;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LigneFraisForfait", mappedBy="idVisiteur")
     */
    private $ligneFraisForfaits;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LigneFraisHorsForfait", mappedBy="idVisiteur")
     */
    private $ligneFraisHorsForfaits;

    public function __construct()
    {
        $this->ficheFrais = new ArrayCollection();
        $this->ligneFraisForfaits = new ArrayCollection();
        $this->ligneFraisHorsForfaits = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }
    
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): self
    {
        $this->mdp = $mdp;

        return $this;
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

    public function getCp(): ?string
    {
        return $this->cp;
    }

    public function setCp(string $cp): self
    {
        $this->cp = $cp;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getDateEmbauche(): ?\DateTimeInterface
    {
        return $this->dateEmbauche;
    }

    public function setDateEmbauche(\DateTimeInterface $dateEmbauche): self
    {
        $this->dateEmbauche = $dateEmbauche;

        return $this;
    }

    public function isComptable(): ?bool
    {
        return $this->comptable;
    }

    public function setComptable(bool $comptable): self
    {
        $this->comptable = $comptable;

        return $this;
    }

    /**
     * @return Collection|FicheFrais[]
     */
    public function getFicheFrais(): Collection
    {
        return $this->ficheFrais;
    }

    public function addFicheFrai(FicheFrais $ficheFrai): self
    {
        if (!$this->ficheFrais->contains($ficheFrai)) {
            $this->ficheFrais[] = $ficheFrai;
            $ficheFrai->setIdVisiteur($this);
        }

        return $this;
    }

    public function removeFicheFrai(FicheFrais $ficheFrai): self
    {
        if ($this->ficheFrais->contains($ficheFrai)) {
            $this->ficheFrais->removeElement($ficheFrai);
            // set the owning side to null (unless already changed)
            if ($ficheFrai->getIdVisiteur() === $this) {
                $ficheFrai->setIdVisiteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|LigneFraisForfait[]
     */
    public function getLigneFraisForfaits(): Collection
    {
        return $this->ligneFraisForfaits;
    }

    public function addLigneFraisForfait(LigneFraisForfait $ligneFraisForfait): self
    {
        if (!$this->ligneFraisForfaits->contains($ligneFraisForfait)) {
            $this->ligneFraisForfaits[] = $ligneFraisForfait;
            $ligneFraisForfait->setIdVisiteur($this);
        }

        return $this;
    }

    public function removeLigneFraisForfait(LigneFraisForfait $ligneFraisForfait): self
    {
        if ($this->ligneFraisForfaits->contains($ligneFraisForfait)) {
            $this->ligneFraisForfaits->removeElement($ligneFraisForfait);
            // set the owning side to null (unless already changed)
            if ($ligneFraisForfait->getIdVisiteur() === $this) {
                $ligneFraisForfait->setIdVisiteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|LigneFraisHorsForfait[]
     */
    public function getLigneFraisHorsForfaits(): Collection
    {
        return $this->ligneFraisHorsForfaits;
    }

    public function addLigneFraisHorsForfait(LigneFraisHorsForfait $ligneFraisHorsForfait): self
    {
        if (!$this->ligneFraisHorsForfaits->contains($ligneFraisHorsForfait)) {
            $this->ligneFraisHorsForfaits[] = $ligneFraisHorsForfait;
            $ligneFraisHorsForfait->setIdVisiteur($this);
        }

        return $this;
    }

    public function removeLigneFraisHorsForfait(LigneFraisHorsForfait $ligneFraisHorsForfait): self
    {
        if ($this->ligneFraisHorsForfaits->contains($ligneFraisHorsForfait)) {
            $this->ligneFraisHorsForfaits->removeElement($ligneFraisHorsForfait);
            // set the owning side to null (unless already changed)
            if ($ligneFraisHorsForfait->getIdVisiteur() === $this) {
                $ligneFraisHorsForfait->setIdVisiteur(null);
            }
        }

        return $this;
    }
}
