<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Удаляем стандартные поля
            $table->dropColumn(['name', 'email']);

            // Добавляем нужные поля
            $table->string('login')->unique()->after('id');
            $table->string('email')->nullable()->after('login');
            $table->string('name')->after('email');
            $table->string('internal_phone')->nullable()->after('name');
            $table->string('external_phone')->nullable()->after('internal_phone');
            $table->date('birth_date')->nullable()->after('external_phone');
            $table->string('horoscope')->nullable()->after('birth_date');

            // Добавляем связь с roles
            $table->foreignId('role_id')->after('horoscope')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Удаляем добавленные поля
            $table->dropForeign(['role_id']);
            $table->dropColumn([
                'login', 'email', 'name',
                'internal_phone', 'external_phone',
                'birth_date', 'horoscope', 'role_id'
            ]);

            // Восстанавливаем базовые поля Laravel
            $table->string('name')->nullable();
            $table->string('email')->nullable()->unique();
        });
    }
};
