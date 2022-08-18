<?php

namespace Kanakku\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Kanakku\Events\UpdateFinished;
use Kanakku\Listeners\Updates\v1\Version110;
use Kanakku\Listeners\Updates\v2\Version200;
use Kanakku\Listeners\Updates\v2\Version201;
use Kanakku\Listeners\Updates\v2\Version202;
use Kanakku\Listeners\Updates\v2\Version210;
use Kanakku\Listeners\Updates\v3\Version300;
use Kanakku\Listeners\Updates\v3\Version310;
use Kanakku\Listeners\Updates\v3\Version311;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UpdateFinished::class => [
            Version110::class,
            Version200::class,
            Version201::class,
            Version202::class,
            Version210::class,
            Version300::class,
            Version310::class,
            Version311::class,
        ],
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
