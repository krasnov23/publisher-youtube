<?php

namespace App\Models;

class ErrorValidationDetails
{

    /**
     * @var ErrorValidationDetailsItem[]
     */
    private array $violations = [];

    public function addViolation(string $field, string $message): void
    {
        // На вход мы принимаем поле в котором произошла ошибка и сообщение само об ошибке все что мы принимаем будет
        // складировать в наш массив violations, но складировать мы будем не просто так, а нам потребуется дополнительный класс
        // который будет агрегировать себя и ту информацию.
        $this->violations[] = new ErrorValidationDetailsItem($field,$message);
    }

    /**
     * @return ErrorValidationDetailsItem[]
     */
    public function getViolations(): array
    {
        return $this->violations;
    }


}