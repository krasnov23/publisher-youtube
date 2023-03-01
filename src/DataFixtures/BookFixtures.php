<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

// Для того чтобы наша фикстура срабатывала только тогда когда у нас уже есть категории нам необходимо реализовать интерфейс
// DependentFixtureInterface
class BookFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $androidCategory = $this->getReference(BookCategoryFixtures::ANDROID_CATEGORY);
        $devicesCategory = $this->getReference(BookCategoryFixtures::DEVICES_CATEGORY);

        $book = (new Book())->setTitle('RXJava for Android Developer')->setPublicationData(new \DateTime('2019-04-01'))
        ->setMeap(false)->setAuthors(['P.Novikov'])->setSlug('rxjava-for-android-developers')
        ->setCategories(new ArrayCollection([$androidCategory,$devicesCategory]))
        ->setImage('rx-java-hi.png');

        $manager->persist($book);
        $manager->flush();


    }

    // Указываем зависимость
    public function getDependencies(): array
    {
        return [
            BookCategoryFixtures::class
        ];
    }

}