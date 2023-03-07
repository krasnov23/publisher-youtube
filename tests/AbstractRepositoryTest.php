<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class AbstractRepositoryTest extends KernelTestCase
{

    protected ?EntityManagerInterface $em;

    // Метод Сетап запускается перед каждым тестом
    protected function setUp(): void
    {
        parent::setUp();

        $this->em = self::getContainer()->get('doctrine.orm.entity_manager');

    }

    protected function getRepositoryForEntity(string $entityClass): mixed
    {

        // Данный метод мы сделали чисто для того чтобы не забывать что в этот метод мы передаем сущность, а не репозиторий
        return $this->em->getRepository($entityClass);
    }

    // Запускается после каждого теста
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->em->close();
    }

}