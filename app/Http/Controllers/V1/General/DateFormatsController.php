<?php

namespace Kanakku\Http\Controllers\V1\General;

use Kanakku\Http\Controllers\Controller;
use Kanakku\Space\DateFormatter;
use Illuminate\Http\Request;

class DateFormatsController extends Controller
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
            'date_formats' => DateFormatter::get_list()
        ]);
    }
}
