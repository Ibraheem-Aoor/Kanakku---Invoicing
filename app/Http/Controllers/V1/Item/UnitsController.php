<?php

namespace Kanakku\Http\Controllers\V1\Item;

use Kanakku\Models\Unit;
use Illuminate\Http\Request;
use Kanakku\Http\Requests\UnitRequest;
use Kanakku\Http\Controllers\Controller;

class UnitsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : 5;

        $units = Unit::whereCompany($request->header('company'))
            ->applyFilters($request->only([
                'unit_id'
            ]))
            ->latest()
            ->paginateData($limit);

        return response()->json([
            'units' => $units
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UnitRequest $request)
    {
        $data = $request->validated();
        $data['company_id'] = $request->header('company');
        $unit = Unit::create($data);

        return response()->json([
            'unit' => $unit
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Kanakku\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function show(Unit $unit)
    {
        return response()->json([
            'unit' => $unit
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Kanakku\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function update(UnitRequest $request, Unit $unit)
    {
        $unit->update($request->validated());

        return response()->json([
            'unit' => $unit
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Kanakku\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Unit $unit)
    {
        if ($unit->items()->exists()) {
            return response()->json([
                'error' => 'items_attached'
            ]);
        }

        $unit->delete();

        return response()->json([
            'success' => 'Unit deleted successfully'
        ]);
    }
}
