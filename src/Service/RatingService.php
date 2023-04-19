<?php

namespace App\Service;

use App\Repository\ReviewRepository;

class RatingService
{

    public function __construct(private ReviewRepository $reviewRepository)
    {
    }


    public function calcReviewRatingForBook(int $id,int $total): float
    {
        // Возвращает средний рейтинг книги в случае если есть хотябы один отзыв
        return $total > 0 ? $this->reviewRepository->getBookTotalRatingSum($id) / $total : 0;
    }

}