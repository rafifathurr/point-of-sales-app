<?php

namespace App\Http\Controllers\ChartofAccount;

use App\Http\Controllers\Controller;
use App\Models\ChartofAccount\AccountNumber;
use App\Models\ChartofAccount\ChartofAccount;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        /**
         * Account Number
         */
        $account_number_collection = AccountNumber::with(['createdBy'])
            ->whereNull('deleted_by')
            ->whereNull('deleted_at')
            ->get();

        return view('chart_of_account.index', compact('datatable_route', 'store_route', 'account_number_collection'));
    }

    /**
     * Show datatable of resource.
     */
    public function dataTable()
    {
        /**
         * Get All Chart of Account
         */
        $chart_of_account = ChartofAccount::with(['accountNumber', 'createdBy'])
            ->whereNull('deleted_by')
            ->whereNull('deleted_at');

        if (User::find(Auth::user()->id)->hasRole('super-admin')) {
            $chart_of_account = $chart_of_account->get();
        } else {
            $chart_of_account = $chart_of_account->where('created_by', Auth::user()->id)->get();
        }

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($chart_of_account)
            ->addIndexColumn()
            ->addColumn('account_number', function ($data) {
                /**
                 * Return Relation Account Number
                 */
                return $data->accountNumber->account_number;
            })
            ->addColumn('date', function ($data) {
                /**
                 * Return Format Date & Time
                 */
                return date('d F Y', strtotime($data->date));
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
                $btn_action .= '<button onclick="openModal(' . "'show'" . ',' . $data->id . ')" class="btn btn-sm btn-primary" title="Detail">Detail</button>';
                $btn_action .= '<button onclick="openModal(' . "'edit'" . ',' . $data->id . ')" class="btn btn-sm btn-warning ml-2" title="Edit">Edit</button>';
                $btn_action .= '<button class="btn btn-sm btn-danger ml-2" onclick="destroyRecord(' . $data->id . ')" title="Delete">Delete</button>';
                $btn_action .= '</div>';
                return $btn_action;
            })
            ->only(['account_number', 'date', 'type', 'name', 'balance', 'action'])
            ->rawColumns(['balance', 'action'])
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
                'date' => 'required',
                'name' => 'required',
                'account_number' => 'required',
                'type' => 'required',
                'balance' => 'required',
            ]);

            /**
             * Begin Transaction
             */
            DB::beginTransaction();

            /**
             * Create Chart of Account Record
             */
            $chart_of_account = ChartofAccount::lockforUpdate()->create([
                'account_number_id' => $request->account_number,
                'name' => $request->name,
                'date' => $request->date,
                'type' => $request->type,
                'balance' => $request->balance,
                'description' => $request->description,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ]);

            /**
             * Validation Create Chart of Account Record
             */
            if ($chart_of_account) {
                DB::commit();
                return redirect()
                    ->route('coa.index')
                    ->with(['success' => 'Successfully Add Chart of Account']);
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with(['failed' => 'Failed Add Chart of Account'])
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
             * Get Chart of Account Record from id
             */
            $chart_of_account = ChartofAccount::with(['accountNumber', 'createdBy', 'updatedBy'])->find($id);

            /**
             * Validation Chart of Account Size id
             */
            if (!is_null($chart_of_account)) {
                $chart_of_account = $chart_of_account->toArray();
                $chart_of_account['updated_by'] = $chart_of_account['updated_by']['name'];
                $chart_of_account['updated_at'] = date('d F Y H:i:s', strtotime($chart_of_account['updated_at']));
                return response()->json($chart_of_account, 200);
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
                'date' => 'required',
                'name' => 'required',
                'type' => 'required',
                'balance' => 'required',
            ]);

            /**
             * Begin Transaction
             */
            DB::beginTransaction();

            /**
             * Update Chart of Account Record
             */
            $chart_of_account_update = ChartofAccount::where('id', $id)->update([
                'date' => $request->date,
                'name' => $request->name,
                'type' => $request->type,
                'balance' => $request->balance,
                'description' => $request->description,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ]);

            /**
             * Validation Update Chart of Account Record
             */
            if ($chart_of_account_update) {
                DB::commit();
                return redirect()
                    ->route('coa.index')
                    ->with(['success' => 'Successfully Update Chart of Account']);
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with(['failed' => 'Failed Add Chart of Account'])
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
             * Update Chart of Account Record
             */
            $chart_of_account_destroy = ChartofAccount::where('id', $id)->update([
                'deleted_by' => Auth::user()->id,
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

            /**
             * Validation Update Chart of Account Record
             */
            if ($chart_of_account_destroy) {
                DB::commit();
                session()->flash('success', 'Chart of Account Successfully Deleted');
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                session()->flash('failed', 'Failed Delete Chart of Account');
            }
        } catch (Exception $e) {
            session()->flash('failed', $e->getMessage());
        }
    }
}
