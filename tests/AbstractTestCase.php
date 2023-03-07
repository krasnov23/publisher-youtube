<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    protected function setEntityId(object $entity, int $value, $idField = 'id')
    {
        $class = new \ReflectionClass($entity);

        // Из нашей рефлексии класса нам нужно получить поле айди сделать его открытым, установить ему значение и вернуть обратно
        // в закрытое состояние
        $property = $class->getProperty($idField);
        $property->setAccessible(true);
        $property->setValue($entity, $value);
        $property->setAccessible(false);
    }
}
