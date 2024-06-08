<?php

namespace App\Http\Controllers\AccountNumber;

use App\Http\Controllers\Controller;
use App\Models\ChartofAccount\AccountNumber;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AccountNumberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /**
         * Datatable Route
         */
        $datatable_route = route('account-number.dataTable');

        /**
         * Store Route
         */
        $store_route = route('account-number.store');

        return view('account_number.index', compact('datatable_route', 'store_route'));
    }

    /**
     * Show datatable of resource.
     */
    public function dataTable()
    {
        /**
         * Get All Account Number
         */
        $account_number = AccountNumber::with(['createdBy'])
            ->whereNull('deleted_by')
            ->whereNull('deleted_at')
            ->get();

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($account_number)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                $btn_action = '<div align="center">';
                $btn_action .= '<button onclick="openModal(' . "'show'" . ',' . $data->id . ')" class="btn btn-sm btn-primary" title="Detail">Detail</button>';
                $btn_action .= '<button onclick="openModal(' . "'edit'" . ',' . $data->id . ')" class="btn btn-sm btn-warning ml-2" title="Edit">Edit</button>';
                $btn_action .= '<button class="btn btn-sm btn-danger ml-2" onclick="destroyRecord(' . $data->id . ')" title="Delete">Delete</button>';
                $btn_action .= '</div>';
                return $btn_action;
            })
            ->only(['account_number', 'name', 'action'])
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
                'account_number' => 'required',
                'name' => 'required',
            ]);

            /**
             * Validation Unique Field Record
             */
            $account_number_check = AccountNumber::whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->where('account_number', $request->account_number)
                ->first();

            /**
             * Validation Unique Field Record
             */
            if (is_null($account_number_check)) {
                /**
                 * Begin Transaction
                 */
                DB::beginTransaction();

                /**
                 * Create Account Number Record
                 */
                $account_number = AccountNumber::lockforUpdate()->create([
                    'account_number' => $request->account_number,
                    'name' => $request->name,
                    'description' => $request->description,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ]);

                /**
                 * Validation Create Account Number Record
                 */
                if ($account_number) {
                    DB::commit();
                    return redirect()
                        ->route('account-number.index')
                        ->with(['success' => 'Successfully Add Account Number']);
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Failed Add Account Number'])
                        ->withInput();
                }
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Account Number Already Exist'])
                    ->withInput();
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            /**
             * Get Account Number Record from id
             */
            $account_number = AccountNumber::find($id);

            /**
             * Validation Account Number Size id
             */
            if (!is_null($account_number)) {
                $account_number = $account_number->toArray();
                $account_number['updated_at'] = date('d M Y H:i:s', strtotime($account_number['updated_at']));
                return response()->json($account_number, 200);
            } else {
                return response()->json(null, 404);
            }
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 400);
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
                'account_number' => 'required',
                'name' => 'required',
            ]);

            /**
             * Validation Unique Field Record
             */
            $account_number_check = AccountNumber::whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->where('account_number', $request->account_number)
                ->where('id', '!=', $id)
                ->first();

            /**
             * Validation Unique Field Record
             */
            if (is_null($account_number_check)) {
                /**
                 * Begin Transaction
                 */
                DB::beginTransaction();

                /**
                 * Update Account Number Record
                 */
                $account_number_update = AccountNumber::where('id', $id)->update([
                    'account_number' => $request->account_number,
                    'name' => $request->name,
                    'description' => $request->description,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ]);

                /**
                 * Validation Update Account Number Record
                 */
                if ($account_number_update) {
                    DB::commit();
                    return redirect()
                        ->route('account-number.index')
                        ->with(['success' => 'Successfully Update Account Number']);
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Failed Add Account Number'])
                        ->withInput();
                }
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Account Number Already Exist'])
                    ->withInput();
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()])
                ->withInput();
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
             * Update Account Number Record
             */
            $account_number_destroy = AccountNumber::where('id', $id)->update([
                'deleted_by' => Auth::user()->id,
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

            /**
             * Validation Update Account Number Record
             */
            if ($account_number_destroy) {
                DB::commit();
                session()->flash('success', 'Account Number Successfully Deleted');
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                session()->flash('failed', 'Failed Delete Account Number');
            }
        } catch (Exception $e) {
            session()->flash('failed', $e->getMessage());
        }
    }
}
