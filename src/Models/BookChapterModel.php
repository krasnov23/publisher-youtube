<?php

namespace App\Models;

class BookChapterModel
{

    /**
     * @param BookChapterModel[] $items
     */
    public function __construct(private int $id,
                                private string $title,
                                private string $slug,
                                private array $items = [])
    {
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return BookChapterModel[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function addItem(BookChapterModel $bookChapter): void
    {
        $this->items[] = $bookChapter;
    }



}