<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(type: 'simple_array')]
    private array $authors = [];

    #[ORM\Column(type: 'string',length: 13,nullable: false)]
    private string $isbn;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(type: 'date_immutable')]
    private \DateTimeInterface $publicationData;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $meap = false;

    /**
     * @var Collection<BookCategory>
     */
    #[ORM\ManyToMany(targetEntity: BookCategory::class)]
    #[ORM\JoinTable(name: 'book_to_book_category')]
    private Collection $categories;

    /**
     * @var Collection<BookToBookFormat>
     */
    #[ORM\OneToMany(mappedBy: 'book', targetEntity: BookToBookFormat::class)]
    private Collection $formats;

    /**
     * @var Collection<Review>
     */
    #[ORM\OneToMany(mappedBy: 'book', targetEntity: Review::class)]
    private Collection $reviews;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->formats = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getAuthors(): array
    {
        return $this->authors;
    }

    public function setAuthors(array $authors): self
    {
        $this->authors = $authors;

        return $this;
    }

    public function getPublicationData(): \DateTimeInterface
    {
        return $this->publicationData;
    }

    public function setPublicationData(\DateTimeInterface $publicationData): self
    {
        $this->publicationData = $publicationData;

        return $this;
    }

    public function isMeap(): bool
    {
        return $this->meap;
    }

    public function setMeap(bool $meap): self
    {
        $this->meap = $meap;

        return $this;
    }

    /**
     * @return Collection<BookCategory>
     */
    public function getCategories(): ArrayCollection|Collection
    {
        return $this->categories;
    }

    /**
     * @param Collection<BookCategory> $categories
     * @return $this
     */
    public function setCategories(Collection $categories): self
    {
        $this->categories = $categories;

        return $this;

    }

    public function getIsbn(): string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }


    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getFormats(): Collection
    {
        return $this->formats;
    }

    public function setFormats(Collection $formats): self
    {
        $this->formats = $formats;

        return $this;
    }

    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function setReviews(Collection $reviews): self
    {
        $this->reviews = $reviews;

        return $this;
    }
}
