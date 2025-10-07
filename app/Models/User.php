<?php

declare(strict_types=1);

namespace App\Models;

use App\Queries\UserQuery;
use Phenix\Database\Models\Attributes\Column;
use Phenix\Database\Models\Attributes\DateTime;
use Phenix\Database\Models\Attributes\Hidden;
use Phenix\Database\Models\Attributes\Id;
use Phenix\Database\Models\DatabaseModel;
use Phenix\Util\Date;

class User extends DatabaseModel
{
    #[Id]
    public int $id;

    #[Column]
    public string $name;

    #[Column]
    public string $email;

    #[Hidden]
    public string $password;

    #[DateTime(name: 'created_at', autoInit: true)]
    public Date $createdAt;

    #[DateTime(name: 'updated_at')]
    public Date|null $updatedAt = null;

    public static function table(): string
    {
        return 'users';
    }

    protected static function newQueryBuilder(): UserQuery
    {
        return new UserQuery();
    }
}
