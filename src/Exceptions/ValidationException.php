<?php

namespace App\Exceptions;

use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;
use RuntimeException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends RuntimeException
{

    public function __construct(private ConstraintViolationListInterface $violation)
    {

        parent::__construct('validation failed');

    }

    public function getViolation(): ConstraintViolationListInterface
    {

        return $this->violation;

    }

}