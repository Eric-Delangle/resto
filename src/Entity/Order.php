<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=Menu::class, mappedBy="commande")
     */
    private $name;

    public function __construct()
    {
        $this->name = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Menu[]
     */
    public function getName(): Collection
    {
        return $this->name;
    }

    public function addName(Menu $name): self
    {
        if (!$this->name->contains($name)) {
            $this->name[] = $name;
            $name->setCommande($this);
        }

        return $this;
    }

    public function removeName(Menu $name): self
    {
        if ($this->name->removeElement($name)) {
            // set the owning side to null (unless already changed)
            if ($name->getCommande() === $this) {
                $name->setCommande(null);
            }
        }

        return $this;
    }
}
