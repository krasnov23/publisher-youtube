<?php

namespace App\Service;

use App\Entity\User;
use App\Exceptions\UserAlreadyExistsException;
use App\Models\IdResponse;
use App\Models\SignUpRequest;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SignUpService
{
    public function __construct(private UserPasswordHasherInterface $hasher,
                                private UserRepository $userRepository,
                                private EntityManagerInterface $em)
    {

    }

    public function signUp(SignUpRequest $signUpRequest): IdResponse
    {
        // Проверяем что емейл пользователя который хочет зарегистрироваться отсутствует в базе данных
        if ($this->userRepository->existsByEmail($signUpRequest->getEmail()))
        {
            throw new UserAlreadyExistsException();
        }


        $user = (new User())
            ->setFirstName($signUpRequest->getFirstName())
            ->setLastName($signUpRequest->getLastName())
            ->setEmail($signUpRequest->getEmail());

        // Передаем захэшированный пароль
        $user->setPassword($this->hasher->hashPassword($user, $signUpRequest->getPassword()));

        $this->em->persist($user);

        $this->em->flush();

        return new IdResponse($user->getId());
    }


}