<?php

namespace App\Models;


class ReviewPage
{
    // Массив самих отзывов
    /**
     * @var ReviewModel[]
     */
    private array $items;

    private float $rating;

    // Текущая страница
    private int $page;

    // Количество страниц
    private int $amountOfPages;

    // Количество на страницу
    private int $amountPerPage;

    // Общее количество элементов
    private int $total;

    public function getItems(): array
    {
        return $this->items;
    }

    public function setItems(array $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function getRating(): float
    {
        return $this->rating;
    }

    public function setRating(float $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getAmountOfPages(): int
    {
        return $this->amountOfPages;
    }

    public function setAmountOfPages(int $amountOfPages): self
    {
        $this->amountOfPages = $amountOfPages;

        return $this;
    }

    public function getAmountPerPage(): int
    {
        return $this->amountPerPage;
    }

    public function setAmountPerPage(int $amountPerPage): self
    {
        $this->amountPerPage = $amountPerPage;

        return $this;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }
}