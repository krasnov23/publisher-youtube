<?php


namespace App\Controller;

use App\Attribute\RequestBody;
use App\Attribute\RequestFile;
use App\Models\Author\CreateBookChapterRequest;
use App\Models\Author\CreateBookRequest;
use App\Models\Author\PublishBookRequest;
use App\Models\Author\UpdateBookChapterRequest;
use App\Models\Author\UpdateBookChapterSortRequest;
use App\Models\Author\UpdateBookRequest;
use App\Models\BookChapterTreeResponse;
use App\Models\Author\UploadCoverResponse;
use App\Security\Voters\AuthorBookVoter;
use App\Service\AuthorBookChapterService;
use App\Service\AuthorBookService;
use App\Models\Author\BookAuthorDetails;
use App\Service\BookPublishService;
use OpenApi\Annotations as OA;
use App\Models\IdResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Models\Author\BookListResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ErrorResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;


class AuthorController extends AbstractController
{
    public function __construct(private AuthorBookService $authorService,
                                private BookPublishService $bookPublishService,
                                private AuthorBookChapterService $bookChapterService)
    {
    }

    // Мы ожидаем что нам будет передан UploadedFile, на нем мы хотим провести валидацию, что эта картинка не ноль,
    // соответствует указанным форматам и не более одного мегабайта, и если валидатор пропускает это значит мы получаем
    // файл (UploadedFile)
    /**
     * @OA\Tag(name="Author API")
     * @OA\Response(
     *     response=200,
     *     description="Publish a book",
     *     @Model(type=UploadCoverResponse::class)
     * )
     *  @OA\Response(
     *     response=400,
     *     description="Validation Failed",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path: '/api/v1/author/book/{id}/uploadCover',methods: ['POST'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR ,subject: 'id')]
    public function uploadCover(
        int $id,
        #[RequestFile(field: 'cover',constraints: [
            new NotNull(),
            new Image(maxSize: '1M', mimeTypes: ['image/jpeg','image/png','image/jpg'])
        ])] UploadedFile $file
    ): Response
    {
        return $this->json($this->authorService->uploadCover($id, $file));
    }

    /**
     * @OA\Tag(name="Author API")
     * @OA\Response(
     *     response=200,
     *     description="Publish a book",
     * )
     *  @OA\Response(
     *     response=404,
     *     description="Book not found",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation Failed",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\RequestBody(@Model(type=PublishBookRequest::class))
     */
    #[Route(path: '/api/v1/author/book/{id}/publish', name:"app_publish_book", methods: ['POST'])]
    // Доступ будет дан тем у кого есть роль или право BOOK_PUBLISH, и принимает id, который он прокинет в некий обработчик
    // voter - это и есть обработчик
    #[IsGranted(AuthorBookVoter::IS_AUTHOR ,subject: 'id')]
    public function publish(int $id,#[RequestBody] PublishBookRequest $request): Response
    {
        $this->bookPublishService->publish($id,$request);

        return $this->json(null);
    }

    /**
     * @OA\Tag(name="Author API")
     * @OA\Response(
     *     response=200,
     *     description="UnPublish a book",
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation Failed",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path: '/api/v1/author/book/{id}/unpublish', name:"app_unpublish_book", methods: ['POST'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR ,subject: 'id')]
    public function unpublish(int $id): Response
    {
        $this->bookPublishService->unpublish($id);

        return $this->json(null);
    }

    /**
     * @OA\Tag(name="Author API")
     * @OA\Response(
     *     response=200,
     *     description="Get authors owned books",
     *     @Model(type=BookListResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation Failed",
     *     @Model(type=ErrorResponse::class)
     * )
     *  @OA\Response(
     *     response=404,
     *     description="User not found",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path: '/api/v1/author/books', name:"app_get_all_books_by_one_author", methods: ['GET'])]
    public function books(#[CurrentUser] UserInterface $user): Response
    {
        return $this->json($this->authorService->getBooks($user));
    }


    /**
     * @OA\Tag(name="Author API")
     * @OA\Response(
     *     response=200,
     *     description="Create a book",
     * )
     *  @OA\Response(
     *     response=404,
     *     description="Book not found",
     *     @Model(type=ErrorResponse::class)
     * )
     *
     * @OA\RequestBody(@Model(type=CreateBookRequest::class))
     */
    #[Route(path: '/api/v1/author/create/book', name:"app_create_book", methods: ['POST'])]
    public function createBook(#[RequestBody] CreateBookRequest $request, #[CurrentUser] UserInterface $user): Response
    {
        return $this->json($this->authorService->createBook($request,$user));
    }


    /**
     * @OA\Tag(name="Author API")
     * @OA\Response(
     *     response=200,
     *     description="Delete book",
     *     @Model(type=BookListResponse::class)
     * )
     *  @OA\Response(
     *     response=404,
     *     description="Book not found",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation Failed",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path: '/api/v1/author/book/{id}/delete', name:"app_delete_authors_book", methods: ['DELETE'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR ,subject: 'id')]
    public function deleteBook(int $id): Response
    {
        $this->authorService->deleteBook($id);

        return $this->json(null);
    }

    /**
     * @OA\Tag(name="Author API")
     * @OA\Response(
     *     response=200,
     *     description="Update a book",
     * )
     *  @OA\Response(
     *     response=404,
     *     description="Book not found",
     *     @Model(type=ErrorResponse::class))
     * @OA\Response(
     *     response=400,
     *     description="Validation Failed",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\RequestBody(@Model(type=UpdateBookRequest::class))
     */
    #[Route(path: '/api/v1/author/book/{id}/update', methods: ['POST'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR ,subject: 'id')]
    public function upDateBook(int $id, #[RequestBody] UpdateBookRequest $request): Response
    {
        $this->authorService->updateBook($id, $request);

        return $this->json(null);
    }

    /**
     * @OA\Tag(name="Author API")
     * @OA\Response(
     *     response=200,
     *     description="Delete book",
     *     @Model(type=BookAuthorDetails::class)
     * )
     * @OA\Response(
     *  response=404,
     *  description="book not found",
     *  @Model(type=ErrorResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation Failed",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path: '/api/v1/author/book/{id}', methods: ['GET'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR ,subject: 'id')]
    public function book(int $id): Response
    {
        return $this->json($this->authorService->getBook($id));
    }

    /**
     * @OA\Tag(name="Author API")
     * @OA\Response(
     *     response=200,
     *     description="Create a book chapter",
     *     @Model(type=IdResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation Failed",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\RequestBody(@Model(type=CreateBookChapterRequest::class))
     */
    #[Route(path: '/api/v1/author/book/{bookId}/chapter',methods: ['POST'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'bookId')]
    public function createBookChapter(#[RequestBody] CreateBookChapterRequest $bookChapterRequest, int $bookId): Response
    {
        return $this->json($this->bookChapterService->createChapter($bookChapterRequest, $bookId));
    }

    /**
     * @OA\Tag(name="Author API")
     * @OA\Response(
     *     response=200,
     *     description="Sort a book chapter",
     * )
     * @OA\Response(
     *  response=404,
     *  description="book's chapter not found",
     *  @Model(type=ErrorResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation Failed",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\RequestBody(@Model(type=UpdateBookChapterSortRequest::class))
     */
    #[Route(path: '/api/v1/author/book/{bookId}/chapterSort',methods: ['POST'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'bookId')]
    public function updateBookChapterSort(#[RequestBody] UpdateBookChapterSortRequest $bookChapterRequest): Response
    {
        $this->bookChapterService->updateChapterSort($bookChapterRequest);

        return $this->json(['response' => 'success']);
    }

    /**
     * @OA\Tag(name="Author API")
     * @OA\Response(
     *     response=200,
     *     description="Update a book chapter",
     * )
     * @OA\Response(
     *  response=404,
     *  description="book not found",
     *  @Model(type=ErrorResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation Failed",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\RequestBody(@Model(type=UpdateBookChapterRequest::class))
     */
    #[Route(path: '/api/v1/author/book/{bookId}/updateChapter',methods: ['POST'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'bookId')]
    public function updateBookChapter(#[RequestBody] UpdateBookChapterRequest $bookChapterRequest): Response
    {
        $this->bookChapterService->updateChapter($bookChapterRequest);

        return $this->json(['response' => 'success']);
    }

    /**
     * @OA\Tag(name="Author API")
     * @OA\Response(
     *     response=200,
     *     description="Get a book's chapters as tree",
     *     @Model(type=BookChapterTreeResponse::class)
     * )
     * @OA\Response(
     *  response=404,
     *  description="book not found",
     *  @Model(type=ErrorResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation Failed",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\RequestBody(@Model(type=UpdateBookChapterRequest::class))
     */
    #[Route(path: '/api/v1/author/book/{bookId}/getChaptersBook',methods: ['GET'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'bookId')]
    public function getChapters(int $bookId): Response
    {
        return $this->json($this->bookChapterService->getChaptersTree($bookId));
    }

    /**
     * @OA\Tag(name="Author API")
     * @OA\Response(
     *     response=200,
     *     description="Delete book's chapter",
     * )
     *  @OA\Response(
     *     response=404,
     *     description="book chapter not found",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path: '/api/v1/author/book/{bookId}/deleteChapter/{id}', name:"app_delete_chapter_book", methods: ['DELETE'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR ,subject: 'bookId')]
    public function deleteChapterBook(int $id,int $bookId): Response
    {
        $this->bookChapterService->deleteChapter($id);

        return $this->json(null);
    }



}