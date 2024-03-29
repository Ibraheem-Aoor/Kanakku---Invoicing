<?php

namespace Kanakku\Http\Controllers\V1\Estimate;

use Illuminate\Http\Request;
use Kanakku\Http\Controllers\Controller;
use Kanakku\Models\Estimate;

class ChangeEstimateStatusController extends Controller
{
    /**
    * Handle the incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  Estimate $estimate
    * @return \Illuminate\Http\Response
    */
   public function __invoke(Request $request, Estimate $estimate)
   {
        $estimate->update($request->only('status'));

        return response()->json([
            'success' => true
        ]);
    }
}
