<?php

namespace Kanakku\Http\Controllers\V1\Update;

use Kanakku\Http\Controllers\Controller;
use Kanakku\Models\Setting;
use Kanakku\Space\Updater;
use Illuminate\Http\Request;

class CheckVersionController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        set_time_limit(600); // 10 minutes

        $json = Updater::checkForUpdate(Setting::getSetting('version'));

        return response()->json($json);
    }
}
