<?php

namespace App\Observers;

use App\Jobs\RebuildCompanyIndex;
use App\Models\Company;

class CompanyObserver
{
    public function created(Company $m): void
    {
        RebuildCompanyIndex::dispatch($m->id);
    }
    public function updated(Company $m): void
    {
        RebuildCompanyIndex::dispatch($m->id);
    }
    public function deleted(Company $m): void
    {
        RebuildCompanyIndex::dispatch($m->id);
    }
}
