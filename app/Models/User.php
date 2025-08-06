<?php

declare(strict_types=1);

namespace App\Models;

use App\Collections\UserCollection;
use App\Queries\UserQuery;
use Phenix\Database\Models\Attributes\Column;
use Phenix\Database\Models\Attributes\Id;
use Phenix\Database\Models\DatabaseModel;

class User extends DatabaseModel
{
    #[Id]
    public int $id;

    #[Column]
    public string $name;

    #[Column]
    public string $email;

    #[Column]
    public string $password;

    public static function table(): string
    {
        return 'users';
    }

    protected static function newQueryBuilder(): UserQuery
    {
        return new UserQuery();
    }

    public function newCollection(): UserCollection
    {
        return new UserCollection();
    }
}
