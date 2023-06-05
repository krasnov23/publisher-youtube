<?php

namespace App\Service;

use App\Models\Author\PublishBookRequest;
use App\Repository\BookRepository;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;


class BookPublishService
{

    public function __construct(private BookRepository $bookRepository)
    {
    }

    // Метод обновляет дату публикации
    public function publish(int $id,PublishBookRequest $publishBookRequest): void
    {
        $this->setPublicationDate($id,$publishBookRequest->getDate());

        // С помощью метода ниже мы можем инициировать событие публикации
        // Что происходит когда вызывается эта строчка, она начинает дергать обработчики
        // Мы можем создать кастомные обработчики под данное событие, мы уже делали это ранее, но ранее мы делали это
        // под события фреймворка (папка Listener( События которые слушают KernelEventListener)).
        // в services.yaml соответственно на одно событие kernel.event_listener мы можем повесить несколько обработчиков
        // ровно такая же логика будет здесь мы можем создать обработчик для твитера или просто для соцсетей, либо же в какую-то
        // внешнюю систему
        // В данном случае главное помнить, что у нас есть основное событие в нашем случае это обновление базы данных,
        // События же используются для каких-либо побочных действий, если мы отсылаем запрос куда-то во внешнюю систему это
        // является побочным действием
        //$this->eventDispatcher->dispatch(new PublishEvent($book));
    }

    // Метод снятия с публикации
    public function unpublish(int $id)
    {
        $this->setPublicationDate($id,null);
    }

    private function setPublicationDate(int $id, DateTimeInterface $dateTime): void
    {
        $book = $this->bookRepository->getBookById($id);

        $book->setPublicationData($dateTime);

        $this->bookRepository->save($book,true);
    }

}