<?php

namespace App\Jobs;

use App\Support\CompanyIndexSql;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class RebuildCompanyIndex implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $companyId) {}

    public function handle(): void
    {
        $select = CompanyIndexSql::selectOne($this->companyId);

        DB::statement("
            REPLACE INTO company_index
            (id, short_name, city, region_name, manager_name,
             sectors_csv, emails_lc, phones_csv, phones_digits, updated_at)
            {$select}
        ");
    }
}
