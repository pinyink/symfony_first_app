<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[UniqueEntity(fields:['url'], message:'This Url is already used.')]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?User $user = null;

    #[ORM\Column(length: 64)]
    private ?string $url = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $summary = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $modified = null;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: PostToCategories::class)]
    private Collection $postToCategories;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    private ?File $sampul = null;

    #[ORM\Column(nullable: true)]
    private ?int $publish = null;

    #[ORM\Column]
    private ?int $views = null;

    /**
     * @var Collection<int, PostStatistic>
     */
    #[ORM\OneToMany(mappedBy: 'Post', targetEntity: PostStatistic::class)]
    private Collection $postStatistics;

    public function __construct()
    {
        $this->postToCategories = new ArrayCollection();
        $this->postStatistics = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $slugger = new AsciiSlugger();
        $url = $slugger->slug($url);
        $this->url = $url;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): static
    {
        $this->summary = $summary;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getModified(): ?\DateTimeInterface
    {
        return $this->modified;
    }

    public function setModified(\DateTimeInterface $modified): static
    {
        $this->modified = $modified;

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
            $postToCategory->setPost($this);
        }

        return $this;
    }

    public function removePostToCategory(PostToCategories $postToCategory): static
    {
        if ($this->postToCategories->removeElement($postToCategory)) {
            // set the owning side to null (unless already changed)
            if ($postToCategory->getPost() === $this) {
                $postToCategory->setPost(null);
            }
        }

        return $this;
    }

    public function getSampul(): ?File
    {
        return $this->sampul;
    }

    public function setSampul(?File $sampul): static
    {
        $this->sampul = $sampul;

        return $this;
    }

    public function getPublish(): ?int
    {
        return $this->publish;
    }

    public function setPublish(?int $publish): static
    {
        $this->publish = $publish;

        return $this;
    }

    public function getViews(): ?int
    {
        return $this->views;
    }

    public function setViews(int $views): static
    {
        $this->views = $views;

        return $this;
    }

    /**
     * @return Collection<int, PostStatistic>
     */
    public function getPostStatistics(): Collection
    {
        return $this->postStatistics;
    }

    public function addPostStatistic(PostStatistic $postStatistic): static
    {
        if (!$this->postStatistics->contains($postStatistic)) {
            $this->postStatistics->add($postStatistic);
            $postStatistic->setPost($this);
        }

        return $this;
    }

    public function removePostStatistic(PostStatistic $postStatistic): static
    {
        if ($this->postStatistics->removeElement($postStatistic)) {
            // set the owning side to null (unless already changed)
            if ($postStatistic->getPost() === $this) {
                $postStatistic->setPost(null);
            }
        }

        return $this;
    }
}
