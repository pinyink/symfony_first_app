<?php

namespace App\Entity;

use App\Repository\PublisherRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PublisherRepository::class)]
class Publisher
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $name = null;

    #[ORM\Column(length: 128)]
    private ?string $address = null;

    /**
     * @var Collection<int, Book>
     */
    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Book::class)]
    private Collection $books;

    /**
     * @var Collection<int, Book>
     */
    #[ORM\OneToMany(mappedBy: 'publisher', targetEntity: Book::class)]
    private Collection $bookpublisher;

    public function __construct()
    {
        $this->books = new ArrayCollection();
        $this->bookpublisher = new ArrayCollection();
    }

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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): static
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
            $book->setAuthor($this);
        }

        return $this;
    }

    public function removeBook(Book $book): static
    {
        if ($this->books->removeElement($book)) {
            // set the owning side to null (unless already changed)
            if ($book->getAuthor() === $this) {
                $book->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBookpublisher(): Collection
    {
        return $this->bookpublisher;
    }

    public function addBookpublisher(Book $bookpublisher): static
    {
        if (!$this->bookpublisher->contains($bookpublisher)) {
            $this->bookpublisher->add($bookpublisher);
            $bookpublisher->setPublisher($this);
        }

        return $this;
    }

    public function removeBookpublisher(Book $bookpublisher): static
    {
        if ($this->bookpublisher->removeElement($bookpublisher)) {
            // set the owning side to null (unless already changed)
            if ($bookpublisher->getPublisher() === $this) {
                $bookpublisher->setPublisher(null);
            }
        }

        return $this;
    }
}
