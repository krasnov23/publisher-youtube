<?php

namespace App\Service;

use App\Entity\Book;
use App\Models\RecommendedBook;
use App\Models\RecommendedBookListResponse;
use App\Repository\BookRepository;
use App\Service\Recommendation\Model\RecommendationItem;
use App\Service\Recommendation\Model\RecommendationResponse;
use App\Service\Recommendation\RecommendationApiService;

class RecommendationService
{
    private const MAX_DESCRIPTION_LENGTH = 150;

    public function __construct(private BookRepository $bookRepository,
                                private RecommendationApiService $recommendationApiService,
                                )
    {
    }

    private function mapRecommended(Book $book): RecommendedBook
    {
        $description = $book->getDescription();

        $description = strlen($description) > self::MAX_DESCRIPTION_LENGTH? substr($description,0,
                self::MAX_DESCRIPTION_LENGTH) . '...'
                : $description;

        return (new RecommendedBook())
            ->setId($book->getId())
            ->setTitle($book->getTitle())
            ->setSlug($book->getSlug())
            ->setImage($book->getImage())
            ->setShortDescription($description);
    }

    public function getRecommendationsByBookId(int $bookId): RecommendedBookListResponse
    {
        // Получили айдишники рекомендаций
        $ids = array_map(fn (RecommendationItem $item) => $item->getId(),
            // getRecommendationByBookId возвращает нам модель RecommendationResponse которую мы получаем от внешнего сервиса
            $this->recommendationApiService->getRecommendationByBookId($bookId)->getRecommendations());

        // Создаем модель RecomendedBook полученную по айдишникам через статический метод
        return new RecommendedBookListResponse(array_map([$this,'mapRecommended'],
            $this->bookRepository->findBooksByIds($ids)));

    }




}