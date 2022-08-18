<?php

namespace Kanakku\Http\Controllers\V1\Report;

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Kanakku\Models\Company;
use PDF;
use Carbon\Carbon;
use Kanakku\Models\User;
use Kanakku\Models\CompanySetting;
use Kanakku\Http\Controllers\Controller;
use Illuminate\Routing\UrlGenerator;

class CustomerSalesReportController extends Controller
{
    /**
    * Handle the incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  string  $hash
    * @return \Illuminate\Http\JsonResponse
    */
    public function __invoke(Request $request, $hash)
    {
        $company = Company::where('unique_hash', $hash)->first();

        $locale = CompanySetting::getSetting('language',  $company->id);

        App::setLocale($locale);

        $start = Carbon::createFromFormat('Y-m-d', $request->from_date);
        $end = Carbon::createFromFormat('Y-m-d', $request->to_date);

        $customers = User::with(['invoices' => function ($query) use ($start, $end) {
                $query->whereBetween(
                    'invoice_date',
                    [$start->format('Y-m-d'), $end->format('Y-m-d')]
                );
            }])
            ->customer()
            ->whereCompany($company->id)
            ->applyInvoiceFilters($request->only(['from_date', 'to_date']))
            ->get();

        $totalAmount = 0;
        foreach ($customers as $customer) {
            $customerTotalAmount = 0;
            foreach ($customer->invoices as $invoice) {
                $customerTotalAmount += $invoice->total;
            }
            $customer->totalAmount = $customerTotalAmount;
            $totalAmount += $customerTotalAmount;
        }

        $dateFormat = CompanySetting::getSetting('carbon_date_format', $company->id);
        $from_date = Carbon::createFromFormat('Y-m-d', $request->from_date)->format($dateFormat);
        $to_date = Carbon::createFromFormat('Y-m-d', $request->to_date)->format($dateFormat);

        $colors = [
            'primary_text_color',
            'heading_text_color',
            'section_heading_text_color',
            'border_color',
            'body_text_color',
            'footer_text_color',
            'footer_total_color',
            'footer_bg_color',
            'date_text_color'
        ];

        $colorSettings = CompanySetting::whereIn('option', $colors)
            ->whereCompany($company->id)
            ->get();

        view()->share([
            'customers' => $customers,
            'totalAmount' => $totalAmount,
            'colorSettings' => $colorSettings,
            'company' => $company,
            'from_date' => $from_date,
            'to_date' => $to_date
        ]);

        $pdf = PDF::loadView('app.pdf.reports.sales-customers');

        if ($request->has('download')) {
            return $pdf->download();
        }

        return $pdf->stream();
    }


    public function reports(Request $request, $hash)
    {
        $company = Company::where('unique_hash', $hash)->first();

        $locale = CompanySetting::getSetting('language',  $company->id);

        App::setLocale($locale);

        $start = Carbon::createFromFormat('Y-m-d', $request->from_date);
        $end = Carbon::createFromFormat('Y-m-d', $request->to_date);

        $customers = User::with(['invoices' => function ($query) use ($start, $end) {
                $query->whereBetween(
                    'invoice_date',
                    [$start->format('Y-m-d'), $end->format('Y-m-d')]
                );
            }])
            ->customer()
            ->whereCompany($company->id)
            ->applyInvoiceFilters($request->only(['from_date', 'to_date']))
            ->get();

        $totalAmount = 0;
        foreach ($customers as $customer) {
            $customerTotalAmount = 0;
            foreach ($customer->invoices as $invoice) {
                $customerTotalAmount += $invoice->total;
            }
            $customer->totalAmount = $customerTotalAmount;
            $totalAmount += $customerTotalAmount;
        }

        $dateFormat = CompanySetting::getSetting('carbon_date_format', $company->id);
        $from_date = Carbon::createFromFormat('Y-m-d', $request->from_date)->format($dateFormat);
        $to_date = Carbon::createFromFormat('Y-m-d', $request->to_date)->format($dateFormat);



        $data=array();
         foreach ($customers as $customer) {
                $row=array();
                $invoiceDetails=array();
                 foreach ($customer->invoices as $invoice){
                    $invoiceRow=array();
                    $invoiceRow['invoiceDate']=$invoice->formattedInvoiceDate;
                    $invoiceRow['invoiceNo']=$invoice->invoice_number;
                    $invoiceRow['invoiceAmount']=format_money($invoice->total);
                    $invoiceDetails[]=$invoiceRow;
                 }
                $row['customerName']=$customer->name;
                $row['invoiceDetails']=$invoiceDetails;
                $row['customerAmount']=format_money($customer->totalAmount);

                $data[]=$row;
            } 

     
            return response()->json([
            'invoiceData'=>$data,
            'invoiceTotalAmount'=>format_money($totalAmount),
            'companyName'=>$company->name,
            'fromDate'=>$from_date,
            'toDate' => $to_date,
            ]); 

        
    }
}
