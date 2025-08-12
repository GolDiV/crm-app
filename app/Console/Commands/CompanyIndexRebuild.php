<?php

namespace App\Console\Commands;

use App\Support\CompanyIndexSql;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CompanyIndexRebuild extends Command
{
    protected $signature = 'company-index:rebuild {--truncate}';
    protected $description = 'Rebuild materialized table company_index for all companies';

    public function handle(): int
    {
        if ($this->option('truncate')) {
            DB::table('company_index')->truncate();
            $this->info('Truncated company_index');
        }

        $select = CompanyIndexSql::selectAll();

        DB::statement("
            REPLACE INTO company_index
            (id, short_name, city, region_name, manager_name,
             sectors_csv, emails_lc, phones_csv, phones_digits, updated_at)
            {$select}
        ");

        $count = DB::table('company_index')->count();
        $this->info("Rebuilt company_index. Rows: {$count}");

        return self::SUCCESS;
    }
}
