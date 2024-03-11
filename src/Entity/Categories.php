<?php

namespace App\Entity;

use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CategoriesRepository::class)]
#[UniqueEntity('url')]
class Categories
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $summary = null;

    #[ORM\OneToMany(mappedBy: 'categories', targetEntity: PostToCategories::class)]
    private Collection $postToCategories;

    public function __construct()
    {
        $this->postToCategories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
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

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): static
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * @return Collection<int, PostToCategories>
     */
    public function getPostToCategories(): Collection
    {
        return $this->postToCategories;
    }

    public function addPostToCategory(PostToCategories $postToCategory): static
    {
        if (!$this->postToCategories->contains($postToCategory)) {
            $this->postToCategories->add($postToCategory);
            $postToCategory->setCategories($this);
        }

        return $this;
    }

    public function removePostToCategory(PostToCategories $postToCategory): static
    {
        if ($this->postToCategories->removeElement($postToCategory)) {
            // set the owning side to null (unless already changed)
            if ($postToCategory->getCategories() === $this) {
                $postToCategory->setCategories(null);
            }
        }

        return $this;
    }
}
