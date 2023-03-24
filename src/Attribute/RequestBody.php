<?php

namespace App\Attribute;

use Attribute;

// Нам необходимо чтобы он применялся только к параметрам метода поэтому мы указываем константу TARGET_PARAMETER
#[Attribute(Attribute::TARGET_PARAMETER)]
class RequestBody
{
}
