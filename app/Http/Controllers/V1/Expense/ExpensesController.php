<?php

namespace Kanakku\Http\Controllers\V1\Expense;

use Kanakku\Models\Expense;
use Illuminate\Http\Request;
use Kanakku\Http\Requests\ExpenseRequest;
use Kanakku\Http\Controllers\Controller;
use Kanakku\Http\Requests\DeleteExpensesRequest;

class ExpensesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : 10;

        $expenses = Expense::with('category', 'user', 'creator', 'fields')
            ->leftJoin('users', 'users.id', '=', 'expenses.user_id')
            ->join('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')
            ->applyFilters($request->only([
                'expense_category_id',
                'user_id',
                'expense_id',
                'search',
                'from_date',
                'to_date',
                'orderByField',
                'orderBy'
            ]))
            ->whereCompany($request->header('company'))
            ->select('expenses.*', 'expense_categories.name', 'users.name as user_name')
            ->paginateData($limit);

        return response()->json([
            'expenses' => $expenses,
            'expenseTotalCount' => Expense::count()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ExpenseRequest $request)
    {
        $expense = Expense::createExpense($request);

        return response()->json([
            'expense' => $expense,
            'success' => true
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Kanakku\Models\Expense $expense
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Expense $expense)
    {
        $expense->load('creator', 'fields.customField');

        return response()->json([
            'expense' => $expense
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Kanakku\Models\Expense $expense
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ExpenseRequest $request, Expense $expense)
    {
        $expense->updateExpense($request);

        return response()->json([
            'expense' => $expense,
            'success' => true
        ]);
    }

    public function delete(DeleteExpensesRequest $request)
    {
        Expense::destroy($request->ids);

        return response()->json([
            'success' => true
        ]);
    }
}
