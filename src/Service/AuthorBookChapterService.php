<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\BookChapter;
use App\Exceptions\BookChapterInvalidException;
use App\Models\Author\CreateBookChapterRequest;
use App\Models\Author\UpdateBookChapterRequest;
use App\Models\Author\UpdateBookChapterSortRequest;
use App\Models\BookChapterModel;
use App\Models\BookChapterTreeResponse;
use App\Models\IdResponse;
use App\Repository\BookChapterRepository;
use App\Repository\BookRepository;
use Symfony\Component\String\Slugger\SluggerInterface;

class AuthorBookChapterService
{
    // Отвечает за максимальный уровень дерева
    private const MAX_LEVEL = 3;

    // Отвечает за шаг сортировки
    private const SORT_STEP = 1;

    // Минимальный уровень дерева (может быть и 0, но мы берем 1)
    private const MIN_LEVEL = 1;

    public function __construct(private BookRepository $bookRepository,
                                private BookChapterRepository $bookChapterRepository,
                                private SluggerInterface $slugger)
    {
    }

    public function createChapter(CreateBookChapterRequest $request,int $bookId): IdResponse
    {
        // Находим книгу по Id
        $book = $this->bookRepository->getBookById($bookId);

        // Получаем название главый которое хотим внести
        $title = $request->getTitle();

        // Получаем айди родителя к которому он относится (у раздела главы, у подраздела раздела)
        $parentId = $request->getParentId();

        $parent = null;

        // уровень - глава
        $level = self::MIN_LEVEL;

        if (null !== $parentId)
        {
            // Получаем сущность родителя по айди, если родителя нет, задаем NULL
            $parent = $this->bookChapterRepository->getBookById($parentId);

            $parentLevel = $parent->getLevel();

            // Если уровень родителя равен трем, что по сути не может быть потому что уровень родителя может быть максимум 2
            // Выбрасываем исключение
            if (self::MAX_LEVEL === $parentLevel)
            {
                throw new BookChapterInvalidException("max level is reached");
            }

            $level = $parentLevel + 1;
        }

        $chapter = (new BookChapter())
            ->setTitle($title)
            ->setSlug($this->slugger->slug($title))
            ->setParent($parent)
            ->setLevel($level)
            // Задает номер равный последнему номеру элемента + 1
            ->setSort($this->getNextMaxSort($book,$level))
            ->setBook($book);

        $this->bookChapterRepository->save($chapter,true);

        return new IdResponse($chapter->getId());
    }

    public function updateChapterSort(UpdateBookChapterSortRequest $bookChapterRequest): void
    {
        // Находим главу по айдишнику
        $chapter = $this->bookChapterRepository->getBookById($bookChapterRequest->getId());

        // Создаем контекст передавая опорные позиции элементов, между которых хотим поставить нашу главу
        $sortContext = SortContext::fromNeighbours($bookChapterRequest->getNextId(),$bookChapterRequest->getPreviousId());

        // Получаем главу по опорному элементу
        $nearChapter = $this->bookChapterRepository->getBookById($sortContext->getNearId());

        // Получаем уровень рядом стоящей главы
        $level = $nearChapter->getLevel();

        if (SortPosition::AsLast === $sortContext->getPosition())
        {
            // Берет максимальное значение сортировки на определенном уровне и прибавляет один ставя наш элемент в конец
            $sort = $this->getNextMaxSort($chapter->getBook(),$level);
        } else {
            // Если глава вставляется по сортировке между глав, то получаем сортировку соседа то есть в данном случае
            // последующей главы и увеличиваем все последующие главы на один начиная с соседней
            $sort = $nearChapter->getSort();
            $this->bookChapterRepository->increaseSortFrom($sort,$chapter->getBook(),$level,self::SORT_STEP);
        }

        // Назначаем уровень глава и номер сортировки.
        $chapter->setLevel($level)->setSort($sort)->setParent($nearChapter->getParent());

        $this->bookChapterRepository->save($chapter,true);

    }

    public function updateChapter(UpdateBookChapterRequest $chapterRequest): void
    {
        $chapter = $this->bookChapterRepository->getBookById($chapterRequest->getId());
        $title = $chapterRequest->getTitle();

        $chapter->setTitle($title)->setSlug($this->slugger->slug($title));

        $this->bookChapterRepository->save($chapter,true);

    }

    // Записали туду потому что забыли что оглавления должны вернуть на странице книги
    /**
     * @todo move somewhere to use from BookService
     */
    public function getChaptersTree(int $bookId): BookChapterTreeResponse
    {
        $book = $this->bookRepository->getBookById($bookId);
        $chapters = $this->bookChapterRepository->findSortedChapterByBook($book);
        $response = new BookChapterTreeResponse();

        /**
         * @var array<int, BookChapterModel> $index
         */
        // индекс нам нужен чтобы по паренту находить главу во вложенном списке
        $index = [];

        foreach ($chapters as $chapter)
        {
            $model = new BookChapterModel($chapter->getId(), $chapter->getTitle(),$chapter->getSlug());

            // Добавляем модели глав в пустой список
            $index[$chapter->getId()] = $model;

            // Если у главы нет родителя, то в BookChapterTreeResponse добавляется модель BookChapterModel
            if (!$chapter->hasParent())
            {
                $response->addItem($model);
                continue;
            }

            // Получаем родителя раздела или подраздела
            $parent = $chapter->getParent();

            // Добавляем в массив по айди родителя BookChapterModel - т.е разделы и подразделы относящиеся к определенной
            // главе, т.е в свойство items BookChapterModel добавляем разделы и подразделы моделью BookChapterModel
            $index[$parent->getId()]->addItem($model);

        }

        return $response;
    }

    public function deleteChapter(int $id): void
    {
        $chapter = $this->bookChapterRepository->getBookById($id);

        $this->bookChapterRepository->remove($chapter,true);
    }

    // Добавляем хелпер в сервис чтобы получить максимальное значение сортировки
    // Берем текущий максимум и прибавляем сорт степ
    private function getNextMaxSort(Book $book,int $level): int
    {
        return $this->bookChapterRepository->getMaxSort($book,$level) + self::SORT_STEP;
    }





}