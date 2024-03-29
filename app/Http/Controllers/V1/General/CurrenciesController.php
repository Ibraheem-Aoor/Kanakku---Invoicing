<?php

namespace Kanakku\Http\Controllers\V1\General;

use Kanakku\Models\Currency;
use Kanakku\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CurrenciesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $currencies = Currency::latest()->get();

        return response()->json([
            'currencies' => $currencies
        ]);
    }
}
