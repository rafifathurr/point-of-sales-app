<?php

namespace App\Http\Controllers\SalesOrder;

use App\Http\Controllers\Controller;
use App\Models\Product\ProductSize;
use App\Models\SalesOrder\Customer;
use App\Models\SalesOrder\PaymentMethod;
use App\Models\SalesOrder\SalesOrder;
use App\Models\SalesOrder\SalesOrderItem;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class SalesOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /**
         * Datatable Route
         */
        $datatable_route = route('sales-order.dataTable');

        /**
         * Create Route
         */
        $create_route = route('sales-order.create');

        /**
         * Create Access Based Role
         */
        $can_create = User::find(Auth::user()->id)->hasRole(['admin', 'cashier']);

        return view('sales_order.index', compact('datatable_route', 'create_route', 'can_create'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

        /**
         * Get All Payment Method
         */
        $payment_method = PaymentMethod::whereNull('deleted_by')
            ->whereNull('deleted_at')
            ->get();

        /**
         * Get All Customer Method
         */
        $customer = Customer::whereNull('deleted_by')
            ->whereNull('deleted_at')
            ->get();

        /**
         * Store Route
         */
        $store_route = route('sales-order.store');

        /**
         * Statement sales order create
         */
        $hide_button_hamburger_nav = true;

        session()->flashInput($request->input());

        return view('sales_order.create', compact('payment_method', 'customer', 'store_route', 'hide_button_hamburger_nav'));
    }

    public function catalogueProduct()
    {
        /**
         * Get All Product
         */
        $product_size = ProductSize::with(['product.categoryProduct', 'discount'])
            ->whereHas('product', function ($query) {
                return $query->where('status', 1);
            })
            ->whereNull('deleted_by')
            ->whereNull('deleted_at')
            ->paginate(9);

        return view('sales_order.includes.catalogue', ['product_size' => $product_size]);
    }

    /**
     * Show datatable of resource.
     */
    public function dataTable()
    {
        /**
         * Get All Sales Order
         */
        $sales_order = SalesOrder::with([
            'paymentMethod'
        ])
            ->whereNull('deleted_by')
            ->whereNull('deleted_at')
            ->get();

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($sales_order)
            ->addIndexColumn()
            ->addColumn('created_at', function ($data) {

                /**
                 * Return Format Date & Time
                 */
                return date('d M Y H:i:s', strtotime($data->date));
            })
            ->addColumn('type', function ($data) {
                return $data->type == 0 ? 'Offline' : 'Online';
            })
            ->addColumn('payment_method', function ($data) {
                return $data->paymentMethod->name;
            })
            ->addColumn('grand_sell_price', function ($data) {
                return 'Rp. ' . number_format($data->grand_sell_price, 0, ',', '.') . ',-';
            })
            ->addColumn('action', function ($data) {
                $btn_action = '<div align="center">';
                $btn_action .= '<a href="' . route('sales-order.show', ['id' => $data->id]) . '" class="btn btn-sm btn-primary" title="Detail">Detail</a>';

                /**
                 * Validation Role Has Access Edit and Delete
                 */
                if (User::find(Auth::user()->id)->hasRole(['admin', 'cashier'])) {
                    $btn_action .= '<a href="' . route('sales-order.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-warning ml-2" title="Edit">Edit</a>';
                    $btn_action .= '<button class="btn btn-sm btn-danger ml-2" onclick="destroyRecord(' . $data->id . ')" title="Delete">Delete</button>';
                }
                $btn_action .= '</div>';
                return $btn_action;
            })
            ->only(['created_at', 'type', 'payment_method', 'action'])
            ->rawColumns(['action'])
            ->make(true);

        return $dataTable;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        dd($request->all());
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
        try {

            /**
             * Begin Transaction
             */
            DB::beginTransaction();

            /**
             * Update Sales Order Record
             */
            $sales_order_destroy = SalesOrder::where('id', $id)
                ->update([
                    'deleted_by' => Auth::user()->id,
                    'deleted_at' => date('Y-m-d H:i:s'),
                ]);

            /**
             * Validation Update Sales Order Record
             */
            if ($sales_order_destroy) {

                /**
                 * Update Sales Order Item Record
                 */
                $sales_order_item_destroy = SalesOrderItem::where('sales_order_id', $id)
                    ->update([
                        'deleted_by' => Auth::user()->id,
                        'deleted_at' => date('Y-m-d H:i:s'),
                    ]);

                if ($sales_order_item_destroy) {
                    DB::commit();
                    session()->flash('success', 'Sales Order Successfully Deleted');
                } else {

                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    session()->flash('failed', 'Failed Delete Sales Order');
                }
            } else {

                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                session()->flash('failed', 'Failed Delete Sales Order');
            }
        } catch (Exception $e) {
            session()->flash('failed', $e->getMessage());
        }
    }
}
