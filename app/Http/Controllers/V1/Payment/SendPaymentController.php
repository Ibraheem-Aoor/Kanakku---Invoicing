<?php

namespace Kanakku\Http\Controllers\V1\Payment;

use Kanakku\Http\Controllers\Controller;
use Kanakku\Models\Payment;
use Kanakku\Http\Requests\SendPaymentRequest;

class SendPaymentController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(SendPaymentRequest $request, Payment $payment)
    {
        $response = $payment->send($request->all());

        return response()->json($response);
    }
}
