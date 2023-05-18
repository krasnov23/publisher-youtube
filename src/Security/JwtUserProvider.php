<?php

namespace App\Security;

use App\Entity\UserApi;
use App\Repository\UserApiRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\PayloadAwareUserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;


class JwtUserProvider implements PayloadAwareUserProviderInterface
{
    public function __construct(private UserApiRepository $userRepository)
    {
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->getUser('email',$identifier);
    }

    public function loadUserByIdentifierAndPayload(string $identifier,array $payload): UserInterface
    {
        return $this->getUser('id',$payload['id']);
    }

    // Устаревший метод
    public function loadUserByUsernameAndPayload(string $username, array $payload): ?UserInterface
    {
        return null;
    }

    // Данный метод обычно используется в сессиях когда необходимо перезагрузить информацию о пользователе в сессию
    public function refreshUser(UserInterface $user): ?UserInterface
    {
        return null;
    }

    // Поддерживаем ли мы класс который к нам приходит
    public function supportsClass(string $class)
    {
        return $class === UserApi::class || is_subclass_of($class, UserApi::class);
    }


    private function getUser($key, $value): UserInterface
    {
        $user = $this->userRepository->findOneBy([$key => $value]);

        if (null === $user)
        {
            $e = new UserNotFoundException('User with id' . json_encode($value) . 'not found');
            $e->setUserIdentifier(json_encode($value));

            throw $e;
        }

        return $user;

    }
}