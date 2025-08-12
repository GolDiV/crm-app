<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Company;
use App\Models\Phone;
use App\Models\Email;
use App\Models\Pivots\CompanySector as CompanySectorPivot;
use App\Observers\CompanyObserver;
use App\Observers\PhoneObserver;
use App\Observers\EmailObserver;
use App\Observers\CompanySectorObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Company::observe(CompanyObserver::class);
        Phone::observe(PhoneObserver::class);
        Email::observe(EmailObserver::class);
        CompanySectorPivot::observe(CompanySectorObserver::class);
    }
}
