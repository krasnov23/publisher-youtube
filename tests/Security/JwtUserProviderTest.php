<?php

namespace App\Tests\Security;

use App\Entity\UserApi;
use App\Repository\UserApiRepository;
use App\Security\JwtUserProvider;
use App\Tests\AbstractTestCase;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class JwtUserProviderTest extends AbstractTestCase
{
    private UserApiRepository $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserApiRepository::class);
    }

    public function testSupportsClass(): void
    {
        $user = (new UserApi())->setEmail('test@test.com');

        $provider = new JwtUserProvider($this->userRepository);

        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'test@test.com'])
            ->willReturn($user);

        $this->assertEquals($user,$provider->loadUserByIdentifier('test@test.com'));
    }

    public function testLoadUserByIdentifierNotFoundException(): void
    {
        $this->expectException(UserNotFoundException::class);

        $provider = new JwtUserProvider($this->userRepository);

        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'test@test.com'])
            ->willReturn(null);

        $provider->loadUserByIdentifier('test@test.com');
    }

    public function testLoaderByIdentifierAndPayload(): void
    {
        $user = (new UserApi());

        $provider = new JwtUserProvider($this->userRepository);

        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => 1])
            ->willReturn($user);

        $this->assertEquals($user,$provider->loadUserByIdentifierAndPayload("doesnt matter",['id' => 1]));
    }

    public function testLoadUserByIdentifierAndPayloadNotFoundException(): void
    {
        $this->expectException(UserNotFoundException::class);

        $provider = new JwtUserProvider($this->userRepository);

        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => 1])
            ->willReturn(null);

        $provider->loadUserByIdentifierAndPayload('doesnt matter',['id' => 1]);
    }





}