<?php

namespace App\Observers;

use App\Jobs\RebuildCompanyIndex;
use App\Models\Phone;

class PhoneObserver
{
    public function created(Phone $m): void
    {
        RebuildCompanyIndex::dispatch($m->company_id);
    }
    public function updated(Phone $m): void
    {
        RebuildCompanyIndex::dispatch($m->company_id);
    }
    public function deleted(Phone $m): void
    {
        RebuildCompanyIndex::dispatch($m->company_id);
    }
}
