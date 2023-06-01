<?php

namespace App\Service;

use App\Exceptions\UploadFileInvalidTypeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

class UploadService
{
    private const LINK_BOOK_PATTERN = '/upload/book/%d/%s';

    // Переменная $uploadDir передается из services.yaml
    public function __construct(private FileSystem $fs,private string $uploadDir)
    {
    }

    // возвращаем ссылку на файл
    public function uploadBookFile(int $bookId,UploadedFile $file)
    {
        // Получаем расширение файла
        // guessExtension смотрит mime тип и по нему готовится определить какое расширение должно быть у файла
        // чтобы например нам не пытались просунуть .php вместо картинки
        $extension = $file->guessExtension();

        if (null === $extension)
        {
            throw new UploadFileInvalidTypeException();
        }

        // Составляем новое имя файла
        $uniqueName = Uuid::v4()->toRfc4122() . '.' . $extension;

        // Составляем путь по которому мы будем заливать файлы
        // DIRECTORY_SEPARATOR - это разделение по файловой системы папок, которое меняет свое значение взависимости от
        // операционной системы


        // Перемещаем файл в место постоянного хранения
        $file->move($this->getUploadPathForBook($bookId), $uniqueName);

        // Возвращает ссылку
        return sprintf(self::LINK_BOOK_PATTERN,$bookId,$uniqueName);
    }

    public function deleteBookFile(int $id, string $fileName): void
    {
        $uploadedPath = $this->getUploadPathForBook($id) . DIRECTORY_SEPARATOR . $fileName;

        $this->fs->remove($uploadedPath);
    }


    private function getUploadPathForBook(int $id): string
    {
        return $this->uploadDir . DIRECTORY_SEPARATOR . 'book' . DIRECTORY_SEPARATOR . $id ;
    }


}