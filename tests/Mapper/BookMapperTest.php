<?php

namespace App\Tests\Mapper;

use App\Entity\Book;
use App\Mapper\BookMapper;
use App\Models\BookDetails;
use App\Tests\AbstractTestCase;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class BookMapperTest extends AbstractTestCase
{


    public function testMap(): void
    {
        $book = (new Book())->setTitle('title')->setSlug('slug')
            ->setImage('123')->setAuthors(['tester'])
            ->setPublicationData(new DateTimeImmutable('2020-10-10'));

        $this->setEntityId($book,1);

        $expected = (new BookDetails())->setId(1)->setSlug('slug')->setTitle('title')
        ->setImage('123')->setAuthors(['tester'])->setPublicationData(1602288000);

        $this->assertEquals($expected,BookMapper::map($book,new BookDetails()));
    }



}
