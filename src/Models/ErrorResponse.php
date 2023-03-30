<?php

namespace App\Models;

<<<<<<< HEAD
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use App\Models\ErrorDebugDetails;
use App\Models\ErrorValidationDetails;


=======
>>>>>>> parent of 973838e (Сделали полноценный цикл для POST метода и добавили к нему слагер)
class ErrorResponse
{
    // details = null, на тот случай если мы захотим из сервиса или из контроллера возвращать ерор респонс, чтобы нам не
    // нужно было всегда указывать детали.
    public function __construct(private string $message, private mixed $details = null)
    {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

<<<<<<< HEAD
    // Уточняем что в OA передает типа объект
    // Если у нас параметр указан просто как object наш слаггер делает этот параметр пустым, поскольку мы знаем что в эти детали
    // может прийти нам необходимо добавить немного параметров, в эту аннотацию и добавим мы параметр oneOf
    // в oneOf мы можем передать одну или несколько схем и добавляем далее ref (референс, ссылку)
    // ErrorDebugDetails исполняется в ApiExceptionListener.php, а ErrorValidationDetails исполняется в ValidationExceptionListener
    //
    /**
     * @OA\Property(type="object",oneOf={
     *     @OA\Schema(ref=@Model(type=ErrorDebugDetails::class)),
     *     @OA\Schema(ref=@Model(type=ErrorValidationDetails::class)),
     * })
     */
=======

>>>>>>> parent of 973838e (Сделали полноценный цикл для POST метода и добавили к нему слагер)
    public function getDetails(): mixed
    {
        return $this->details;
    }


}