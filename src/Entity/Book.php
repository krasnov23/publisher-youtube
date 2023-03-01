<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

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

    #[ORM\Column(type: "simple_array")]
    private array $authors = [];

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $publicationData;

    #[ORM\Column(type: "boolean",options: ['default' => false])]
    private bool $meap = false;

    /**
     * @var Collection<BookCategory>
     */
    #[ORM\ManyToMany(targetEntity: BookCategory::class)]
    private Collection $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();

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
     * @return Collection<BookCategory>
     */
    public function setCategories(Collection $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

}
