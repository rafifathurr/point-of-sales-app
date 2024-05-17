<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Product\Supplier;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /**
         * Datatable Route
         */
        $datatable_route = route('supplier.dataTable');

        /**
         * Create Access Based Role
         */
        $can_create = User::find(Auth::user()->id)->hasRole('super-admin');

        return view('supplier.index', compact('datatable_route', 'can_create'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('supplier.create');
    }

    /**
     * Show datatable of resource.
     */
    public function dataTable()
    {
        /**
         * Get All Supplier
         */
        $suppliers = Supplier::whereNull('deleted_by')
            ->whereNull('deleted_at')
            ->get();

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($suppliers)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                $btn_action = '<div align="center">';
                $btn_action .= '<a href="' . route('supplier.show', ['id' => $data->id]) . '" class="btn btn-sm btn-primary" title="Detail">Detail</a>';

                /**
                 * Validation Role Has Access Edit and Delete
                 */
                if (User::find(Auth::user()->id)->hasRole('super-admin')) {
                    $btn_action .= '<a href="' . route('supplier.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-warning ml-2" title="Edit">Edit</a>';
                    $btn_action .= '<button class="btn btn-sm btn-danger ml-2" onclick="destroyRecord(' . $data->id . ')" title="Delete">Delete</button>';
                }
                $btn_action .= '</div>';
                return $btn_action;
            })
            ->only(['name', 'action'])
            ->rawColumns(['action'])
            ->make(true);

        return $dataTable;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            /**
             * Validation Request Body Variables
             */
            $request->validate([
                'name' => 'required|string',
                'phone' => 'required',
            ]);

            /**
             * Validation Unique Field Record
             */
            $phone_check = Supplier::whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->where('phone', $request->phone)
                ->first();

            /**
             * Validation Unique Field Record
             */
            if (is_null($phone_check)) {

                /**
                 * Begin Transaction
                 */
                DB::beginTransaction();

                /**
                 * Create Supplier Record
                 */
                $supplier = Supplier::lockforUpdate()
                    ->create([
                        'name' => $request->name,
                        'phone' => $request->phone,
                        'address' => $request->address,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);

                /**
                 * Validation Create Supplier Record
                 */
                if ($supplier) {
                    DB::commit();
                    return redirect()->route('supplier.index')->with(['success' => 'Successfully Add Supplier']);
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    return redirect()->back()->with(['failed' => 'Failed Add Supplier'])->withInput();
                }
            } else {
                return redirect()->back()->with(['failed' => 'Phone Number Already Exist'])->withInput();
            }
        } catch (Exception $e) {
            return redirect()->back()->with(['failed' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {

            /**
             * Get Supplier Record from id
             */
            $supplier = Supplier::find($id);

            /**
             * Validation Supplier id
             */
            if (!is_null($supplier)) {
                return view('supplier.detail', compact('supplier'));
            } else {
                return redirect()->back()->with(['failed' => 'Invalid Request!']);
            }
        } catch (Exception $e) {
            return redirect()->back()->with(['failed' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {

            /**
             * Get Supplier Record from id
             */
            $supplier = Supplier::find($id);

            /**
             * Validation Supplier id
             */
            if (!is_null($supplier)) {
                return view('supplier.edit', compact('supplier'));
            } else {
                return redirect()->back()->with(['failed' => 'Invalid Request!']);
            }
        } catch (Exception $e) {
            return redirect()->back()->with(['failed' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            /**
             * Validation Request Body Variables
             */
            $request->validate([
                'name' => 'required|string',
                'phone' => 'required',
            ]);

            /**
             * Validation Unique Field Record
             */
            $phone_check = Supplier::whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->where('phone', $request->phone)
                ->where('id', '!=', $id)
                ->first();

            /**
             * Validation Unique Field Record
             */
            if (is_null($phone_check)) {

                /**
                 * Get Supplier Record from id
                 */
                $supplier = Supplier::find($id);

                /**
                 * Validation Supplier id
                 */
                if (!is_null($supplier)) {

                    /**
                     * Begin Transaction
                     */
                    DB::beginTransaction();

                    /**
                     * Update Supplier Record
                     */
                    $supplier_update = Supplier::where('id', $id)
                        ->update([
                            'name' => $request->name,
                            'phone' => $request->phone,
                            'address' => $request->address,
                            'updated_by' => Auth::user()->id,
                        ]);

                    /**
                     * Validation Update Supplier Record
                     */
                    if ($supplier_update) {
                        DB::commit();
                        return redirect()->route('supplier.index')->with(['success' => 'Successfully Update Supplier']);
                    } else {

                        /**
                         * Failed Store Record
                         */
                        DB::rollBack();
                        return redirect()->back()->with(['failed' => 'Failed Update Supplier'])->withInput();
                    }
                } else {
                    return redirect()->back()->with(['failed' => 'Invalid Request!']);
                }
            } else {
                return redirect()->back()->with(['failed' => 'Phone Number Already Exist'])->withInput();
            }
        } catch (Exception $e) {
            return redirect()->back()->with(['failed' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {

            /**
             * Begin Transaction
             */
            DB::beginTransaction();

            /**
             * Update Supplier Record
             */
            $supplier_destroy = Supplier::where('id', $id)
                ->update([
                    'deleted_by' => Auth::user()->id,
                    'deleted_at' => date('Y-m-d H:i:s'),
                ]);

            /**
             * Validation Update Supplier Record
             */
            if ($supplier_destroy) {
                DB::commit();
                session()->flash('success', 'Supplier Successfully Deleted');
            } else {

                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                session()->flash('failed', 'Failed Delete Supplier');
            }
        } catch (Exception $e) {
            session()->flash('failed', $e->getMessage());
        }
    }
}
