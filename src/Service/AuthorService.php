<?php

namespace App\Service;

use App\Entity\Book;
use App\Exceptions\BookAlreadyExistsException;
use App\Models\Author\BookListItem;
use App\Models\Author\BookListResponse;
use App\Models\Author\CreateBookRequest;
use App\Models\Author\PublishBookRequest;
use App\Models\Author\UploadCoverResponse;
use App\Models\IdResponse;
use App\Repository\BookRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class AuthorService
{

    public function __construct(
        private EntityManagerInterface $em,
        private BookRepository $bookRepository,
        private SluggerInterface $slugger,
        private Security $security,
        private UploadService $uploadService
        //private EventDispatcherInterface $eventDispatcher
    )
    {
    }


    // Получаем id книги и загруженный файл.
    public function uploadCover(int $id, UploadedFile $uploadedFile): UploadCoverResponse
    {
        $book = $this->bookRepository->getUserBookById($id, $this->security->getUser());


        $oldImage = $book->getImage();

        $link = $this->uploadService->uploadBookFile($id,$uploadedFile);

        $book->setImage($link);

        $this->em->flush();

        // Если у нас уже есть загруженная в папку картинка значит нам необходимо ее удалить, для того чтобы не занимать
        // лишнее место
        if (null !== $oldImage)
        {
            $this->uploadService->deleteBookFile($book->getId(), basename($oldImage));
        }

        return new UploadCoverResponse($link);
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

    // Получение всех книг текущего пользователя
    public function getBooks(): BookListResponse
    {
        $user = $this->security->getUser();

        return new BookListResponse(
            array_map([$this,'map'],$this->bookRepository->findUserBooks($user))
        );
    }

    // Создание книги
    public function createBook(CreateBookRequest $request): IdResponse
    {
        // Генерируем слаг
        $slug = $this->slugger->slug($request->getTitle());

        if ($this->bookRepository->existsBySlug($slug))
        {
            throw new BookAlreadyExistsException();
        }

        $book = (new Book())
                ->setMeap(false)
                ->setTitle($request->getTitle())
                ->setSlug($slug)
                ->setPublicationData(new DateTimeImmutable('2020-10-10'))
                ->setUser($this->security->getUser());

        $this->em->persist($book);
        $this->em->flush();

        return new IdResponse($book->getId());
    }

    // Удаление определенной книги.
    public function deleteBook(int $id): void
    {
        $user = $this->security->getUser();

        $book = $this->bookRepository->getUserBookById($id, $user);

        $this->em->remove($book);
        $this->em->flush();
    }

    private function setPublicationDate(int $book, ?DateTimeInterface $dateTime): void
    {
        $book = $this->bookRepository->getUserBookById($book, $this->security->getUser());

        $book->setPublicationData($dateTime);

        $this->em->flush();
    }

    private function map(Book $book): BookListItem
    {
        return (new BookListItem())
            ->setId($book->getId())
            ->setSlug($book->getSlug())
            ->setImage($book->getImage())
            ->setTitle($book->getTitle());
    }


}