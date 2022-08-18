<?php

namespace Kanakku\Http\Controllers\V1\Estimate;

use Kanakku\Http\Controllers\Controller;
use Kanakku\Models\Estimate;
use Kanakku\Http\Requests\SendEstimatesRequest;

class SendEstimateController extends Controller
{
    /**
    * Handle the incoming request.
    *
    * @param  \Kanakku\Http\Requests\SendEstimatesRequest  $request
    * @return \Illuminate\Http\JsonResponse
    */
    public function __invoke(SendEstimatesRequest $request, Estimate $estimate)
    {
        $response = $estimate->send($request->all());

        return response()->json($response);
    }
}
