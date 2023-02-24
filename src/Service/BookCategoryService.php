<?php

namespace App\Service;

use App\Entity\BookCategory;
use App\Models\BookCategoryListItem;
use App\Models\BookCategoryListResponse;
use App\Repository\BookCategoryRepository;
use Doctrine\Common\Collections\Criteria;

class BookCategoryService
{

    public function __construct(private BookCategoryRepository $bookCategoryRepository)
    {

    }

    public function getCategories(): BookCategoryListResponse
    {
        // Ищет все книги, сортирует по названию категорий книги в алфавитном порядке, пустой список означает что у нас нет критерия
        // то есть не ищем категорию с конкретным названием
        $categories = $this->bookCategoryRepository->findBy([],['title'=> Criteria::ASC]);


        $items = array_map(
            // Передает каждый элемент категории и раскладывает его по айди названию и слагу передавая
            // В BookCategoryListItem.
            // То есть берет каждый элемент массива категорис и передает каждый параметр в сущность BookCategoryListItem,
            // сохраняя все это в список
            // array_map - берется список и каждый элемент списка перекладывается в то что происходит после знака =>
            fn (BookCategory $bookCategory) => new BookCategoryListItem(
                $bookCategory->getId(),$bookCategory->getTitle(),$bookCategory->getSlug()
            ), $categories
        );

        // В данном случае почему мы выбираем не просто массив, а передаем его как объект? Все потому что массив не расширяем
        // Массив будет выглядеть следующим образом: [{'id' => 1} , {'id' => 2 }] Если например кроме этого массива мы захотим
        // добавить массив какой-то общей категории или например еще общее количество книг, то мы не сможем этого сделать
        // поэтому объект является более расширяемой структурой. {'items': [{'id' => 1} , {'id' => 2 }], 'bookscount' : 123}
        // например если бы мы хотели добавить какой-нибудь дополнительный ключ мы бы просто добавили свойство bookscount например

        return  new BookCategoryListResponse($items);


    }

}