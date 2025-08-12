<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CompanySector extends Pivot
{
    protected $table = 'company_sector';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = ['company_id', 'sector_id'];
}
