<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder\Customer;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /**
         * Datatable Route
         */
        $datatable_route = route('customer.dataTable');

        /**
         * Create Access Based Role
         */
        $can_create = User::find(Auth::user()->id)->hasRole(['admin', 'cashier']);

        return view('customer.index', compact('datatable_route', 'can_create'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customer.create');
    }

    /**
     * Show datatable of resource.
     */
    public function dataTable()
    {
        /**
         * Get All Customer
         */
        $customers = Customer::whereNull('deleted_by')
            ->whereNull('deleted_at')
            ->get();

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($customers)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                $btn_action = '<div align="center">';
                $btn_action .= '<a href="' . route('customer.show', ['id' => $data->id]) . '" class="btn btn-sm btn-primary" title="Detail">Detail</a>';

                /**
                 * Validation Role Has Access Edit and Delete
                 */
                if (User::find(Auth::user()->id)->hasRole(['admin', 'cashier'])) {
                    $btn_action .= '<a href="' . route('customer.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-warning ml-2" title="Edit">Edit</a>';
                    $btn_action .= '<button class="btn btn-sm btn-danger ml-2" onclick="destroyRecord(' . $data->id . ')" title="Delete">Delete</button>';
                }
                $btn_action .= '</div>';
                return $btn_action;
            })
            ->only(['name', 'phone', 'action'])
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
            $phone_check = Customer::whereNull('deleted_by')
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
                 * Create Customer Record
                 */
                $customer = Customer::create([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ]);

                /**
                 * Validation Create Customer Record
                 */
                if ($customer) {
                    DB::commit();
                    return redirect()->route('customer.index')->with(['success' => 'Successfully Add Customer']);
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    return redirect()->back()->with(['failed' => 'Failed Add Customer'])->withInput();
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
             * Get Customer Record from id
             */
            $customer = Customer::find($id);

            /**
             * Validation Customer id
             */
            if (!is_null($customer)) {
                return view('customer.detail', compact('customer'));
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
             * Get Customer Record from id
             */
            $customer = Customer::find($id);

            /**
             * Validation Customer id
             */
            if (!is_null($customer)) {
                return view('customer.edit', compact('customer'));
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
            $phone_check = Customer::whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->where('phone', $request->phone)
                ->where('id', '!=', $id)
                ->first();

            /**
             * Validation Unique Field Record
             */
            if (is_null($phone_check)) {

                /**
                 * Get Customer Record from id
                 */
                $customer = Customer::find($id);

                /**
                 * Validation Customer id
                 */
                if (!is_null($customer)) {

                    /**
                     * Begin Transaction
                     */
                    DB::beginTransaction();

                    /**
                     * Update Customer Record
                     */
                    $customer_update = Customer::where('id', $id)
                        ->update([
                            'name' => $request->name,
                            'phone' => $request->phone,
                            'address' => $request->address,
                            'updated_by' => Auth::user()->id,
                        ]);

                    /**
                     * Validation Update Customer Record
                     */
                    if ($customer_update) {
                        DB::commit();
                        return redirect()->route('customer.index')->with(['success' => 'Successfully Update Customer']);
                    } else {

                        /**
                         * Failed Store Record
                         */
                        DB::rollBack();
                        return redirect()->back()->with(['failed' => 'Failed Update Customer'])->withInput();
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
             * Update Customer Record
             */
            $customer_destroy = Customer::where('id', $id)
                ->update([
                    'deleted_by' => Auth::user()->id,
                    'deleted_at' => date('Y-m-d H:i:s'),
                ]);

            /**
             * Validation Update Customer Record
             */
            if ($customer_destroy) {
                DB::commit();
                session()->flash('success', 'Customer Successfully Deleted');
            } else {

                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                session()->flash('failed', 'Failed Delete Customer');
            }
        } catch (Exception $e) {
            return redirect()->back()->with(['failed' => $e->getMessage()])->withInput();
        }
    }
}
