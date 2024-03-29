<?php

namespace Kanakku\Http\Controllers\V1\Mobile\Customer;

use Barryvdh\DomPDF\PDF;
use Kanakku\Models\Company;
use Kanakku\Models\CompanySetting;
use Kanakku\Http\Controllers\Controller;
use Kanakku\Models\Invoice;
use Kanakku\Models\InvoiceTemplate;
use Kanakku\Mail\InvoiceViewedMail;
use Kanakku\Models\User;

class InvoicePdfController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Invoice $invoice)
    {
        if ($invoice && ($invoice->status == Invoice::STATUS_SENT || $invoice->status == Invoice::STATUS_DRAFT)) {
            $invoice->status = Invoice::STATUS_VIEWED;
            $invoice->viewed = true;
            $invoice->save();
            $notifyInvoiceViewed = CompanySetting::getSetting(
                'notify_invoice_viewed',
                $invoice->company_id
            );

            if ($notifyInvoiceViewed == 'YES') {
                $data['invoice'] = Invoice::findOrFail($invoice->id)->toArray();
                $data['user'] = User::find($invoice->user_id)->toArray();
                $notificationEmail = CompanySetting::getSetting(
                    'notification_email',
                    $invoice->company_id
                );

                \Mail::to($notificationEmail)->send(new InvoiceViewedMail($data));
            }
        }

        return $invoice->getGeneratedPDFOrStream('invoice');
    }
}
