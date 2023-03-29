<?php

namespace App\Service;

use App\Entity\Subscriber;
use App\Exceptions\SubscriberAlreadyExistsException;
use App\Repository\SubscriberRepository;
use App\Models\SubscriberRequest;
use Doctrine\ORM\EntityManagerInterface;

class SubscriberService
{

    public function __construct(private SubscriberRepository $subscriberRepository, private EntityManagerInterface $entityManager)
    {

    }


    public function subscribe(SubscriberRequest $subscriberRequest)
    {
        // Проверяет есть ли email среди тех кто подписан на рассылку то есть (Есть ли email в базе данных ),
        // Если уже есть то выкидывает исключение
        if ($this->subscriberRepository->existsByEmail($subscriberRequest->getEmail()))
        {
            throw new SubscriberAlreadyExistsException();
        }

        $subscriber = new Subscriber();

        $subscriber->setEmail($subscriberRequest->getEmail());

        $this->entityManager->persist($subscriber);
        $this->entityManager->flush();

    }



}