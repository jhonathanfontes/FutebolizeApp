<?php

namespace App\Exceptions;

use CodeIgniter\HTTP\Exceptions\HTTPException;

class ForbiddenException extends HTTPException
{
    public static function forForbidden(string $message = 'Acesso proibido')
    {
        return new static($message, 403);
    }
}
