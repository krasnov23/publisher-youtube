<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_default')]
    public function index(BookRepository $books): Response
    {
        return $this->json($books->findAll());
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
