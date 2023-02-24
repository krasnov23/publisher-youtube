<?php

namespace App\Models;

use App\Models\BookCategoryListItem;

class BookCategoryListResponse
{
    /**
     * @var BookCategoryListItem[] $items
     */
    private array $items ;

    /**
     * @param BookCategoryListItem[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return BookCategoryListItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param BookCategoryListItem[] $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }


}