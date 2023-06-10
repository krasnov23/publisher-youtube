<?php

namespace App\Tests\Service;

use App\Entity\UserApi;
use App\Exceptions\UserAlreadyExistsException;
use App\Models\SignUpRequest;
use App\Repository\UserApiRepository;
use App\Service\SignUpService;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;

class SignUpServiceTest extends TestCase
{
    private UserPasswordHasher $hasher;

    private UserApiRepository $userRepository;

    private AuthenticationSuccessHandler $successHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hasher = $this->createMock(UserPasswordHasher::class);
        $this->userRepository = $this->createMock(UserApiRepository::class);
        $this->successHandler = $this->createMock(AuthenticationSuccessHandler::class);
    }

    private function createService(): SignUpService
    {
        return new SignUpService($this->hasher,$this->userRepository,$this->successHandler);
    }


    public function testSignUpUserAlreadyExists()
    {
        $this->expectException(UserAlreadyExistsException::class);

        $this->userRepository->expects($this->once())
            ->method('existsByEmail')
            ->with('test@test.com')
            ->willReturn(true);

        $this->createService()->signUp((new SignUpRequest())->setEmail('test@test.com'));
    }


    public function testSignUp()
    {
        $response = new Response();

        // Данный пользователь используется для хэширования
        $expectedHasherUser = (new UserApi())
            ->setEmail('test@test.com')
            ->setRoles(['ROLE_USER'])
            ->setFirstName('Vasya')
            ->setLastName('Pupkin');

        // Для персиста будет использоваться другой пользователь
        $expectedUser = clone $expectedHasherUser;
        $expectedUser->setPassword('hashed_password');

        $this->userRepository->expects($this->once())
            ->method('existsByEmail')
            ->with('test@test.com')
            ->willReturn(false);

        $this->hasher->expects($this->once())
            ->method('hashPassword')
            ->with($expectedHasherUser,'testtest')
            ->willReturn("hashed_password");


        $this->successHandler->expects($this->once())
            ->method("handleAuthenticationSuccess")
            ->with($expectedUser)
            ->willReturn($response);

        $this->userRepository->expects($this->once())
            ->method('save')
            ->with($expectedUser,true);

        $signUpRequest = (new SignUpRequest())
            ->setEmail('test@test.com')
            ->setFirstName('Vasya')
            ->setLastName('Pupkin')
            ->setEmail('test@test.com')
            ->setPassword('testtest');

        $this->assertEquals($response, $this->createService()->signUp($signUpRequest));

    }

    


}
