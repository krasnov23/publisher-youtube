<?php

namespace App\Service;

use App\Repository\UserApiRepository;
use Doctrine\ORM\EntityManagerInterface;

class RoleService
{
    public function __construct(private UserApiRepository $userRepository,
                                private EntityManagerInterface $em)
    {
    }

    // Задает права Админа пользователю по id
    public function grandAdmin(int $userId): void
    {
        $this->grantRole($userId, 'ROLE_ADMIN');
    }

    // Задает права Автора пользователю по id.
    public function grandAuthor(int $userId): void
    {
        $this->grantRole($userId,'ROLE_AUTHOR');
    }

    private function grantRole(int $userId,string $role): void
    {
        $user = $this->userRepository->getUser($userId);
        $user->setRoles([$role]);

        $this->em->flush();

    }
}