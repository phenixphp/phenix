<?php

declare(strict_types=1);

use Phenix\Database\Migration;

class CreatePersonalAccessTokensTable extends Migration
{
    public function up(): void
    {
        $table = $this->table('personal_access_tokens', ['id' => false, 'primary_key' => 'id']);

        $table->uuid('id');
        $table->string('tokenable_type', 100);
        $table->unsignedInteger('tokenable_id');
        $table->string('name', 100);
        $table->string('token', 255)->unique();
        $table->text('abilities')->nullable();
        $table->dateTime('last_used_at')->nullable();
        $table->dateTime('expires_at');
        $table->timestamps();
        $table->addIndex(['tokenable_type', 'tokenable_id'], ['name' => 'idx_tokenable']);
        $table->addIndex(['expires_at'], ['name' => 'idx_expires_at']);
        $table->create();
    }

    public function down(): void
    {
        $this->table('personal_access_tokens')->drop();
    }
}
