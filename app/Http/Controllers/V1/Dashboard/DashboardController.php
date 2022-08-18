<?php
namespace Kanakku\Http\Controllers\V1\Dashboard;

use Illuminate\Http\Request;
use Kanakku\Models\Estimate;
use Kanakku\Models\Invoice;
use Kanakku\Models\CompanySetting;
use Kanakku\Models\Expense;
use Kanakku\Models\Payment;
use Carbon\Carbon;
use Kanakku\Models\User;
use Kanakku\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\UrlGenerator;

class DashboardController extends Controller
{

    protected $url;

    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */


    public function __invoke(Request $request)
    {
        $invoiceTotals = [];
        $expenseTotals = [];
        $receiptTotals = [];
        $netProfits = [];
        $i = 0;
        $months = [];
        $monthCounter = 0;
        $fiscalYear = CompanySetting::getSetting('fiscal_year', $request->header('company'));
        $startDate = Carbon::now();
        $start = Carbon::now();
        $end = Carbon::now();
        $week = Carbon::now();
        $terms = explode('-', $fiscalYear);
        // print_r($terms);
        // exit;

        if ($terms[0] <= $start->month) {
            $startDate->month($terms[0])->startOfMonth();
            $start->month($terms[0])->startOfMonth();
            $end->month($terms[0])->endOfMonth();
        } else {

            $startDate->subYear()->month($terms[0])->startOfMonth();
            $start->subYear()->month($terms[0])->startOfMonth();
            $end->subYear()->month($terms[0])->endOfMonth();
        }


        $start_year=$start->format('Y-m-d');
        $end_year=$start->subMonth()->endOfMonth()->addYear()->format('Y-m-d');


        if ($request->has('previous_year')) {
            $startDate->subYear()->startOfMonth();
            $start->subYear()->startOfMonth();
            $end->subYear()->endOfMonth();
        }

       

        while ($monthCounter < 12) {
            array_push(
                $invoiceTotals,
                Invoice::whereBetween(
                    'invoice_date',
                    [$start->format('Y-m-d'), $end->format('Y-m-d')]
                )
                ->whereCompany($request->header('company'))
                ->sum('total')
            );
            array_push(
                $expenseTotals,
                Expense::whereBetween(
                    'expense_date',
                    [$start->format('Y-m-d'), $end->format('Y-m-d')]
                )
                ->whereCompany($request->header('company'))
                ->sum('amount')
            );
            array_push(
                $receiptTotals,
                Payment::whereBetween(
                    'payment_date',
                    [$start->format('Y-m-d'), $end->format('Y-m-d')]
                )
                ->whereCompany($request->header('company'))
                ->sum('amount')
            );
            array_push(
                $netProfits,
                ($receiptTotals[$i] - $expenseTotals[$i])
            );
            $i++;
            array_push($months, $start->format('M'));
            $monthCounter++;
            $end->startOfMonth();
            $start->addMonth()->startOfMonth();
            $end->addMonth()->endOfMonth();
        }

        $start->subMonth()->endOfMonth();

        $salesTotal = Invoice::whereCompany($request->header('company'))
            ->whereBetween(
                'invoice_date',
                [$startDate->format('Y-m-d'), $start->format('Y-m-d')]
            )
            ->sum('total');
        $totalReceipts = Payment::whereCompany($request->header('company'))
            ->whereBetween(
                'payment_date',
                [$startDate->format('Y-m-d'), $start->format('Y-m-d')]
            )
            ->sum('amount');
        $totalExpenses = Expense::whereCompany($request->header('company'))
            ->whereBetween(
                'expense_date',
                [$startDate->format('Y-m-d'), $start->format('Y-m-d')]
            )
            ->sum('amount');
        $netProfit = (int)$totalReceipts - (int)$totalExpenses;

        $chartData = [
            'months'        => $months,
            'invoiceTotals' => $invoiceTotals,
            'expenseTotals' => $expenseTotals,
            'receiptTotals' => $receiptTotals,
            'netProfits'    => $netProfits
        ];

         


        
        // count 

        $customersCount = User::customer()->whereCompany($request->header('company'))->get()->count();
        $invoicesCount = Invoice::whereCompany($request->header('company'))->whereBetween('invoice_date',[$start_year, $end_year])->get()->count();
        $estimatesCount = Estimate::whereCompany($request->header('company'))->whereBetween('estimate_date',[$start_year, $end_year])->get()->count();
        $totalDueAmount = Invoice::whereCompany($request->header('company'))->whereBetween('invoice_date',[$start_year, $end_year])->sum('due_amount');
        $dueInvoices = Invoice::with('user')->whereCompany($request->header('company'))->whereBetween('invoice_date',[$start_year, $end_year])->where('due_amount', '>', 0)->take(5)->latest()->get();
        $estimates = Estimate::with('user')->whereCompany($request->header('company'))->whereBetween('estimate_date',[$start_year, $end_year])->take(5)->latest()->get();
        $totalInvoiceAmount = Invoice::whereCompany($request->header('company'))->whereBetween('invoice_date',[$start_year, $end_year])->sum('total');
        
        

        $invoiceCount=Invoice::whereCompany($request->header('company'))->whereBetween('invoice_date',[$start_year, $end_year])->whereIn('paid_status',['PAID','UNPAID','PARTIALLY_PAID'] )->get();
        $invoicePaidCount=$invoiceCount->where('paid_status','PAID')->count();
        $invoiceUnPaidCount=$invoiceCount->where('paid_status','UNPAID')->count();
        $invoicePartiallyPaidCount=$invoiceCount->where('paid_status','PARTIALLY_PAID')->count();
        
        $invoiceStatusData=[
            'invoicePaidCount'        => ($invoicesCount != 0)?round((($invoicePaidCount/$invoicesCount)*100)):'0',
            'invoiceUnPaidCount'      => ($invoicesCount != 0)?round((($invoiceUnPaidCount/$invoicesCount)*100)):'0',
            'invoicePartiallyPaidCount'       => ($invoicesCount != 0)?round((($invoicePartiallyPaidCount/$invoicesCount)*100)):'0',
        ];
 
        $invoiceDueData=[
            'invoiceDueAmount'        => $totalDueAmount ,
            'invoiceDuePercentage'      => ($totalInvoiceAmount != 0) ? round((($totalDueAmount/$totalInvoiceAmount)*100)) : '0',
         ];

        $estimateCount=Estimate::whereCompany($request->header('company'))->whereBetween('estimate_date',[$start_year, $end_year])->whereIn('status',['DRAFT','SENT','ACCEPTED'] )->get();
        $estimatesDraftCount=$estimateCount->where('status','DRAFT')->count();
        $estimatesSentCount=$estimateCount->where('status','SENT')->count();
        $estimatesAcceptedCount=$estimateCount->where('status','ACCEPTED')->count();
             
        $estimatesStatusData=[
            'estimatesDraftCount'        => ($estimatesCount != 0)?round((($estimatesDraftCount/$estimatesCount)*100)):'0',
            'estimatesSentCount'      => ($estimatesCount != 0)?round((($estimatesSentCount/$estimatesCount)*100)):'0',
            'estimatesAcceptedCount'       => ($estimatesCount != 0)?round((($estimatesAcceptedCount/$estimatesCount)*100)):'0',
            
        ];



        //Invoice Analytics Chart
           
           if(!empty($request->input('invoice_type'))){
                if($request->input('invoice_type')=='Weekly'){
                  $invoiceData=Invoice::whereCompany($request->header('company'))->whereBetween('invoice_date',[$week->startOfWeek()->format('Y-m-d'), $week->endOfWeek()->format('Y-m-d')])->whereIn('paid_status',['PAID','UNPAID','PARTIALLY_PAID'] )->get();
                    $invoiceChartPaidCount=$invoiceData->where('paid_status','PAID')->count();
                    $invoiceChartUnPaidCount=$invoiceData->where('paid_status','UNPAID')->count();
                    $invoiceChartPartiallyPaidCount=$invoiceData->where('paid_status','PARTIALLY_PAID')->count();

                    $invoicedAmount=$invoiceData->sum('total');
                    $invoicePendingAmount=$invoiceData->sum('due_amount');
                    $invoiceReceivedAmount=($invoicedAmount-$invoicePendingAmount);

               }

               if($request->input('invoice_type')=='Monthly'){
                    $invoiceData=Invoice::whereCompany($request->header('company'))->whereMonth('invoice_date', carbon::today()->month)->whereIn('paid_status',['PAID','UNPAID','PARTIALLY_PAID'] )->get();
                    $invoiceChartPaidCount=$invoiceData->where('paid_status','PAID')->count();
                    $invoiceChartUnPaidCount=$invoiceData->where('paid_status','UNPAID')->count();
                    $invoiceChartPartiallyPaidCount=$invoiceData->where('paid_status','PARTIALLY_PAID')->count();

                    $invoicedAmount=$invoiceData->sum('total');
                    $invoicePendingAmount=$invoiceData->sum('due_amount');
                    $invoiceReceivedAmount=($invoicedAmount-$invoicePendingAmount);

               }

               if($request->input('invoice_type')=='Yearly'){
                    $invoiceData=Invoice::whereCompany($request->header('company'))->whereBetween('invoice_date',[$start_year, $end_year])->whereIn('paid_status',['PAID','UNPAID','PARTIALLY_PAID'] )->get();
                    $invoiceChartPaidCount=$invoiceData->where('paid_status','PAID')->count();
                    $invoiceChartUnPaidCount=$invoiceData->where('paid_status','UNPAID')->count();
                    $invoiceChartPartiallyPaidCount=$invoiceData->where('paid_status','PARTIALLY_PAID')->count();

                    $invoicedAmount=$invoiceData->sum('total');
                    $invoicePendingAmount=$invoiceData->sum('due_amount');
                    $invoiceReceivedAmount=($invoicedAmount-$invoicePendingAmount);

               }

           }else{

                $invoiceData=Invoice::whereCompany($request->header('company'))->whereMonth('invoice_date', carbon::today()->month)->whereIn('paid_status',['PAID','UNPAID','PARTIALLY_PAID'] )->get();
                    $invoiceChartPaidCount=$invoiceData->where('paid_status','PAID')->count();
                    $invoiceChartUnPaidCount=$invoiceData->where('paid_status','UNPAID')->count();
                    $invoiceChartPartiallyPaidCount=$invoiceData->where('paid_status','PARTIALLY_PAID')->count();

                    $invoicedAmount=$invoiceData->sum('total');
                    $invoicePendingAmount=$invoiceData->sum('due_amount');
                    $invoiceReceivedAmount=($invoicedAmount-$invoicePendingAmount);
           }


           $invoiceAnalyticsChartValue=array( ($invoicesCount != 0)?round((($invoiceChartPaidCount/$invoicesCount)*100)):'0',
                                          ($invoicesCount != 0)?round((($invoiceChartUnPaidCount/$invoicesCount)*100)):'0',
                                          ($invoicesCount != 0)?round((($invoiceChartPartiallyPaidCount/$invoicesCount)*100)):'0');
           $invoiceAnalyticsChartlabel=array('PAID','UNPAID','PARTIALLY PAID');


           $invoiceAnalyticsChartData=['label'=>$invoiceAnalyticsChartlabel,
                                   'value'=>$invoiceAnalyticsChartValue];

          $invoiceAnalyticsChartAmount=['invoicedAmount'=>$invoicedAmount,
                                        'invoicePendingAmount'=>$invoicePendingAmount,
                                        'invoiceReceivedAmount'=>$invoiceReceivedAmount];                       

        
        return response()->json([
            'invoiceAnalyticsChartData'=>$invoiceAnalyticsChartData,
            'invoiceAnalyticsChartAmount'=>$invoiceAnalyticsChartAmount,
            'invoiceStatusData'=>$invoiceStatusData,
            'estimatesStatusData'=>$estimatesStatusData,
            'dueInvoices' => $dueInvoices,
            'estimates' => $estimates,
            'estimatesCount' => $estimatesCount,
            'invoiceDueData' => $invoiceDueData,
            'invoicesCount' => $invoicesCount,
            'customersCount' => $customersCount,
            'chartData' => $chartData,
            'salesTotal' => $salesTotal,
            'totalReceipts' => $totalReceipts,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $netProfit
        ]);
    }


    public function getUserDetails()
    {
        $user = Auth::user();

         return response()->json([
            'user_image' => !empty($user->avatar)?$user->avatar:$this->url->to('/').'/assets/img/default-avatar.jpg',
            'name'=>$user->name
        ]);

              
    }
}
