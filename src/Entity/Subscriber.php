<?php

namespace App\Entity;

use App\Repository\SubscriberRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;
use phpDocumentor\Reflection\Types\Integer;

// атрибут строчкой ниже позволяет нам делать какие-либо действия до того как наша сущность будет заперсистена(persist)
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: SubscriberRepository::class)]
class Subscriber
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(length: 255)]
    private string $email ;

    // datetime_immutable - чтобы пользователь не мог прибавить что-то к дате, а была возможность только ее перезаписать
    #[ORM\Column(type: "datetime_immutable")]
    private \DateTimeInterface $createdAt;

    // Преперсист обработчик, создает дату до того как наша дата будет заперсистена
    #[ORM\PrePersist]
    public function setCreatedValue(): void
    {
        $this->createdAt = new DateTimeImmutable();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }


}
