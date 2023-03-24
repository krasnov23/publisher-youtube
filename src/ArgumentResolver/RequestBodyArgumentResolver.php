<?php

namespace App\ArgumentResolver;

use App\Attribute\RequestBody;
use App\Exceptions\RequestBodyConvertException;
use App\Exceptions\ValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class RequestBodyArgumentResolver implements ValueResolverInterface
{
    public function __construct(private SerializerInterface $serializer, private ValidatorInterface $validator)
    {

    }


    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        // Логика которая указана ниже данного условия вызывается только тогда когда, обнаружен атрибут RequestBody
        // (он находится например в SubscribeController)
        if (!$argument->getAttributesOfType(RequestBody::class,ArgumentMetadata::IS_INSTANCEOF))
        {
            return [];
        }

        try {
            // Десерилизируем тело запроса
            $model = $this->serializer->deserialize($request->getContent(),
                // $argument->getType() - вернет класс SubscriberRequest
                $argument->getType(),
                JsonEncoder::FORMAT);
        } catch (\Throwable $throwable)
        {
            // В этот раз сохраняем оригинальное исключение с помощью $throwable
            throw new RequestBodyConvertException($throwable);
        }

        // С помощью алгоритмов выше наш запрос был десереалиризирован, теперь нужно его отвалидировать

        $errors = $this->validator->validate($model);

        // Если ошибки при валидации у нас есть, то мы передадим список ошибок

        if (count($errors) > 0){
            // Если человек действительно попадает в это исключение когда он не проходит валидацию
            // наш дефолтный обработчик, который мы писали в (урок 7 ютуб или исключения.txt) выдаст нам internal error
            // и распишет trace и врятли мы сможем понять что случилось, поэтому нам необходимо написать еще один обработчик
            // чтобы понять что случилось, где произошла ошибка итд.
            throw new ValidationException($errors);
        }


        return [$model];

    }

}