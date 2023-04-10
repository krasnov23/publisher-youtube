<?php

namespace App\Entity;

use App\Repository\BookToBookFormatRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookToBookFormatRepository::class)]
class BookToBookFormat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // precision общее количество знаков в числе , scale количество знаков после запятой до которых округлится число
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $price;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $discountPercent;

    // inverseBy это значит что со стороны сущности Book мы будем ждать formats
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: Book::class,inversedBy: 'formats')]
    private Book $book;

    // В данном случае inversedBy не нужен, так как нам не нужна связь от BookFormat к этому маппингу, нам не нужно имея
    // BookFormat получать маппинг потому что мы будем получать все это из книги.
    // EAGER - означает что
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: BookFormat::class,fetch: 'EAGER')]
    private BookFormat $format;

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDiscountPercent(): ?int
    {
        return $this->discountPercent;
    }

    public function setDiscountPercent(?int $discountPercent): self
    {
        $this->discountPercent = $discountPercent;

        return $this;
    }

    public function getBook(): Book
    {
        return $this->book;
    }

    public function setBook(Book $book): self
    {
        $this->book = $book;

        return $this;
    }

    public function getFormat(): BookFormat
    {
        return $this->format;
    }

    public function setFormat(BookFormat $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
