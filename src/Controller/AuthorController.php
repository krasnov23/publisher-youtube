<?php

namespace App\Controller;

use App\Attribute\RequestBody;
use App\Attribute\RequestFile;
use App\Models\Author\CreateBookRequest;
use App\Models\Author\PublishBookRequest;
use App\Models\Author\UploadCoverResponse;
use App\Service\AuthorService;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Models\Author\BookListResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ErrorResponse;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;


class AuthorController extends AbstractController
{
    public function __construct(private AuthorService $authorService)
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
     * @OA\RequestBody(@Model(type=PublishBookRequest::class))
     */
    #[Route(path: '/api/v1/author/book/{id}/publish', name:"app_publish_book", methods: ['POST'])]
    public function publish(int $id,#[RequestBody] PublishBookRequest $request): Response
    {
        $this->authorService->publish($id,$request);

        return $this->json(null);
    }

    /**
     * @OA\Tag(name="Author API")
     * @OA\Response(
     *     response=200,
     *     description="UnPublish a book",
     * )
     */
    #[Route(path: '/api/v1/author/book/{id}/unpublish', name:"app_unpublish_book", methods: ['POST'])]
    public function unpublish(int $id): Response
    {
        $this->authorService->unpublish($id);

        return $this->json(null);
    }

    /**
     * @OA\Tag(name="Author API")
     * @OA\Response(
     *     response=200,
     *     description="Get authors owned books",
     *     @Model(type=BookListResponse::class)
     * )
     *  @OA\Response(
     *     response=404,
     *     description="User not found",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path: '/api/v1/author/books', name:"app_get_all_books_by_one_author", methods: ['GET'])]
    public function books(): Response
    {
        return $this->json($this->authorService->getBooks());
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
     * @OA\RequestBody(@Model(type=CreateBookRequest::class))
     */
    #[Route(path: '/api/v1/author/create/book', name:"app_create_book", methods: ['POST'])]
    public function createBook(#[RequestBody] CreateBookRequest $request): Response
    {
        return $this->json($this->authorService->createBook($request));
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
     */
    #[Route(path: '/api/v1/author/book/{id}', name:"app_delete_authors_book", methods: ['DELETE'])]
    public function deleteBook(int $id): Response
    {
        $this->authorService->deleteBook($id);

        return $this->json(null);
    }



}