<?php

namespace App\Http\Controllers\ChartofAccount;

use App\Http\Controllers\Controller;
use App\Models\ChartofAccount\ChartofAccount;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ChartofAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /**
         * Datatable Route
         */
        $datatable_route = route('coa.dataTable');

        /**
         * Store Route
         */
        $store_route = route('coa.store');

        return view('chart_of_account.index', compact('datatable_route', 'store_route'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Show datatable of resource.
     */
    public function dataTable()
    {
        /**
         * Get All Chart of Account
         */
        $stock_in = ChartofAccount::with([
            'createdBy'
        ])
            ->whereNull('deleted_by')
            ->whereNull('deleted_at')
            ->get();

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($stock_in)
            ->addIndexColumn()
            ->addColumn('date', function ($data) {

                /**
                 * Return Format Date & Time
                 */
                return date('d M Y', strtotime($data->date));
            })
            ->addColumn('type', function ($data) {

                /**
                 * Return Type
                 */
                return $data->type == 0 ? 'Debt' : 'Credit';
            })
            ->addColumn('balance', function ($data) {
                return '<div align="right"> Rp. ' . number_format($data->balance, 0, ',', '.') . ',-' . '</div>';
            })
            ->addColumn('action', function ($data) {
                $btn_action = '<div align="center">';
                $btn_action .= '<a href="' . route('coa.show', ['id' => $data->id]) . '" class="btn btn-sm btn-primary" title="Detail">Detail</a>';
                $btn_action .= '<a href="' . route('coa.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-warning ml-2" title="Edit">Edit</a>';
                $btn_action .= '<button class="btn btn-sm btn-danger ml-2" onclick="destroyRecord(' . $data->id . ')" title="Delete">Delete</button>';
                $btn_action .= '</div>';
                return $btn_action;
            })
            ->only(['date', 'type', 'balance', 'action'])
            ->rawColumns(['balance', 'action'])
            ->make(true);

        return $dataTable;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
