<?php

namespace App\Observers;

use App\Jobs\RebuildCompanyIndex;
use App\Models\Pivots\CompanySector;

class CompanySectorObserver
{
    public function created(CompanySector $p): void
    {
        RebuildCompanyIndex::dispatch($p->company_id);
    }
    public function deleted(CompanySector $p): void
    {
        RebuildCompanyIndex::dispatch($p->company_id);
    }
}
