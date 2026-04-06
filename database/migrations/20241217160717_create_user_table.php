<?php

declare(strict_types=1);

use Phenix\Database\Migration;

class CreateUserTable extends Migration
{
    public function up(): void
    {
        $table = $this->table('users');
        $table->string('name', 100);
        $table->string('email', 124)->unique();
        $table->string('password', 255);
        $table->dateTime('email_verified_at')->nullable();
        $table->timestamps();
        $table->create();
    }

    public function down(): void
    {
        $this->table('users')->drop()->save();
    }
}
