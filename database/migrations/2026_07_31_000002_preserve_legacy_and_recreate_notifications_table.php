<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('notifications') && !$this->isLaravelNotificationsTable()) {
            if (Schema::hasTable('legacy_notifications')) {
                throw new \RuntimeException(
                    'Cannot preserve the legacy notifications table because legacy_notifications already exists.'
                );
            }

            Schema::rename('notifications', 'legacy_notifications');
        }

        if (!Schema::hasTable('notifications')) {
            $this->createLaravelNotificationsTable();
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('notifications') && $this->isLaravelNotificationsTable()) {
            Schema::drop('notifications');
        }

        if (!Schema::hasTable('notifications') && Schema::hasTable('legacy_notifications')) {
            Schema::rename('legacy_notifications', 'notifications');
        }
    }

    private function isLaravelNotificationsTable(): bool
    {
        $requiredColumns = [
            'id',
            'type',
            'notifiable_type',
            'notifiable_id',
            'data',
            'read_at',
            'created_at',
            'updated_at',
        ];

        foreach ($requiredColumns as $column) {
            if (!Schema::hasColumn('notifications', $column)) {
                return false;
            }
        }

        return true;
    }

    private function createLaravelNotificationsTable(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['notifiable_type', 'notifiable_id', 'created_at']);
        });
    }
};
