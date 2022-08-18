<?php

namespace Kanakku\Http\Controllers\V1\Payment;

use Kanakku\Http\Controllers\Controller;
use Kanakku\Models\Payment;

class PaymentPdfController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Payment $payment)
    {
        return $payment->getGeneratedPDFOrStream('payment');
    }
}
