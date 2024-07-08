<?php

namespace App\Http\Controllers\SalesOrder;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\Product\ProductSize;
use App\Models\SalesOrder\Customer;
use App\Models\SalesOrder\PaymentMethod;
use App\Models\SalesOrder\SalesOrder;
use App\Models\SalesOrder\SalesOrderItem;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $payment_method = PaymentMethod::whereNull('deleted_by')->whereNull('deleted_at')->get();

        /**
         * Get All Customer Method
         */
        $customer = Customer::whereNull('deleted_by')->whereNull('deleted_at')->get();

        /**
         * Store Route
         */
        $store_route = route('sales-order.store');

        /**
         * Statement sales order create
         */
        $hide_button_hamburger_nav = true;

        return view('sales_order.create', compact('payment_method', 'customer', 'store_route', 'hide_button_hamburger_nav'));
    }

    /**
     * Get Catalogue Menu
     */
    public function catalogueProduct(Request $request)
    {
        /**
         * Request Parameter
         */
        $input = $request->all();

        /**
         * Request For Update Order
         */
        if (!isset($input['update'])) {
            if (!is_null($input['query'])) {
                /**
                 * Get All Product
                 */
                $product_size = ProductSize::with(['product.categoryProduct', 'discount'])
                    ->whereHas('product', function ($query) use ($input) {
                        return $query->where('status', 1)->where('name', 'like', '%' . $input['query'] . '%');
                    })
                    ->whereNull('deleted_by')
                    ->whereNull('deleted_at')
                    ->paginate(4);
            } else {
                /**
                 * Get All Product
                 */
                $product_size = ProductSize::with(['product.categoryProduct', 'discount'])
                    ->whereHas('product', function ($query) {
                        return $query->where('status', 1);
                    })
                    ->whereNull('deleted_by')
                    ->whereNull('deleted_at')
                    ->paginate(4);
            }

            if (count($product_size) > 0) {
                return view('sales_order.includes.catalogue', ['product_size' => $product_size, 'update' => false]);
            } else {
                return view('sales_order.includes.notfound');
            }
        } else {
            if (!is_null($input['query'])) {
                /**
                 * Get All Product
                 */
                $product_size = ProductSize::with(['product.categoryProduct', 'discount'])
                    ->whereHas('product', function ($query) use ($input) {
                        return $query->where('name', 'like', '%' . $input['query'] . '%');
                    })
                    ->whereNull('deleted_by')
                    ->whereNull('deleted_at')
                    ->paginate(9);
            } else {
                /**
                 * Get All Product
                 */
                $product_size = ProductSize::with(['product.categoryProduct', 'discount'])
                    ->whereNull('deleted_by')
                    ->whereNull('deleted_at')
                    ->paginate(9);
            }

            if (count($product_size) > 0) {
                return view('sales_order.includes.catalogue', ['product_size' => $product_size, 'update' => false]);
            } else {
                return view('sales_order.includes.notfound');
            }
        }
    }

    /**
     * Show datatable of resource.
     */
    public function dataTable()
    {
        /**
         * Get All Sales Order
         */
        $sales_order = SalesOrder::with(['paymentMethod'])
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
                return date('d F Y H:i:s', strtotime($data->created_at));
            })
            ->addColumn('type', function ($data) {
                return $data->type == 0 ? 'Offline' : 'Online';
            })
            ->addColumn('payment_method', function ($data) {
                return $data->paymentMethod->name;
            })
            ->addColumn('grand_sell_price', function ($data) {
                return '<div align="right"> Rp. ' . number_format($data->grand_sell_price, 0, ',', '.') . ',-' . '</div>';
            })
            ->addColumn('action', function ($data) {
                $btn_action = '<div align="center">';
                $btn_action .= '<a href="' . route('sales-order.show', ['id' => $data->id]) . '" class="btn btn-sm btn-primary" title="Detail">Detail</a>';

                /**
                 * Validation Role Has Access Edit and Delete
                 */
                if (User::find(Auth::user()->id)->hasRole(['admin', 'cashier']) && Auth::user()->id == $data->created_by) {
                    $btn_action .= '<a href="' . route('sales-order.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-warning ml-2" title="Edit">Edit</a>';
                    $btn_action .= '<button class="btn btn-sm btn-danger ml-2" onclick="destroyRecord(' . $data->id . ')" title="Delete">Delete</button>';
                }
                $btn_action .= '<a href="' . route('sales-order.invoice', ['id' => $data->id]) . '" class="btn btn-sm btn-success ml-2" target="_blank" title="Invoice">Invoice</a>';
                $btn_action .= '</div>';
                return $btn_action;
            })
            ->only(['invoice_number', 'created_at', 'type', 'payment_method', 'grand_sell_price', 'action'])
            ->rawColumns(['grand_sell_price', 'action'])
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
                'type' => 'required',
                'payment_method' => 'required',
                'sales_order_item' => 'required',
                'total_capital_price' => 'required',
                'total_sell_price' => 'required',
                'discount_price' => 'required',
                'grand_sell_price' => 'required',
                'grand_profit_price' => 'required',
            ]);

            /**
             * Create Invoice Number
             */
            $invoice_number = 'INV/' . date('Y') . '/' . strtotime(date('Y-m-d H:i:s'));

            /**
             * Begin Transaction
             */
            DB::beginTransaction();

            /**
             * Create Sales Order Record
             */
            $sales_order = SalesOrder::lockforUpdate()->create([
                'invoice_number' => $invoice_number,
                'customer_id' => $request->customer,
                'payment_method_id' => $request->payment_method,
                'type' => $request->type,
                'total_capital_price' => $request->total_capital_price,
                'total_sell_price' => $request->total_sell_price,
                'discount_price' => $request->discount_price,
                'grand_sell_price' => $request->grand_sell_price,
                'grand_profit_price' => $request->grand_profit_price,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ]);

            /**
             * Validation Create Stock In Record
             */
            if ($sales_order) {
                /**
                 * Validation request customer
                 */
                if (!is_null($sales_order->customer_id)) {
                    /**
                     * Customer Record
                     */
                    $customer = Customer::find($sales_order->customer_id);

                    /**
                     * Calculation Point
                     */
                    $calculation_point = $customer->point + $this->generatePoint($sales_order->grand_sell_price);

                    /**
                     * Update Customer Point Record
                     */
                    $customer_update = Customer::where('id', $sales_order->customer_id)->update([
                        'point' => $calculation_point,
                        'updated_by' => Auth::user()->id,
                    ]);

                    if ($customer_update) {
                        /**
                         * Each Sales Order Item Product Request
                         */
                        foreach ($request->sales_order_item as $product_id => $sales_order_item_request_product) {
                            /**
                             * Variable update status
                             */
                            $product_action_status_update = false;

                            /**
                             * Each Sales Order Item Product Size Request
                             */
                            foreach ($sales_order_item_request_product['product_size'] as $product_size_id => $sales_order_item_request_product_size) {
                                /**
                                 * Create Sales Order Item Record
                                 */
                                $sales_order_item = SalesOrderItem::lockforUpdate()->create([
                                    'product_size_id' => $product_size_id,
                                    'sales_order_id' => $sales_order->id,
                                    'qty' => $sales_order_item_request_product_size['qty'],
                                    'capital_price' => $sales_order_item_request_product_size['capital_price'],
                                    'sell_price' => $sales_order_item_request_product_size['sell_price'],
                                    'discount_price' => $sales_order_item_request_product_size['discount_price'],
                                    'total_sell_price' => $sales_order_item_request_product_size['total_sell_price'],
                                    'total_profit_price' => $sales_order_item_request_product_size['total_profit_price'],
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);

                                /**
                                 * Validation Create Sales Order Item Record
                                 */
                                if (!$sales_order_item) {
                                    /**
                                     * Failed Store Record
                                     */
                                    DB::rollBack();
                                    return redirect()
                                        ->back()
                                        ->with(['failed' => 'Failed Store Sales Order Item'])
                                        ->withInput();
                                }

                                /**
                                 * Calculation Stock
                                 */
                                $calculation_stock = intval($sales_order_item_request_product_size['stock']) - intval($sales_order_item_request_product_size['qty']);

                                /**
                                 * Update Stock of Product
                                 */
                                $product_size_update = ProductSize::where('id', $product_size_id)->update([
                                    'stock' => $calculation_stock,
                                    'updated_by' => Auth::user()->id,
                                ]);

                                /**
                                 * Check Last Empty Stock of Product Another Product Request
                                 */
                                $last_stock_product_record = ProductSize::where('product_id', $product_id)->whereNull('deleted_by')->whereNull('deleted_at')->where('id', '!=', $product_size_id)->where('stock', '>', 0)->get()->toArray();

                                /**
                                 * Validation Update Stock of Product
                                 */
                                if (!$product_size_update) {
                                    /**
                                     * Failed Store Record
                                     */
                                    DB::rollBack();
                                    return redirect()
                                        ->back()
                                        ->with(['failed' => 'Failed Update Stock Product'])
                                        ->withInput();
                                }

                                /**
                                 * Validation Status and Stock of Product
                                 */
                                if (empty($last_stock_product_record) && $calculation_stock == 0) {
                                    /**
                                     * Variable update status
                                     */
                                    $product_action_status_update = true;
                                } else {
                                    /**
                                     * Variable update status
                                     */
                                    $product_action_status_update = false;
                                }
                            }

                            /**
                             * Validation update status product
                             */
                            if ($product_action_status_update) {
                                /**
                                 * Update Status Product
                                 */
                                $product_update = Product::where('id', $product_id)->update([
                                    'status' => 0,
                                    'updated_by' => Auth::user()->id,
                                ]);

                                /**
                                 * Validation Update Product
                                 */
                                if (!$product_update) {
                                    /**
                                     * Failed Store Record
                                     */
                                    DB::rollBack();
                                    return redirect()
                                        ->back()
                                        ->with(['failed' => 'Failed Update Product'])
                                        ->withInput();
                                }
                            } else {
                                /**
                                 * Update Product
                                 */
                                $product_update = Product::where('id', $product_id)->update([
                                    'updated_by' => Auth::user()->id,
                                ]);

                                /**
                                 * Validation Update Product
                                 */
                                if (!$product_update) {
                                    /**
                                     * Failed Store Record
                                     */
                                    DB::rollBack();
                                    return redirect()
                                        ->back()
                                        ->with(['failed' => 'Failed Update Product'])
                                        ->withInput();
                                }
                            }
                        }

                        DB::commit();
                        return redirect()
                            ->route('sales-order.show', ['id' => $sales_order->id])
                            ->with(['success' => 'Successfully Add Sales Order']);
                    } else {
                        /**
                         * Failed Store Record
                         */
                        DB::rollBack();
                        return redirect()
                            ->back()
                            ->with(['failed' => 'Failed Update Customer'])
                            ->withInput();
                    }
                } else {
                    /**
                     * Each Sales Order Item Product Request
                     */
                    foreach ($request->sales_order_item as $product_id => $sales_order_item_request_product) {
                        /**
                         * Variable update status
                         */
                        $product_action_status_update = false;

                        /**
                         * Each Sales Order Item Product Size Request
                         */
                        foreach ($sales_order_item_request_product['product_size'] as $product_size_id => $sales_order_item_request_product_size) {
                            /**
                             * Create Sales Order Item Record
                             */
                            $sales_order_item = SalesOrderItem::lockforUpdate()->create([
                                'product_size_id' => $product_size_id,
                                'sales_order_id' => $sales_order->id,
                                'qty' => $sales_order_item_request_product_size['qty'],
                                'capital_price' => $sales_order_item_request_product_size['capital_price'],
                                'sell_price' => $sales_order_item_request_product_size['sell_price'],
                                'discount_price' => $sales_order_item_request_product_size['discount_price'],
                                'total_sell_price' => $sales_order_item_request_product_size['total_sell_price'],
                                'total_profit_price' => $sales_order_item_request_product_size['total_profit_price'],
                                'created_by' => Auth::user()->id,
                                'updated_by' => Auth::user()->id,
                            ]);

                            /**
                             * Validation Create Sales Order Item Record
                             */
                            if (!$sales_order_item) {
                                /**
                                 * Failed Store Record
                                 */
                                DB::rollBack();
                                return redirect()
                                    ->back()
                                    ->with(['failed' => 'Failed Store Sales Order Item'])
                                    ->withInput();
                            }

                            /**
                             * Calculation Stock
                             */
                            $calculation_stock = intval($sales_order_item_request_product_size['stock']) - intval($sales_order_item_request_product_size['qty']);

                            /**
                             * Update Stock of Product
                             */
                            $product_size_update = ProductSize::where('id', $product_size_id)->update([
                                'stock' => $calculation_stock,
                                'updated_by' => Auth::user()->id,
                            ]);

                            /**
                             * Check Last Empty Stock of Product Another Product Request
                             */
                            $last_stock_product_record = ProductSize::where('product_id', $product_id)->whereNull('deleted_by')->whereNull('deleted_at')->where('id', '!=', $product_size_id)->where('stock', '>', 0)->get()->toArray();

                            /**
                             * Validation Update Stock of Product
                             */
                            if (!$product_size_update) {
                                /**
                                 * Failed Store Record
                                 */
                                DB::rollBack();
                                return redirect()
                                    ->back()
                                    ->with(['failed' => 'Failed Update Stock Product'])
                                    ->withInput();
                            }

                            /**
                             * Validation Status and Stock of Product
                             */
                            if (empty($last_stock_product_record) && $calculation_stock == 0) {
                                /**
                                 * Variable update status
                                 */
                                $product_action_status_update = true;
                            } else {
                                /**
                                 * Variable update status
                                 */
                                $product_action_status_update = false;
                            }
                        }

                        /**
                         * Validation update status product
                         */
                        if ($product_action_status_update) {
                            /**
                             * Update Status Product
                             */
                            $product_update = Product::where('id', $product_id)->update([
                                'status' => 0,
                                'updated_by' => Auth::user()->id,
                            ]);

                            /**
                             * Validation Update Product
                             */
                            if (!$product_update) {
                                /**
                                 * Failed Store Record
                                 */
                                DB::rollBack();
                                return redirect()
                                    ->back()
                                    ->with(['failed' => 'Failed Update Product'])
                                    ->withInput();
                            }
                        } else {
                            /**
                             * Update Product
                             */
                            $product_update = Product::where('id', $product_id)->update([
                                'updated_by' => Auth::user()->id,
                            ]);

                            /**
                             * Validation Update Product
                             */
                            if (!$product_update) {
                                /**
                                 * Failed Store Record
                                 */
                                DB::rollBack();
                                return redirect()
                                    ->back()
                                    ->with(['failed' => 'Failed Update Product'])
                                    ->withInput();
                            }
                        }
                    }

                    DB::commit();
                    return redirect()
                        ->route('sales-order.show', ['id' => $sales_order->id])
                        ->with(['success' => 'Successfully Add Sales Order']);
                }
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with(['failed' => 'Failed Store Sales Order'])
                    ->withInput();
            }
        } catch (Exception $e) {
            return redirect()
                ->route('sales-order.create')
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
             * Get Sales Order Record from id
             */
            $sales_order = SalesOrder::with(['customer', 'paymentMethod', 'salesOrderItem.productSize.product'])->find($id);

            /**
             * Validation Sales Order id
             */
            if (!is_null($sales_order)) {

                /**
                 * Point Obtained from Sales Order
                 */
                $point_obtained = $this->generatePoint($sales_order->grand_sell_price);

                /**
                 * Show Capital Price Access Based Role
                 */
                $show_capital_price = User::find(Auth::user()->id)->hasRole(['super-admin', 'admin']);

                return view('sales_order.detail', compact('sales_order', 'show_capital_price', 'point_obtained'));
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
     * Export invoice the specified resource.
     */
    public function invoice(string $id)
    {
        try {
            /**
             * Get Sales Order Record from id
             */
            $sales_order = SalesOrder::with(['customer', 'paymentMethod', 'salesOrderItem.productSize.product'])->find($id);

            /**
             * Validation Sales Order id
             */
            if (!is_null($sales_order)) {
                /**
                 * Return PDF format
                 */
                return PDF::loadView('sales_order.invoice', ['sales_order' => $sales_order])->stream($sales_order->invoice_number . '.pdf');
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
             * Get Sales Order Record from id
             */
            $sales_order = SalesOrder::with(['customer', 'paymentMethod', 'salesOrderItem.productSize.product'])->find($id);

            /**
             * Validation Sales Order id
             */
            if (!is_null($sales_order)) {
                /**
                 * Update Route
                 */
                $update_route = route('sales-order.update', ['id' => $id]);

                /**
                 * Get All Payment Method
                 */
                $payment_method = PaymentMethod::whereNull('deleted_by')->whereNull('deleted_at')->get();

                /**
                 * Get All Customer Method
                 */
                $customer = Customer::whereNull('deleted_by')->whereNull('deleted_at')->get();
                /**
                 * Statement sales order create
                 */
                $hide_button_hamburger_nav = true;

                return view('sales_order.edit', compact('update_route', 'sales_order', 'payment_method', 'customer', 'hide_button_hamburger_nav'));
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
                'type' => 'required',
                'payment_method' => 'required',
                'sales_order_item' => 'required',
                'total_capital_price' => 'required',
                'total_sell_price' => 'required',
                'discount_price' => 'required',
                'grand_sell_price' => 'required',
                'grand_profit_price' => 'required',
            ]);

            /**
             * Last Sales Order Record
             */
            $sales_order = SalesOrder::with(['customer', 'paymentMethod', 'salesOrderItem.productSize.product'])->find($id);

            /**
             * Begin Transaction
             */
            DB::beginTransaction();

            /**
             * Update Sales Order
             */
            $sales_order_updated = SalesOrder::where('id', $id)->update([
                'customer_id' => $request->customer,
                'payment_method_id' => $request->payment_method,
                'type' => $request->type,
                'total_capital_price' => $request->total_capital_price,
                'total_sell_price' => $request->total_sell_price,
                'discount_price' => $request->discount_price,
                'grand_sell_price' => $request->grand_sell_price,
                'grand_profit_price' => $request->grand_profit_price,
                'updated_by' => Auth::user()->id,
            ]);

            if ($sales_order_updated) {
                /**
                 * Validation request customer
                 */
                if (!is_null($request->customer) || !is_null($sales_order->customer_id)) {
                    /**
                     * Validation Request Customer and Customer Record Sales Order
                     */
                    if (!is_null($request->customer) && !is_null($sales_order->customer_id)) {
                        /**
                         * Validation Customer Requested Equals thann Sales Order Customer Record
                         */
                        if ($request->customer == $sales_order->customer_id) {
                            /**
                             * Customer Record
                             */
                            $customer = Customer::find($sales_order->customer_id);

                            /**
                             * Calculation Last Point
                             */
                            $calculation_point_last = $customer->point - $this->generatePoint($sales_order->grand_sell_price);

                            /**
                             * Check Calculation Less Than 0
                             */
                            if ($calculation_point_last < 0) {
                                $calculation_point_last = 0;
                            }

                            /**
                             * Calculation Point Result
                             */
                            $calculation_point_result = $calculation_point_last + $this->generatePoint($request->grand_sell_price);

                            /**
                             * Update Customer Point Record
                             */
                            $customer_update = Customer::where('id', $sales_order->customer_id)->update([
                                'point' => $calculation_point_result,
                                'updated_by' => Auth::user()->id,
                            ]);

                            if ($customer_update) {
                                /**
                                 * Destroy Last Sales Order Item
                                 */
                                $sales_order_item_destroy = SalesOrderItem::where('sales_order_id', $id)->update([
                                    'deleted_by' => Auth::user()->id,
                                    'deleted_at' => date('Y-m-d H:i:s'),
                                ]);

                                if ($sales_order_item_destroy) {
                                    /**
                                     * Each Sales Order Item Product Request
                                     */
                                    foreach ($request->sales_order_item as $sales_order_item_request_product) {
                                        /**
                                         * Each Sales Order Item Product Size Request
                                         */
                                        foreach ($sales_order_item_request_product['product_size'] as $product_size_id => $sales_order_item_request_product_size) {
                                            /**
                                             * Create Sales Order Item Record
                                             */
                                            $sales_order_item = SalesOrderItem::lockforUpdate()->create([
                                                'product_size_id' => $product_size_id,
                                                'sales_order_id' => $sales_order->id,
                                                'qty' => $sales_order_item_request_product_size['qty'],
                                                'capital_price' => $sales_order_item_request_product_size['capital_price'],
                                                'sell_price' => $sales_order_item_request_product_size['sell_price'],
                                                'discount_price' => $sales_order_item_request_product_size['discount_price'],
                                                'total_sell_price' => $sales_order_item_request_product_size['total_sell_price'],
                                                'total_profit_price' => $sales_order_item_request_product_size['total_profit_price'],
                                                'created_by' => Auth::user()->id,
                                                'updated_by' => Auth::user()->id,
                                            ]);

                                            /**
                                             * Validation Create Sales Order Item Record
                                             */
                                            if (!$sales_order_item) {
                                                /**
                                                 * Failed Store Record
                                                 */
                                                DB::rollBack();
                                                return redirect()
                                                    ->back()
                                                    ->with(['failed' => 'Failed Store Sales Order Item'])
                                                    ->withInput();
                                            }
                                        }
                                    }

                                    DB::commit();
                                    return redirect()
                                        ->route('sales-order.show', ['id' => $sales_order->id])
                                        ->with(['success' => 'Successfully Update Sales Order']);
                                } else {
                                    /**
                                     * Failed Update Destroy
                                     */
                                    DB::rollBack();
                                    return redirect()
                                        ->back()
                                        ->with(['failed' => 'Failed Destroy Sales Order Item'])
                                        ->withInput();
                                }
                            } else {
                                /**
                                 * Failed Update Record
                                 */
                                DB::rollBack();
                                return redirect()
                                    ->back()
                                    ->with(['failed' => 'Failed Update Customer'])
                                    ->withInput();
                            }
                        } else {
                            /**
                             * Customer Record
                             */
                            $customer = Customer::find($sales_order->customer_id);
                            $customer_new = Customer::find($request->customer);

                            /**
                             * Calculation Last Point
                             */
                            $calculation_point_last = $customer->point - $this->generatePoint($sales_order->grand_sell_price);

                            /**
                             * Check Calculation Less Than 0
                             */
                            if ($calculation_point_last < 0) {
                                $calculation_point_last = 0;
                            }

                            /**
                             * Calculation Point Result
                             */
                            $calculation_point_result = $customer_new->point + $this->generatePoint($request->grand_sell_price);

                            /**
                             * Update Customer Point Record
                             */
                            $customer_update = Customer::where('id', $sales_order->customer_id)->update([
                                'point' => $calculation_point_last,
                                'updated_by' => Auth::user()->id,
                            ]);

                            $customer_new_update = Customer::where('id', $request->customer)->update([
                                'point' => $calculation_point_result,
                                'updated_by' => Auth::user()->id,
                            ]);

                            if ($customer_update && $customer_new_update) {
                                /**
                                 * Destroy Last Sales Order Item
                                 */
                                $sales_order_item_destroy = SalesOrderItem::where('sales_order_id', $id)->update([
                                    'deleted_by' => Auth::user()->id,
                                    'deleted_at' => date('Y-m-d H:i:s'),
                                ]);

                                if ($sales_order_item_destroy) {
                                    /**
                                     * Each Sales Order Item Product Request
                                     */
                                    foreach ($request->sales_order_item as $sales_order_item_request_product) {
                                        /**
                                         * Each Sales Order Item Product Size Request
                                         */
                                        foreach ($sales_order_item_request_product['product_size'] as $product_size_id => $sales_order_item_request_product_size) {
                                            /**
                                             * Create Sales Order Item Record
                                             */
                                            $sales_order_item = SalesOrderItem::lockforUpdate()->create([
                                                'product_size_id' => $product_size_id,
                                                'sales_order_id' => $sales_order->id,
                                                'qty' => $sales_order_item_request_product_size['qty'],
                                                'capital_price' => $sales_order_item_request_product_size['capital_price'],
                                                'sell_price' => $sales_order_item_request_product_size['sell_price'],
                                                'discount_price' => $sales_order_item_request_product_size['discount_price'],
                                                'total_sell_price' => $sales_order_item_request_product_size['total_sell_price'],
                                                'total_profit_price' => $sales_order_item_request_product_size['total_profit_price'],
                                                'created_by' => Auth::user()->id,
                                                'updated_by' => Auth::user()->id,
                                            ]);

                                            /**
                                             * Validation Create Sales Order Item Record
                                             */
                                            if (!$sales_order_item) {
                                                /**
                                                 * Failed Store Record
                                                 */
                                                DB::rollBack();
                                                return redirect()
                                                    ->back()
                                                    ->with(['failed' => 'Failed Store Sales Order Item'])
                                                    ->withInput();
                                            }
                                        }
                                    }

                                    DB::commit();
                                    return redirect()
                                        ->route('sales-order.show', ['id' => $sales_order->id])
                                        ->with(['success' => 'Successfully Update Sales Order']);
                                } else {
                                    /**
                                     * Failed Update Destroy
                                     */
                                    DB::rollBack();
                                    return redirect()
                                        ->back()
                                        ->with(['failed' => 'Failed Destroy Sales Order Item'])
                                        ->withInput();
                                }
                            } else {
                                /**
                                 * Failed Update Record
                                 */
                                DB::rollBack();
                                return redirect()
                                    ->back()
                                    ->with(['failed' => 'Failed Update Customer'])
                                    ->withInput();
                            }
                        }
                    } else {
                        /**
                         * Validation Customer Requested and Last Record Haven't Customer Record
                         */
                        if (!is_null($request->customer) && is_null($sales_order->customer_id)) {
                            /**
                             * Customer Record
                             */
                            $customer = Customer::find($request->customer);

                            /**
                             * Calculation Point
                             */
                            $calculation_point = $customer->point + $this->generatePoint($request->grand_sell_price);

                            /**
                             * Update Customer Point Record
                             */
                            $customer_update = Customer::where('id', $request->customer)->update([
                                'point' => $calculation_point,
                                'updated_by' => Auth::user()->id,
                            ]);

                            if ($customer_update) {
                                /**
                                 * Destroy Last Sales Order Item
                                 */
                                $sales_order_item_destroy = SalesOrderItem::where('sales_order_id', $id)->update([
                                    'deleted_by' => Auth::user()->id,
                                    'deleted_at' => date('Y-m-d H:i:s'),
                                ]);

                                if ($sales_order_item_destroy) {
                                    /**
                                     * Each Sales Order Item Product Request
                                     */
                                    foreach ($request->sales_order_item as $sales_order_item_request_product) {
                                        /**
                                         * Each Sales Order Item Product Size Request
                                         */
                                        foreach ($sales_order_item_request_product['product_size'] as $product_size_id => $sales_order_item_request_product_size) {
                                            /**
                                             * Create Sales Order Item Record
                                             */
                                            $sales_order_item = SalesOrderItem::lockforUpdate()->create([
                                                'product_size_id' => $product_size_id,
                                                'sales_order_id' => $sales_order->id,
                                                'qty' => $sales_order_item_request_product_size['qty'],
                                                'capital_price' => $sales_order_item_request_product_size['capital_price'],
                                                'sell_price' => $sales_order_item_request_product_size['sell_price'],
                                                'discount_price' => $sales_order_item_request_product_size['discount_price'],
                                                'total_sell_price' => $sales_order_item_request_product_size['total_sell_price'],
                                                'total_profit_price' => $sales_order_item_request_product_size['total_profit_price'],
                                                'created_by' => Auth::user()->id,
                                                'updated_by' => Auth::user()->id,
                                            ]);

                                            /**
                                             * Validation Create Sales Order Item Record
                                             */
                                            if (!$sales_order_item) {
                                                /**
                                                 * Failed Store Record
                                                 */
                                                DB::rollBack();
                                                return redirect()
                                                    ->back()
                                                    ->with(['failed' => 'Failed Store Sales Order Item'])
                                                    ->withInput();
                                            }
                                        }
                                    }

                                    DB::commit();
                                    return redirect()
                                        ->route('sales-order.show', ['id' => $sales_order->id])
                                        ->with(['success' => 'Successfully Update Sales Order']);
                                } else {
                                    /**
                                     * Failed Update Destroy
                                     */
                                    DB::rollBack();
                                    return redirect()
                                        ->back()
                                        ->with(['failed' => 'Failed Destroy Sales Order Item'])
                                        ->withInput();
                                }
                            } else {
                                /**
                                 * Failed Update Record
                                 */
                                DB::rollBack();
                                return redirect()
                                    ->back()
                                    ->with(['failed' => 'Failed Update Customer'])
                                    ->withInput();
                            }
                        } else {
                            /**
                             * Customer Record
                             */
                            $customer = Customer::find($sales_order->customer_id);

                            /**
                             * Calculation Point
                             */
                            $calculation_point = $customer->point - $this->generatePoint($sales_order->grand_sell_price);

                            /**
                             * Check Calculation Less Than 0
                             */
                            if ($calculation_point < 0) {
                                $calculation_point = 0;
                            }

                            /**
                             * Update Customer Point Record
                             */
                            $customer_update = Customer::where('id', $sales_order->customer_id)->update([
                                'point' => $calculation_point,
                                'updated_by' => Auth::user()->id,
                            ]);

                            if ($customer_update) {
                                /**
                                 * Destroy Last Sales Order Item
                                 */
                                $sales_order_item_destroy = SalesOrderItem::where('sales_order_id', $id)->update([
                                    'deleted_by' => Auth::user()->id,
                                    'deleted_at' => date('Y-m-d H:i:s'),
                                ]);

                                if ($sales_order_item_destroy) {
                                    /**
                                     * Each Sales Order Item Product Request
                                     */
                                    foreach ($request->sales_order_item as $sales_order_item_request_product) {
                                        /**
                                         * Each Sales Order Item Product Size Request
                                         */
                                        foreach ($sales_order_item_request_product['product_size'] as $product_size_id => $sales_order_item_request_product_size) {
                                            /**
                                             * Create Sales Order Item Record
                                             */
                                            $sales_order_item = SalesOrderItem::lockforUpdate()->create([
                                                'product_size_id' => $product_size_id,
                                                'sales_order_id' => $sales_order->id,
                                                'qty' => $sales_order_item_request_product_size['qty'],
                                                'capital_price' => $sales_order_item_request_product_size['capital_price'],
                                                'sell_price' => $sales_order_item_request_product_size['sell_price'],
                                                'discount_price' => $sales_order_item_request_product_size['discount_price'],
                                                'total_sell_price' => $sales_order_item_request_product_size['total_sell_price'],
                                                'total_profit_price' => $sales_order_item_request_product_size['total_profit_price'],
                                                'created_by' => Auth::user()->id,
                                                'updated_by' => Auth::user()->id,
                                            ]);

                                            /**
                                             * Validation Create Sales Order Item Record
                                             */
                                            if (!$sales_order_item) {
                                                /**
                                                 * Failed Store Record
                                                 */
                                                DB::rollBack();
                                                return redirect()
                                                    ->back()
                                                    ->with(['failed' => 'Failed Store Sales Order Item'])
                                                    ->withInput();
                                            }
                                        }
                                    }

                                    DB::commit();
                                    return redirect()
                                        ->route('sales-order.show', ['id' => $sales_order->id])
                                        ->with(['success' => 'Successfully Update Sales Order']);
                                } else {
                                    /**
                                     * Failed Update Destroy
                                     */
                                    DB::rollBack();
                                    return redirect()
                                        ->back()
                                        ->with(['failed' => 'Failed Destroy Sales Order Item'])
                                        ->withInput();
                                }
                            } else {
                                /**
                                 * Failed Update Record
                                 */
                                DB::rollBack();
                                return redirect()
                                    ->back()
                                    ->with(['failed' => 'Failed Update Customer'])
                                    ->withInput();
                            }
                        }
                    }
                } else {
                    /**
                     * Destroy Last Sales Order Item
                     */
                    $sales_order_item_destroy = SalesOrderItem::where('sales_order_id', $id)->update([
                        'deleted_by' => Auth::user()->id,
                        'deleted_at' => date('Y-m-d H:i:s'),
                    ]);

                    if ($sales_order_item_destroy) {
                        /**
                         * Each Sales Order Item Product Request
                         */
                        foreach ($request->sales_order_item as $sales_order_item_request_product) {
                            /**
                             * Each Sales Order Item Product Size Request
                             */
                            foreach ($sales_order_item_request_product['product_size'] as $product_size_id => $sales_order_item_request_product_size) {
                                /**
                                 * Create Sales Order Item Record
                                 */
                                $sales_order_item = SalesOrderItem::lockforUpdate()->create([
                                    'product_size_id' => $product_size_id,
                                    'sales_order_id' => $sales_order->id,
                                    'qty' => $sales_order_item_request_product_size['qty'],
                                    'capital_price' => $sales_order_item_request_product_size['capital_price'],
                                    'sell_price' => $sales_order_item_request_product_size['sell_price'],
                                    'discount_price' => $sales_order_item_request_product_size['discount_price'],
                                    'total_sell_price' => $sales_order_item_request_product_size['total_sell_price'],
                                    'total_profit_price' => $sales_order_item_request_product_size['total_profit_price'],
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);

                                /**
                                 * Validation Create Sales Order Item Record
                                 */
                                if (!$sales_order_item) {
                                    /**
                                     * Failed Store Record
                                     */
                                    DB::rollBack();
                                    return redirect()
                                        ->back()
                                        ->with(['failed' => 'Failed Store Sales Order Item'])
                                        ->withInput();
                                }
                            }
                        }

                        DB::commit();
                        return redirect()
                            ->route('sales-order.show', ['id' => $sales_order->id])
                            ->with(['success' => 'Successfully Update Sales Order']);
                    } else {
                        /**
                         * Failed Update Destroy
                         */
                        DB::rollBack();
                        return redirect()
                            ->back()
                            ->with(['failed' => 'Failed Destroy Sales Order Item'])
                            ->withInput();
                    }
                }
            } else {
                /**
                 * Failed Update Record
                 */
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with(['failed' => 'Failed Update Sales Order'])
                    ->withInput();
            }
        } catch (Exception $e) {
            return redirect()
                ->route('sales-order.create')
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
             * Update Sales Order Record
             */
            $sales_order_destroy = SalesOrder::where('id', $id)->update([
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
                $sales_order_item_destroy = SalesOrderItem::where('sales_order_id', $id)->update([
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

    /**
     * Calculation Point Formula
     */
    private function generatePoint($price)
    {
        $point_result = 0;
        if (intval($price) >= 100000) {
            $point_result = round(intval($price) / 100000);
        } else {
            $point_result = 0;
        }
        return $point_result;
    }
}
