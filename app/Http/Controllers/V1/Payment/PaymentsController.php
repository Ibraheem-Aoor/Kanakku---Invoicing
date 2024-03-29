<?php

namespace Kanakku\Http\Controllers\V1\Payment;

use Illuminate\Http\Request;
use Kanakku\Models\Payment;
use Kanakku\Http\Requests\DeletePaymentsRequest;
use Kanakku\Http\Requests\PaymentRequest;
use Kanakku\Http\Controllers\Controller;

class PaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : 10;

        $payments = Payment::with(['user', 'invoice', 'paymentMethod', 'creator'])
            ->join('users', 'users.id', '=', 'payments.user_id')
            ->leftJoin('invoices', 'invoices.id', '=', 'payments.invoice_id')
            ->leftJoin('payment_methods', 'payment_methods.id', '=', 'payments.payment_method_id')
            ->applyFilters($request->only([
                'search',
                'payment_number',
                'payment_id',
                'payment_method_id',
                'customer_id',
                'orderByField',
                'orderBy'
            ]))
            ->whereCompany($request->header('company'))
            ->select('payments.*', 'users.name', 'invoices.invoice_number', 'payment_methods.name as payment_mode')
            ->latest()
            ->paginateData($limit);

        return response()->json([
            'payments' => $payments,
            'paymentTotalCount' => Payment::count()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PaymentRequest $request)
    {
        $payment = Payment::createPayment($request);

        return response()->json([
            'payment' => $payment,
            'success' => true
        ]);
    }

    public function show(Request $request, Payment $payment)
    {
        $payment->load([
            'user',
            'invoice',
            'paymentMethod',
            'fields.customField'
        ]);

        return response()->json([
            'nextPaymentNumber' => $payment->getPaymentNumAttribute(),
            'payment_prefix' => $payment->getPaymentPrefixAttribute(),
            'payment' => $payment,
        ]);
    }

    public function update(PaymentRequest $request, Payment $payment)
    {
        $payment = $payment->updatePayment($request);

        return response()->json([
            'payment' => $payment,
            'success' => true
        ]);
    }

    public function delete(DeletePaymentsRequest $request)
    {
        Payment::deletePayments($request->ids);

        return response()->json([
            'success' => true
        ]);
    }
}
