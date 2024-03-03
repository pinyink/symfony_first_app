<?php

namespace App\Entity;

use App\Repository\CrudDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CrudDetailRepository::class)]
class CrudDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $name = null;

    #[ORM\Column(length: 4)]
    private ?string $type = null;

    #[ORM\Column(nullable: true)]
    private ?array $setting = null;

    #[ORM\ManyToOne(inversedBy: 'crudDetails')]
    private ?Crud $crud = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getSetting(): ?array
    {
        return $this->setting;
    }

    public function setSetting(?array $setting): static
    {
        $this->setting = $setting;

        return $this;
    }

    public function getCrud(): ?Crud
    {
        return $this->crud;
    }

    public function setCrud(?Crud $crud): static
    {
        $this->crud = $crud;

        return $this;
    }
}
