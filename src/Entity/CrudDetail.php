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

    #[ORM\ManyToOne(inversedBy: 'crudDetails')]
    private ?Crud $crud = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $type = null;

    #[ORM\Column(nullable: true)]
    private ?array $setting = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFieldName(): ?string
    {
        return $this->name;
    }

    public function setFieldName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): static
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
}
