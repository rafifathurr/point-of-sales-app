<?php

namespace App\Http\Controllers\PaymentMethod;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder\PaymentMethod;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /**
         * Datatable Route
         */
        $datatable_route = route('payment-method.dataTable');

        /**
         * Create Access Based Role
         */
        $can_create = User::find(Auth::user()->id)->hasRole('super-admin');

        return view('payment_method.index', compact('datatable_route', 'can_create'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('payment_method.create');
    }

    /**
     * Show datatable of resource.
     */
    public function dataTable()
    {
        /**
         * Get All Payment Method
         */
        $payment_method = PaymentMethod::whereNull('deleted_by')->whereNull('deleted_at')->get();

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($payment_method)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                $btn_action = '<div align="center">';
                $btn_action .= '<a href="' . route('payment-method.show', ['id' => $data->id]) . '" class="btn btn-sm btn-primary" title="Detail">Detail</a>';

                /**
                 * Validation Role Has Access Edit and Delete
                 */
                if (User::find(Auth::user()->id)->hasRole('super-admin')) {
                    $btn_action .= '<a href="' . route('payment-method.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-warning ml-2" title="Edit">Edit</a>';
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
            ]);

            /**
             * Get Slug Name
             */
            $slug_name = $this->slugGenerator($request->name);

            /**
             * Validation Unique Field Record
             */
            $slug_name_check = PaymentMethod::whereNull('deleted_by')->whereNull('deleted_at')->where('slug', $slug_name)->first();

            /**
             * Validation Unique Field Record
             */
            if (is_null($slug_name_check)) {
                /**
                 * Begin Transaction
                 */
                DB::beginTransaction();

                /**
                 * Create Payment Method Record
                 */
                $payment_method = PaymentMethod::lockforUpdate()->create([
                    'slug' => $slug_name,
                    'name' => $request->name,
                    'description' => $request->description,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ]);

                /**
                 * Validation Create Payment Method Record
                 */
                if ($payment_method) {
                    DB::commit();
                    return redirect()
                        ->route('payment-method.index')
                        ->with(['success' => 'Successfully Add Payment Method']);
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Failed Add Payment Method'])
                        ->withInput();
                }
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Name Already Exist'])
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
             * Get Payment Method Record from id
             */
            $payment_method = PaymentMethod::find($id);

            /**
             * Validation Payment Method id
             */
            if (!is_null($payment_method)) {
                return view('payment_method.detail', compact('payment_method'));
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Invalid Request!']);
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            /**
             * Get Payment Method Record from id
             */
            $payment_method = PaymentMethod::find($id);

            /**
             * Validation Payment Method id
             */
            if (!is_null($payment_method)) {
                return view('payment_method.edit', compact('payment_method'));
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Invalid Request!']);
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()]);
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
            ]);

            /**
             * Get Slug Name
             */
            $slug_name = $this->slugGenerator($request->name);

            /**
             * Validation Unique Field Record
             */
            $slug_name_check = PaymentMethod::whereNull('deleted_by')->whereNull('deleted_at')->where('slug', $slug_name)->where('id', '!=', $id)->first();

            /**
             * Validation Unique Field Record
             */
            if (is_null($slug_name_check)) {
                /**
                 * Get Payment Method Record from id
                 */
                $payment_method = PaymentMethod::find($id);

                /**
                 * Validation Payment Method id
                 */
                if (!is_null($payment_method)) {
                    /**
                     * Begin Transaction
                     */
                    DB::beginTransaction();

                    /**
                     * Update Payment Method Record
                     */
                    $payment_method_update = PaymentMethod::where('id', $id)->update([
                        'slug' => $slug_name,
                        'name' => $request->name,
                        'description' => $request->description,
                        'updated_by' => Auth::user()->id,
                    ]);

                    /**
                     * Validation Update Payment Method Record
                     */
                    if ($payment_method_update) {
                        DB::commit();
                        return redirect()
                            ->route('payment-method.index')
                            ->with(['success' => 'Successfully Update Payment Method']);
                    } else {
                        /**
                         * Failed Store Record
                         */
                        DB::rollBack();
                        return redirect()
                            ->back()
                            ->with(['failed' => 'Failed Update Payment Method'])
                            ->withInput();
                    }
                } else {
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Invalid Request!']);
                }
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Name Already Exist'])
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
             * Update Payment Method Record
             */
            $payment_method_destroy = PaymentMethod::where('id', $id)->update([
                'deleted_by' => Auth::user()->id,
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

            /**
             * Validation Update Payment Method Record
             */
            if ($payment_method_destroy) {
                DB::commit();
                session()->flash('success', 'Payment Method Successfully Deleted');
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                session()->flash('failed', 'Failed Delete Payment Method');
            }
        } catch (Exception $e) {
            session()->flash('failed', $e->getMessage());
        }
    }

    /**
     * Config Slug
     */
    private function slugGenerator(string $str)
    {
        /**
         * Slug Configuration
         */
        $slug_name = '';
        $exploded_name = explode(' ', strtolower($str));

        if (count($exploded_name) == 1) {
            $alphabet = str_split($exploded_name[0]);
            $str_vocal = ['a', 'i', 'u', 'e', 'o'];
            foreach ($alphabet as $char) {
                if (strlen($slug_name) < 3 && !in_array($char, $str_vocal)) {
                    $slug_name .= $char;
                }
            }
        } else {
            foreach ($exploded_name as $each_string) {
                if (strlen($slug_name) < 3) {
                    $slug_name .= $each_string[0];
                }
            }
        }

        return $slug_name;
    }
}
