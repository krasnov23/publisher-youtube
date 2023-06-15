<?php

namespace App\Validation;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AtLeastOneRequiredValidator extends ConstraintValidator
{
    // Создает доступ к полям объекта
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->propertyAccessor = $propertyAccessor ?? PropertyAccess::createPropertyAccessor();
    }

    public function validate(mixed $object, Constraint $constraint)
    {
        // Проверяем что нам передали констреинт который мы ожидаем
        if (!$constraint instanceof AtLeastOneRequired)
        {
            throw new UnexpectedTypeException($constraint, AtLeastOneRequired::class);
        }

        // Смотрим в каких полях есть значения из переданных если все есть то окей, если поля не были переданы то идем дальше
        $passed = array_filter($constraint->requiredFields, function (string $required) use ($object)
        {   // Получаем доступ к полям передавая объект с атрибутом AtLeastOneRequired(UpdateBookChapterSortRequest) и
            // значения свойства requiredFields AtLeastOneRequired
            return null !== $this->propertyAccessor->getValue($object, $required);
        });

        if (!empty($passed)){
            return ;
        }

        // Формируем значение плейсхолдера для сообщения об ошибке, делаем строку из словаря
        $fieldsList = implode(',', $constraint->requiredFields);

        // Проходимся по полям и каждому заменяем сообщение об ошибке placeholder
        foreach ($constraint->requiredFields as $required){
            $this->context->buildViolation($constraint->message)
                // вставляем сообщение вместо fields в свойство message AtLeastOneRequired
                ->setParameter('{{ fields }}', $fieldsList)
                ->setCode(AtLeastOneRequired::ONE_REQUIRED_ERROR)
                ->atPath($required)
                ->addViolation();
        }
    }
}