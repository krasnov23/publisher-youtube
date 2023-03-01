<?php

namespace App\Models;


class BookListResponse
{
    /**
     * @var BookListItem[] $items
     */
    private array $items ;

    /**
     * @param BookListItem[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return BookListItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }


}