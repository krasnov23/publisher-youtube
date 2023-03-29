<?php

namespace App\Service\ExceptionHandler;

// Класс, который при выбросе исключения будет искать исключение в services.yaml
use InvalidArgumentException;


class ExceptionMappingResolver
{
    /**
     * @var ExceptionMapping[]
     */
    // Устанавливаем пустой массив так как для второго теста он не может принимать простой массив, по причине того что
    // У нас указана типизация которая сверху (@var ExceptionMapping[])
    private array $mappings = [];

    // В данном случае фактически метод констракт просто перемапливает наши исключения из сервис.yaml проверяя указан ли в них код
    public function __construct(array $mappings)
    {
        // Поскольку массивы в php не типизированы, мы не можем указать формат массива который мы ожидаем, нам ничего не мешает передать
        // в него что угодно н. То есть предостерегаем себя от передачи в массивы не валидных данных
        // (например: ключ ololo вместо code в exceptions в services.yaml )
        // Мы никогда не пишем логику в конструкторе, но данный случай исключение.
        foreach ($mappings as $class => $mapping)
        {
            if (empty($mapping['code'])){
                // Проверяет есть ли код ответа, если нет то не продолжаем работу.
                throw new InvalidArgumentException('code is mandatory for class' . $class );
            }

            // атрибуты hiden и логабл значения указанные после знаков вопроса, это значение по умолчанию
            $this->addMapping($class,$mapping['code'],$mapping['hidden'] ?? true,$mapping['loggable'] ?? false);
        }

    }

    // Метод который возвращает нам наш класс с ошибкой
    public function resolve(string $throwableClass): ?ExceptionMapping
    {
        $foundMapping = null;

        // Проходит по массиву и сравнивает его с нашим названием класса исключения
        foreach ($this->mappings as $class => $mapping)
        {
            // метод is_subclass_of, отвечает за то чтобы например нижняя строчка services.yaml, parameters/exceptions
            // если она будет Exception: {code : 502}, то если исключение не подходит ни под одно из указанных исключений
            // исключение принимало этот самый код 502
            if ($throwableClass === $class || is_subclass_of($throwableClass,$class))
            {
                $foundMapping = $mapping;
            }
        }

        return $foundMapping;
    }

    // Этот метод создан для проверки типов, так как массив у нас может содержать что угодно и мы не можем проверить типы.
    // но создав метод метод уже проверяет их.
    private function addMapping(string $class, int $code, bool $hidden, bool $loggable ): void
    {
        // Класс в данном случае номер конкретного элемента массива который пересоздается в класс ExceptionMapping
        $this->mappings[$class] = new ExceptionMapping($code, $hidden, $loggable);

    }


}