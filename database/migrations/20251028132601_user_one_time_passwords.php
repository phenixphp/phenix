<?php

declare(strict_types=1);

use App\Constants\OneTimePasswordScope;
use Phenix\Database\Constants\ColumnAction;
use Phenix\Database\Migration;

class UserOneTimePasswords extends Migration
{
    public function up(): void
    {
        $table = $this->table('user_one_time_passwords', ['id' => false, 'primary_key' => 'id']);
        $table->uuid('id');
        $table->enum('scope', OneTimePasswordScope::toArray());
        $table->string('code', 255);
        $table->unsignedInteger('user_id');
        $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->onDelete(ColumnAction::CASCADE);
        $table->datetime('expires_at');
        $table->datetime('used_at')->nullable();
        $table->timestamps();
        $table->create();
    }

    public function down(): void
    {
        $this->table('user_one_time_passwords')->drop();
    }
}