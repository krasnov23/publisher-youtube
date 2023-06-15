<?php

namespace App\Validation;

use Attribute;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

// Attribute::TARGET_CLASS - Данный атрибут можно использовать только в классах
#[Attribute(Attribute::TARGET_CLASS)]
// Данный атрибут сделан для UpdateBookChapterSortRequest для того чтобы один из previousId или nextId был обязательно не пустым
class AtLeastOneRequired extends Constraint
{
    /**
     * @var string[]
     */
    public array $requiredFields;

    public string $message = 'At least one of {{ fields }} is required';

    public const ONE_REQUIRED_ERROR = 'c28db1f3-d1ac-4b61-a9d4-e4db3ef81215';

    protected static $errorNames = [
        self::ONE_REQUIRED_ERROR => 'ONE_REQUIRED_ERROR'
    ];

    // Переопределяем конструктор добавив новые аргументы
    public function __construct(array $options = [],
                                array $requiredFields = null,
                                string $message = null ,
                                array $groups = null,
                                mixed $payload = null)
    {
        // В теле конструктора проверяем первый аргумент если он передан то он станет required fields
        if (!empty($options) && array_is_list($options))
        {
            $requiredFields = $requiredFields ?? $options;
            $options = [];
        }

        // Если ничего не передано выбрасываем исключение в противном случае,
        // сохраняем, то что нам передали
        if (empty($requiredFields))
        {
            throw new ConstraintDefinitionException('The requiredFields of AtLeastOneRequired constraint
            cannot be empty');
        }

        $options['value'] = $requiredFields;

        parent::__construct($options, $groups, $payload);

        $this->requiredFields = $requiredFields;
        $this->message = $message ?? $this->message;
    }

    public function getRequiredOptions(): array
    {
        return ['requiredFields'];
    }

    public function getDefaultOption(): string
    {
        return 'requiredFields';
    }

    // Отмечает ограничение которое можно наложить на классы
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}