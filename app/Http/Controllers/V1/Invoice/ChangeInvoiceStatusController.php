<?php

namespace Kanakku\Http\Controllers\V1\Invoice;

use Illuminate\Http\Request;
use Kanakku\Http\Controllers\Controller;
use Kanakku\Models\Invoice;

class ChangeInvoiceStatusController extends Controller
{
    /**
    * Handle the incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\JsonResponse
    */
   public function __invoke(Request $request, Invoice $invoice)
   {
        if ($request->status == Invoice::STATUS_SENT) {
            $invoice->status = Invoice::STATUS_SENT;
            $invoice->sent = true;
            $invoice->save();
        } elseif ($request->status == Invoice::STATUS_COMPLETED) {
            $invoice->status = Invoice::STATUS_COMPLETED;
            $invoice->paid_status = Invoice::STATUS_PAID;
            $invoice->due_amount = 0;
            $invoice->save();
        }

        return response()->json([
            'success' => true
        ]);
    }
}
