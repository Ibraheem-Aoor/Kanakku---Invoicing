<?php

namespace Kanakku\Http\Controllers\V1\Settings;

use Kanakku\Models\CompanySetting;
use Kanakku\Http\Controllers\Controller;
use Kanakku\Http\Requests\GetSettingsRequest;

class GetCompanySettingsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(GetSettingsRequest $request)
    {
        $settings = CompanySetting::getSettings($request->settings, $request->header('company'));

        return response()->json($settings);
    }
}
