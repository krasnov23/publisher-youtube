<?php

namespace App\Models;


// Класс для создания списка глав
class BookChapterTreeResponse
{
    /**
     * @param BookChapterModel[] $items
     */
    public function __construct(private array $items = [])
    {
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