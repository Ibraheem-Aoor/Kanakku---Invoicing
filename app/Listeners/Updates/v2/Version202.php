<?php

namespace Kanakku\Listeners\Updates\v2;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Kanakku\Events\UpdateFinished;
use Kanakku\Listeners\Updates\Listener;
use Kanakku\Models\Setting;

class Version202 extends Listener
{
    const VERSION = '2.0.2';

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(UpdateFinished $event)
    {
        if ($this->isListenerFired($event)) {
            return;
        }

        // Update Kanakku app version
        Setting::setSetting('version', static::VERSION);
    }
}
