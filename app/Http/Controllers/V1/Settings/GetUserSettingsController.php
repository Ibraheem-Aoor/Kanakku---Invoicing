<?php

namespace Kanakku\Http\Controllers\V1\Settings;

use Auth;
use Kanakku\Http\Controllers\Controller;
use Kanakku\Http\Requests\GetSettingsRequest;

class GetUserSettingsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\GetSettingsRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(GetSettingsRequest $request)
    {
        $user = Auth::user();

        return response()->json($user->getSettings($request->settings));
    }
}
