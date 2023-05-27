<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTest;

class AdminController extends AbstractControllerTest
{

    public function testGrantAuthor(): void
    {
        $user = $this->createUser('user@test.com','testtest');

        $username = 'admin@test.com';
        $password = 'testtest';


        // Создали пользователя с правами админа
        $admin = $this->createAdmin($username,$password);

        // Аутентифицировались
        $this->auth($username,$password);

        $this->client->request('POST','/api/v1/admin/grantAuthor/' . $user->getId());

        $this->assertResponseIsSuccessful();
    }
}