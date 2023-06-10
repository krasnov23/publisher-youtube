<?php

namespace App\DataFixtures;

use App\Entity\BookFormat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BookFormatFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $format = (new BookFormat())
            ->setTitle('Format1')
            ->setDescription('This is first format')
            ->setComment(null);

        $format2 = (new BookFormat())
            ->setTitle('Format2')
            ->setDescription('This is second format')
            ->setComment('Some commend for second format');

        $manager->persist($format);
        $manager->persist($format2);
        $manager->flush();
    }


}