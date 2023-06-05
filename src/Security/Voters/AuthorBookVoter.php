<?php

namespace App\Security\Voters;

use App\Repository\BookRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\The;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AuthorBookVoter extends Voter
{

    public const IS_AUTHOR = 'IS_AUTHOR';

    public function __construct(private BookRepository $bookRepository)
    {

    }

    // Проверяет что метод контроллера содержит self::IS_AUTHOR
    // Проверяем поддерживает ли воутер входящий объект или нет
    // Данный вотер срабатывает при следующем атрибуте #[IsGranted(AuthorBookVoter::IS_AUTHOR ,subject: 'id')]
    protected function supports(string $attribute, mixed $subject): bool
    {
        // Проверяем что нам передали права BOOK_PUBLISH
        if (self::IS_AUTHOR !== $attribute){
            return false;
        }

        // Поскольку subject мы получаем в виде строки, а наше значение должно возвращать интеджер
        // приводим получаемую строку в целое число
        return intval($subject) > 0;

    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // Передаем id книги и Автора
        return $this->bookRepository->existsUserBookById((int) $subject, $token->getUser());
    }
}