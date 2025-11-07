<?php

declare(strict_types=1);

use App\Constants\OneTimePasswordScope;
use Phenix\Database\Migration;

class UserOneTimePasswords extends Migration
{
    public function up(): void
    {
        $table = $this->table('user_one_time_passwords');
        $table->enum('scope', OneTimePasswordScope::toArray());
        $table->string('code', 255);
        $table->unsignedInteger('user_id');
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