<?php

namespace Kanakku\Http\Controllers\V1\Invoice;

use Kanakku\Http\Controllers\Controller;
use Kanakku\Models\Invoice;
use Kanakku\Http\Requests\SendInvoiceRequest;

class SendInvoiceController extends Controller
{
    /**
     * Mail a specific invoice to the corresponding customer's email address.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(SendInvoiceRequest $request, Invoice $invoice)
    {
        $invoice->send($request->all());

        return response()->json([
            'success' => true
        ]);
    }
}
