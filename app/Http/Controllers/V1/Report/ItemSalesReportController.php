<?php

namespace Kanakku\Http\Controllers\V1\Report;

use Illuminate\Http\Request;
use Kanakku\Models\Company;
use PDF;
use Carbon\Carbon;
use Kanakku\Models\InvoiceItem;
use Kanakku\Models\CompanySetting;
use Kanakku\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;

class ItemSalesReportController extends Controller
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

        $items = InvoiceItem::whereCompany($company->id)
            ->applyInvoiceFilters($request->only(['from_date', 'to_date']))
            ->itemAttributes()
            ->get();

        $totalAmount = 0;
        foreach ($items as $item) {
            $totalAmount += $item->total_amount;
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
            'items' => $items,
            'colorSettings' => $colorSettings,
            'totalAmount' => $totalAmount,
            'company' => $company,
            'from_date' => $from_date,
            'to_date' => $to_date
        ]);
        $pdf = PDF::loadView('app.pdf.reports.sales-items');

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

        $items = InvoiceItem::whereCompany($company->id)
            ->applyInvoiceFilters($request->only(['from_date', 'to_date']))
            ->itemAttributes()
            ->get();

        $totalAmount = 0;
        foreach ($items as $item) {
            $totalAmount += $item->total_amount;
        }

        $dateFormat = CompanySetting::getSetting('carbon_date_format', $company->id);
        $from_date = Carbon::createFromFormat('Y-m-d', $request->from_date)->format($dateFormat);
        $to_date = Carbon::createFromFormat('Y-m-d', $request->to_date)->format($dateFormat);

        $data=array();
         foreach ($items as $item) {
                $row=array();
                $row['itemName']=$item->name;
                $row['itemAmount']=format_money($item->total_amount);
                $data[]=$row;
            } 

     
            return response()->json([
            'itemData'=>$data,
            'itemTotalAmount'=>format_money($totalAmount),
            'companyName'=>$company->name,
            'fromDate'=>$from_date,
            'toDate' => $to_date,
            ]);

        
        
    }
}
