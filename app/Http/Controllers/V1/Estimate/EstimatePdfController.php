<?php

namespace Kanakku\Http\Controllers\V1\Estimate;

use Kanakku\Models\Estimate; 
use Kanakku\Http\Controllers\Controller;

class EstimatePdfController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Estimate $estimate)
    {
        return $estimate->getGeneratedPDFOrStream('estimate');
    }
}
