<?php

namespace App\ArgumentResolver;

use App\Attribute\RequestFile;
use App\Exceptions\ValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestFileArgumentResolver implements ValueResolverInterface
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$argument->getAttributes(RequestFile::class,ArgumentMetadata::IS_INSTANCEOF))
        {
            return [];
        }

        /** @var RequestFile $attribute */
        $attribute = $argument->getAttributes(RequestFile::class,ArgumentMetadata::IS_INSTANCEOF)[0];

        // С помощью реквеста получаем наш файл, получаем мы его по ключу, а ключ берем из наших полей
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get($attribute->getField());

        // производим валидацию c учетом указанных ограничений
        $errors = $this->validator->validate($uploadedFile,$attribute->getConstraints());

        if (count($errors) > 0)
        {
            throw new ValidationException($errors);
        }

        return [$uploadedFile];

    }
}