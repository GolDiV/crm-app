<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyIndex extends Model
{
    protected $table = 'company_index';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'short_name',
        'city',
        'region_name',
        'manager_name',
        'sectors_csv',
        'emails_lc',
        'phones_csv',
        'phones_digits',
        'updated_at',
    ];
}
