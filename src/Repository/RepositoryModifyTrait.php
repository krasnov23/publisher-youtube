<?php

namespace App\Repository;

trait RepositoryModifyTrait
{

    public function save(object $entity, bool $flush = false): void
    {
        // Отлавливаем ошибки в случае передачи не того типа
        assert($this->_entityName === get_class($entity));

        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(object $entity, bool $flush = false): void
    {
        // Отлавливаем ошибки в случае передачи не того типа
        assert($this->_entityName === get_class($entity));

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}