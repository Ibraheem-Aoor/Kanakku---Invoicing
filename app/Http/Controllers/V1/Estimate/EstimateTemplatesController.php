<?php

namespace Kanakku\Http\Controllers\V1\Estimate;

use Kanakku\Models\EstimateTemplate;
use Kanakku\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EstimateTemplatesController extends Controller
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
            'templates' => EstimateTemplate::all()
        ]);
    }
}
