<?php

namespace App\Models\Author;

use App\Validation\AtLeastOneRequired;
use Symfony\Component\Validator\Constraints\Positive;

#[AtLeastOneRequired(['nextId','previousId'])]
class UpdateBookChapterSortRequest
{
    // id - элемент который мы перемещаем, nextId и previousId это элементы между которыми мы вставляем перемещаемый элемент
    // Поскольку у нас не должно возникнуть случая когда и nextId и previousId NULL, поэтому создаем новый атрибут для валидации
    // AtLeastOneRequired -
    #[Positive]
    private int $id;

    #[Positive]
    private ?int $nextId;

    #[Positive]
    private ?int $previousId;


    public function getId(): int
    {
        return $this->id;
    }


    public function setId(int $id): void
    {
        $this->id = $id;
    }


    public function getNextId(): ?int
    {
        return $this->nextId;
    }

    public function setNextId(?int $nextId): void
    {
        $this->nextId = $nextId;
    }


    public function getPreviousId(): ?int
    {
        return $this->previousId;
    }


    public function setPreviousId(?int $previousId): void
    {
        $this->previousId = $previousId;
    }
}