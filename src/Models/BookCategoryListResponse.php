<?php

namespace App\Models;

class BookCategoryListResponse
{
    /**
     * @var BookCategoryModel[]
     */
    private array $items;

    /**
     * @param BookCategoryModel[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return BookCategoryModel[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param BookCategoryModel[] $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }
}
