<?php

namespace App\Service;

class SortContext
{
    public function __construct(private SortPosition $position,private int $nearId)
    {
    }

    public static function fromNeighbours(?int $nextId, ?int $previousId): self
    {
        // Определим по значению позиции тип сортировки
        // Если выполняется условие null === $previousId && null !== $nextId значит SortPosition::AsFirst
        $position = match (true){
            null === $previousId && null !== $nextId => SortPosition::AsFirst,
            null !== $previousId && null === $nextId => SortPosition::AsLast,
            default => SortPosition::Between,
        };

        // Создаем СортКонтекст с типом сортировки и опорным элементом.
        // Если SortPosition последнее, то опорный элемент предыдущий в остальных случаях следующий
        return new self($position,SortPosition::AsLast === $position ? $previousId : $nextId);
    }

    public function getPosition(): SortPosition
    {
        return $this->position;
    }

    public function getNearId(): int
    {
        return $this->nearId;
    }


}