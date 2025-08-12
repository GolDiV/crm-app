<?php

namespace App\Observers;

use App\Jobs\RebuildCompanyIndex;
use App\Models\Email;

class EmailObserver
{
    public function created(Email $m): void
    {
        RebuildCompanyIndex::dispatch($m->company_id);
    }
    public function updated(Email $m): void
    {
        RebuildCompanyIndex::dispatch($m->company_id);
    }
    public function deleted(Email $m): void
    {
        RebuildCompanyIndex::dispatch($m->company_id);
    }
}
