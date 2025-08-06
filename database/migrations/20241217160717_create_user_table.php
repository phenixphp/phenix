<?php

declare(strict_types=1);

use Phenix\Database\Migration;

class CreateUserTable extends Migration
{
    public function up(): void
    {
        $table = $this->table('users');
        $table->addColumn('name', 'string', ['limit' => 100]);
        $table->addColumn('email', 'string', ['limit' => 100]);
        $table->addColumn('password', 'string', ['limit' => 255]);
        $table->addColumn('created_at', 'datetime', ['null' => true]);
        $table->addColumn('updated_at', 'datetime', ['null' => true]);
        $table->create();
    }

    public function down(): void
    {
        $this->table('users')->drop()->save();
    }
}
