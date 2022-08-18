<?php

namespace Kanakku\Http\Controllers\V1\General;

use Kanakku\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LanguagesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return response()->json([
            'languages' => config('kanakku.languages')
        ]);
    }
}
