<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
    $table->id();
    $table->string('short_name');
    $table->text('full_name');
    $table->foreignId('region_id')
            ->nullable()
            ->constrained('regions')
            ->nullOnDelete();
    $table->string('type');
    $table->string('city');
    $table->text('address')->nullable();
    $table->string('inn')->nullable();
    $table->string('kpp')->nullable();
    $table->foreignId('user_id')
          ->nullable()
          ->constrained('users')
          ->nullOnDelete();
    $table->text('note')->nullable();
    $table->string('website')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
