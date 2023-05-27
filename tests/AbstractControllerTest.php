<?php

namespace App\Tests;

use App\Entity\UserApi;
use Doctrine\ORM\EntityManagerInterface;
use Helmich\JsonAssert\JsonAssertions;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class AbstractControllerTest extends WebTestCase
{

    use JsonAssertions;

    protected KernelBrowser $client;

    protected ?EntityManagerInterface $em;

    protected UserPasswordHasherInterface $hasher;

    protected function setUp(): void
    {
        parent::setUp();

        // Создали клиента
        $this->client = static::createClient();

        $this->em = self::getContainer()->get('doctrine.orm.entity_manager');

        $this->hasher = self::getContainer()->get('security.user_password_hasher');
    }

    protected function tearDown(): void
    {

        parent::tearDown();

        $this->em->close();

        $this->em = null;

    }

    // Метод который мы будем создавать перед методом, в котором он должен как бы авторизовать пользователя
    protected function auth(string $username, string $password): void
    {
        $this->client->request('POST','/api/v1/auth/login',
            [],
            [],
            // Задает заголовки запроса
            ['CONTENT-TYPE' => 'application/json'],
            json_encode([
                'username' => $username,
                'password' => $password,
            ])
            );


        $this->assertResponseIsSuccessful();

        // Возьмем payload из ответа и превратим его в массив
        $data = json_encode($this->client->getResponse()->getContent(),true);

        // Подставим для всех будущим запросов наш токен из ответа
        $this->client->setServerParameter('HTTP_Authorization',sprintf('Bearer %s',$data['token']));

    }

    protected function createUser(string $userName, string $password): UserApi
    {
        return $this->createUserWithRoles($userName, $password,['ROLE_USER']);
    }

    protected function createAdmin(string $userName, string $password): UserApi
    {
        return $this->createUserWithRoles($userName, $password,['ROLE_ADMIN']);
    }

    protected function createAuthor(string $userName, string $password): UserApi
    {
        return $this->createUserWithRoles($userName, $password,['ROLE_AUTHOR']);
    }

    private function createUserWithRoles(string $username, string $password, array $roles): UserApi
    {
        $user = (new UserApi())
            ->setRoles($roles)
            ->setLastName($username)
            ->setFirstName($username)
            ->setEmail($username);

        $user->setPassword($this->hasher->hashPassword($user,$password));

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }




}