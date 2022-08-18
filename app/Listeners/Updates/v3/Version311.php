<?php

namespace Kanakku\Listeners\Updates\v3;

use Kanakku\Listeners\Updates\Listener;
use Kanakku\Events\UpdateFinished;
use Kanakku\Models\Setting;
use Kanakku\Models\Currency;
use Artisan;

class Version311 extends Listener
{
    const VERSION = '3.1.1';

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

        Artisan::call('migrate', ['--force' => true]);

        // Update Kanakku app version
        Setting::setSetting('version', static::VERSION);
    }
}
