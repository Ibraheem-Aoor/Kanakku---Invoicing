<?php

namespace Kanakku\Listeners\Updates\v3;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Kanakku\Listeners\Updates\Listener;
use Illuminate\Database\Schema\Blueprint;
use Kanakku\Events\UpdateFinished;
use Kanakku\Models\Setting;
use Kanakku\Models\Currency;
use Schema;
use Artisan;

class Version310 extends Listener
{
    const VERSION = '3.1.0';


    /**
     * Handle the event.
     *
     * @param UpdateFinished $event
     * @return void
     */
    public function handle(UpdateFinished $event)
    {
        if ($this->isListenerFired($event)) {
            return;
        }

        Currency::firstOrCreate(
            [
                'name' => 'Kyrgyzstani som',
                'code' => 'KGS'
            ],
            [
                'name' => 'Kyrgyzstani som',
                'code' => 'KGS',
                'symbol' => 'С̲ ',
                'precision' => '2',
                'thousand_separator' => '.',
                'decimal_separator' => ','
            ]
        );

        Artisan::call('migrate', ['--force' => true]);

        // Update Kanakku app version
        Setting::setSetting('version', static::VERSION);
    }
}
