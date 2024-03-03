<?php

namespace App\Entity;

use App\Repository\CrudRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CrudRepository::class)]
class Crud
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $entity_name = null;

    #[ORM\Column(length: 64)]
    private ?string $form_name = null;

    #[ORM\Column(length: 64)]
    private ?string $route_name = null;

    #[ORM\OneToMany(mappedBy: 'crud', targetEntity: CrudDetail::class)]
    private Collection $crudDetails;

    public function __construct()
    {
        $this->crudDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntityName(): ?string
    {
        return $this->entity_name;
    }

    public function setEntityName(string $entity_name): static
    {
        $this->entity_name = $entity_name;

        return $this;
    }

    public function getFormName(): ?string
    {
        return $this->form_name;
    }

    public function setFormName(string $form_name): static
    {
        $this->form_name = $form_name;

        return $this;
    }

    public function getRouteName(): ?string
    {
        return $this->route_name;
    }

    public function setRouteName(string $route_name): static
    {
        $this->route_name = $route_name;

        return $this;
    }

    /**
     * @return Collection<int, CrudDetail>
     */
    public function getCrudDetails(): Collection
    {
        return $this->crudDetails;
    }

    public function addCrudDetail(CrudDetail $crudDetail): static
    {
        if (!$this->crudDetails->contains($crudDetail)) {
            $this->crudDetails->add($crudDetail);
            $crudDetail->setCrud($this);
        }

        return $this;
    }

    public function removeCrudDetail(CrudDetail $crudDetail): static
    {
        if ($this->crudDetails->removeElement($crudDetail)) {
            // set the owning side to null (unless already changed)
            if ($crudDetail->getCrud() === $this) {
                $crudDetail->setCrud(null);
            }
        }

        return $this;
    }
}
