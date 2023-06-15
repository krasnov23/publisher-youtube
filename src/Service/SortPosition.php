<?php

namespace App\Service;

// Сортировка может быть в начале либо между элементами, либо в конце
enum SortPosition
{
    case AsFirst;

    case Between;

    case AsLast;
}
