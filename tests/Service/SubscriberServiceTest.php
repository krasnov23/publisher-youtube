<?php

namespace src\Service;

use App\Entity\Subscriber;
use App\Exceptions\SubscriberAlreadyExistsException;
use App\Models\SubscriberRequest;
use App\Repository\SubscriberRepository;
use App\Service\SubscriberService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class SubscriberServiceTest extends TestCase
{

    private SubscriberRepository $repository;

    private EntityManagerInterface $em;

    private const EMAIL = 'test@test.com';

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->createMock(SubscriberRepository::class);

        $this->em = $this->createMock(EntityManagerInterface::class);
    }

    // Тест subscribe при отсутствие емейла в базе его сохранит
    public function testSubscriberAlreadyExists()
    {
        // Ожидаем исключение
        $this->expectException(SubscriberAlreadyExistsException::class);

        // Задаем поведение репозиторию
        $this->repository->expects($this->once())
            ->method('existsByEmail')
            ->with(self::EMAIL)
            ->willReturn(true);

        // Создаем объект Request в который задаем email
        $request = new SubscriberRequest();
        $request->setEmail(self::EMAIL);

        // Отправляем все эти объекты в наш сервис
        (new SubscriberService($this->repository, $this->em))->subscribe($request);

    }

    public function testSubscribe()
    {
        // Задаем поведение репозиторию
        $this->repository->expects($this->once())
            ->method('existsByEmail')
            ->with(self::EMAIL)
            ->willReturn(false);

        // Создали нашего подписчика
        $expectedSubscriber = new Subscriber();
        $expectedSubscriber->setEmail(self::EMAIL);

        $this->em->expects($this->once())
            ->method('persist')
            ->with($expectedSubscriber);

        $this->em->expects($this->once())
            ->method('flush');

        // Создаем объект Request в который задаем email
        $request = new SubscriberRequest();
        $request->setEmail(self::EMAIL);

        // Отправляем все эти объекты в наш сервис
        (new SubscriberService($this->repository, $this->em))->subscribe($request);

    }



}
