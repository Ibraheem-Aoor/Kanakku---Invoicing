<?php

namespace Kanakku\Http\Controllers\V1\Update;

use Kanakku\Models\Setting;
use Illuminate\Http\Request;
use Kanakku\Space\Updater;
use Kanakku\Space\SiteApi;
use Illuminate\Support\Facades\Artisan;
use Kanakku\Http\Controllers\Controller;

class UpdateController extends Controller
{
    public function download(Request $request)
    {
        $request->validate([
            'version' => 'required',
        ]);

        $path = Updater::download($request->version);

        return response()->json([
            'success' => true,
            'path' => $path
        ]);
    }

    public function unzip(Request $request)
    {
        $request->validate([
            'path' => 'required',
        ]);

        try {
            $path = Updater::unzip($request->path);

            return response()->json([
                'success' => true,
                'path' => $path
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function copyFiles(Request $request)
    {
        $request->validate([
            'path' => 'required',
        ]);

        $path = Updater::copyFiles($request->path);

        return response()->json([
            'success' => true,
            'path' => $path
        ]);
    }

    public function migrate(Request $request)
    {
        Updater::migrateUpdate();

        return response()->json([
            'success' => true
        ]);
    }

    public function finishUpdate(Request $request)
    {
        $request->validate([
            'installed' => 'required',
            'version' => 'required',
        ]);

        $json = Updater::finishUpdate($request->installed, $request->version);

        return response()->json($json);
    }

    public function checkLatestVersion(Request $request)
    {
        set_time_limit(600); // 10 minutes

        $json = Updater::checkForUpdate(Setting::getSetting('version'));

        return response()->json($json);
    }
}
