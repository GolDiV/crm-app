<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('company_index', function (Blueprint $t) {
            $t->unsignedBigInteger('id')->primary();
            $t->string('short_name')->nullable();
            $t->string('city')->nullable();
            $t->string('region_name')->nullable()->index();
            $t->string('manager_name')->nullable()->index();
            $t->text('sectors_csv')->nullable();
            $t->text('emails_lc')->nullable();
            $t->text('phones_csv')->nullable();
            $t->text('phones_digits')->nullable();
            $t->timestamp('updated_at')->nullable();

            $t->index('short_name');
            $t->index('city');
        });

        // префиксные индексы для TEXT-полей
        DB::statement('CREATE INDEX idx_ci_emails_lc ON company_index (emails_lc(191))');
        DB::statement('CREATE INDEX idx_ci_phones_digits ON company_index (phones_digits(20))');
    }


    public function down(): void
    {
        // снять префиксный индекс вручную
        DB::statement('DROP INDEX idx_ci_emails_lc ON company_index');
        DB::statement('DROP INDEX idx_ci_phones_digits ON company_index');
        Schema::dropIfExists('company_index');
    }
};
