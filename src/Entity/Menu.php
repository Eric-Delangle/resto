<?php

namespace App\Entity;

use App\Entity\Purchase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\MenuRepository;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=MenuRepository::class)
 */
class Menu
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $entree;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $plat;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fromage;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $dessert;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $boisson;



    /**
     * @ORM\Column(type="integer", length=255)
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)hgcv 
     */
    private $slug;

    /**
     * @ORM\ManyToMany(targetEntity=Purchase::class, mappedBy="menu",cascade={"remove"})
     */
    private $purchases;

    public function __construct()
    {
        $this->purchases = new ArrayCollection();
    }




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEntree(): ?string
    {
        return $this->entree;
    }

    public function setEntree(string $entree): self
    {
        $this->entree = $entree;

        return $this;
    }

    public function getPlat(): ?string
    {
        return $this->plat;
    }

    public function setPlat(string $plat): self
    {
        $this->plat = $plat;

        return $this;
    }

    public function getFromage(): ?string
    {
        return $this->fromage;
    }

    public function setFromage(string $fromage): self
    {
        $this->fromage = $fromage;

        return $this;
    }

    public function getDessert(): ?string
    {
        return $this->dessert;
    }

    public function setDessert(string $dessert): self
    {
        $this->dessert = $dessert;

        return $this;
    }

    public function getBoisson(): ?string
    {
        return $this->boisson;
    }

    public function setBoisson(string $boisson): self
    {
        $this->boisson = $boisson;

        return $this;
    }


    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return Collection|Purchase[]
     */
    public function getPurchases(): Collection
    {
        return $this->purchases;
    }

    public function addPurchase(Purchase $purchase): self
    {
        if (!$this->purchases->contains($purchase)) {
            $this->purchases[] = $purchase;
            $purchase->addMenu($this);
        }

        return $this;
    }

    public function removePurchase(Purchase $purchase): self
    {
        if ($this->purchases->removeElement($purchase)) {
            $purchase->removeMenu($this);
        }

        return $this;
    }
}
