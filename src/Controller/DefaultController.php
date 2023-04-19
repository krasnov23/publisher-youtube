<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class DefaultController extends AbstractController
{
    public function __construct(private SerializerInterface $serializer )
    {
    }

    #[Route('/', name: 'app_default')]
    public function index(BookRepository $books): Response
    {

        // Данный код возвращает нам json с возможностью доступ к сущностям через сущности книги
        return $this->json(
            json_decode(
                $this->serializer->serialize(
                    $books->findAll(),
                    'json',
                    [AbstractNormalizer::IGNORED_ATTRIBUTES => ['book']]
                )));

    }

    #[Route('/new-book', name: 'new_book')]
    public function addNewBook(BookRepository $books): Response
    {
        $book = new Book();
        $book->setTitle('Pavel');

        $books->save($book, true);

        return new Response();
    }
}
