<?php

namespace Kanakku\Http\Controllers\V1\General;

use Kanakku\Models\Country;
use Kanakku\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CountriesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        return response()->json([
            'countries' => Country::all()
        ]);
    }
}