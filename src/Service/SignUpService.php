<?php

namespace App\Service;

use App\Entity\UserApi;
use App\Exceptions\UserAlreadyExistsException;
use App\Models\IdResponse;
use App\Models\SignUpRequest;
use App\Repository\UserApiRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SignUpService
{
    public function __construct(private UserPasswordHasherInterface $hasher,
                                private UserApiRepository $userRepository,
                                private AuthenticationSuccessHandler $successHandler)
    {

    }

    public function signUp(SignUpRequest $signUpRequest): Response
    {
        // Проверяем что емейл пользователя который хочет зарегистрироваться отсутствует в базе данных
        if ($this->userRepository->existsByEmail($signUpRequest->getEmail()))
        {
            throw new UserAlreadyExistsException();
        }

        $user = (new UserApi())
            ->setFirstName($signUpRequest->getFirstName())
            ->setLastName($signUpRequest->getLastName())
            ->setEmail($signUpRequest->getEmail())
            ->setRoles(['ROLE_USER']);

        // Передаем захэшированный пароль
        $user->setPassword($this->hasher->hashPassword($user, $signUpRequest->getPassword()));

        $this->userRepository->save($user,true);

        return $this->successHandler->handleAuthenticationSuccess($user);
    }




}