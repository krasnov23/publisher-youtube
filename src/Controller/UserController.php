<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UserController extends AbstractController
{

    #[Route('/api/v1/user/me', name: 'app_user',methods: ['GET'])]
    public function me(#[CurrentUser] UserInterface $user): Response
    {
        return $this->json($user);
    }


}
