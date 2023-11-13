<?php

namespace App\Entity;

use App\Repository\EntrepriseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]
class Entreprise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $denomination = null;


    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: ConfigApp::class)]
    private Collection $configApps;


    public function __construct()
    {

        $this->configApps = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDenomination(): ?string
    {
        return $this->denomination;
    }

    public function setDenomination(string $denomination): self
    {
        $this->denomination = $denomination;

        return $this;
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

    /**
     * @return Collection<int, ConfigApp>
     */
    public function getConfigApps(): Collection
    {
        return $this->configApps;
    }

    public function addConfigApp(ConfigApp $configApp): static
    {
        if (!$this->configApps->contains($configApp)) {
            $this->configApps->add($configApp);
            $configApp->setEntreprise($this);
        }

        return $this;
    }

    public function removeConfigApp(ConfigApp $configApp): static
    {
        if ($this->configApps->removeElement($configApp)) {
            // set the owning side to null (unless already changed)
            if ($configApp->getEntreprise() === $this) {
                $configApp->setEntreprise(null);
            }
        }

        return $this;
    }

}
