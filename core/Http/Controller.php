<?php

declare(strict_types=1);

namespace Core\Http;

use Core\Concerns\HasRequest;

abstract class Controller
{
    use HasRequest;
}
