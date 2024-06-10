<?php

namespace App\Http\Controllers\Dashboard;

use App\Exports\ChartofAccount\ChartofAccountExport;
use App\Exports\SalesOrder\SalesOrderExport;
use App\Exports\Stock\StockExport;
use App\Http\Controllers\Controller;
use App\Models\ChartofAccount\ChartofAccount;
use App\Models\Product\CategoryProduct;
use App\Models\Product\Product;
use App\Models\Product\StockInOut;
use App\Models\SalesOrder\SalesOrder;
use App\Models\SalesOrder\SalesOrderItem;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    /**
     * Display a dashboard of the resource.
     */
    public function index()
    {
        for ($month = 1; $month <= date('m'); $month++) {
            $dashboard['months'][$month] = date('F', mktime(0, 0, 0, $month, 10));
        }

        $dashboard['years'] = SalesOrder::select(DB::raw('YEAR(created_at) as year'))->whereNull('deleted_at')->groupBy(DB::raw('YEAR(created_at)'))->orderBy(DB::raw('YEAR(created_at)'), 'ASC')->get()->toArray();

        return view('dashboard.index', compact('dashboard'));
    }

    /**
     * Sales Order Statistic Report
     */
    public function salesOrder(Request $request)
    {
        try {
            /**
             * Validation request
             */
            if (!is_null($request->year) && !is_null($request->month)) {
                /**
                 * Total income by request month and year
                 */
                $total_income = SalesOrder::whereNull('deleted_by')
                    ->whereNull('deleted_at')
                    ->whereMonth('created_at', $request->month)
                    ->whereYear('created_at', $request->year)
                    ->sum('grand_sell_price');

                /**
                 * Total profit by request month and year
                 */
                $total_profit = SalesOrder::whereNull('deleted_by')
                    ->whereNull('deleted_at')
                    ->whereMonth('created_at', $request->month)
                    ->whereYear('created_at', $request->year)
                    ->sum('grand_profit_price');

                /**
                 * Convert string result
                 */
                $data['total_income'] = 'Rp. ' . number_format($total_income, 0, ',', '.') . ',-';

                /**
                 * Convert string result
                 */
                $data['total_profit'] = 'Rp. ' . number_format($total_profit, 0, ',', '.') . ',-';

                /**
                 * Total sales order by request month and year
                 */
                $data['total_sales_order'] = count(
                    SalesOrder::whereNull('deleted_by')
                        ->whereNull('deleted_at')
                        ->whereMonth('created_at', $request->month)
                        ->whereYear('created_at', $request->year)
                        ->get(),
                );

                /**
                 * Total product sold by request month and year
                 */
                $data['total_product_sold'] = SalesOrderItem::whereHas('salesOrder', function ($query) use ($request) {
                    return $query
                        ->whereNull('deleted_by')
                        ->whereNull('deleted_at')
                        ->whereMonth('created_at', $request->month)
                        ->whereYear('created_at', $request->year);
                })->sum('qty');

                /**
                 * Total number of day by month and year
                 */
                $number_of_day = cal_days_in_month(CAL_GREGORIAN, $request->month, $request->year);

                /**
                 * Validation month requested as current month
                 */
                if ($request->month == date('m')) {
                    /**
                     * Each day until number of current day
                     */
                    for ($day = 1; $day <= date('d'); $day++) {
                        $count_per_day = count(
                            SalesOrder::whereNull('deleted_by')
                                ->whereNull('deleted_at')
                                ->whereDay('created_at', $day)
                                ->whereMonth('created_at', $request->month)
                                ->whereYear('created_at', $request->year)
                                ->get(),
                        );

                        $offline_count_per_day = count(
                            SalesOrder::whereNull('deleted_by')
                                ->whereNull('deleted_at')
                                ->whereDay('created_at', $day)
                                ->whereMonth('created_at', $request->month)
                                ->whereYear('created_at', $request->year)
                                ->where('type', 0)
                                ->get(),
                        );

                        $online_count_per_day = count(
                            SalesOrder::whereNull('deleted_by')
                                ->whereNull('deleted_at')
                                ->whereDay('created_at', $day)
                                ->whereMonth('created_at', $request->month)
                                ->whereYear('created_at', $request->year)
                                ->where('type', 1)
                                ->get(),
                        );

                        $data['days'][] = $day;
                        $data['sales_data'][] = $count_per_day;
                        $data['online_data'][] = $online_count_per_day;
                        $data['offline_data'][] = $offline_count_per_day;
                    }
                } else {
                    for ($day = 1; $day <= $number_of_day; $day++) {
                        $count_per_day = count(
                            SalesOrder::whereNull('deleted_by')
                                ->whereNull('deleted_at')
                                ->whereDay('created_at', $day)
                                ->whereMonth('created_at', $request->month)
                                ->whereYear('created_at', $request->year)
                                ->get(),
                        );

                        $offline_count_per_day = count(
                            SalesOrder::whereNull('deleted_by')
                                ->whereNull('deleted_at')
                                ->whereDay('created_at', $day)
                                ->whereMonth('created_at', $request->month)
                                ->whereYear('created_at', $request->year)
                                ->where('type', 0)
                                ->get(),
                        );

                        $online_count_per_day = count(
                            SalesOrder::whereNull('deleted_by')
                                ->whereNull('deleted_at')
                                ->whereDay('created_at', $day)
                                ->whereMonth('created_at', $request->month)
                                ->whereYear('created_at', $request->year)
                                ->where('type', 0)
                                ->get(),
                        );

                        $data['days'][] = $day;
                        $data['sales_data'][] = $count_per_day;
                        $data['online_data'][] = $online_count_per_day;
                        $data['offline_data'][] = $offline_count_per_day;
                    }
                }

                return response()->json($data, 200);
            } else {
                return response()->json(['message' => 'Invalid Request'], 400);
            }
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Show datatable of resource.
     */
    public function salesOrderDataTable(Request $request)
    {
        /**
         * Validation request
         */
        if (!is_null($request->year) && !is_null($request->month)) {
            /**
             * Get All Sales Order
             */
            $sales_order = SalesOrder::whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->whereMonth('created_at', $request->month)
                ->whereYear('created_at', $request->year)
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
                    return date('d M Y H:i:s', strtotime($data->created_at));
                })
                ->addColumn('total_sell_price', function ($data) {
                    return '<div align="right"> Rp. ' . number_format($data->total_sell_price, 0, ',', '.') . ',-' . '</div>';
                })
                ->addColumn('total_capital_price', function ($data) {
                    return '<div align="right"> Rp. ' . number_format($data->total_capital_price, 0, ',', '.') . ',-' . '</div>';
                })
                ->addColumn('discount_price', function ($data) {
                    return '<div align="right"> Rp. ' . number_format($data->discount_price, 0, ',', '.') . ',-' . '</div>';
                })
                ->addColumn('grand_profit_price', function ($data) {
                    return '<div align="right"> Rp. ' . number_format($data->grand_profit_price, 0, ',', '.') . ',-' . '</div>';
                })
                ->addColumn('grand_sell_price', function ($data) {
                    return '<div align="right"> Rp. ' . number_format($data->grand_sell_price, 0, ',', '.') . ',-' . '</div>';
                })
                ->only(['invoice_number', 'created_at', 'total_sell_price', 'total_capital_price', 'discount_price', 'grand_profit_price', 'grand_sell_price'])
                ->rawColumns(['total_sell_price', 'total_capital_price', 'discount_price', 'grand_profit_price', 'grand_sell_price'])
                ->make(true);

            return $dataTable;
        } else {
            return response()->json(['message' => 'Invalid Request'], 400);
        }
    }

    /**
     * Export Stock.
     */
    public function salesOrderExport(Request $request)
    {
        /**
         * Validation request
         */
        if (!is_null($request->year) && !is_null($request->month)) {
            /**
             * Get All Sales Order
             */
            $sales_order = SalesOrder::with(['customer', 'paymentMethod', 'salesOrderItem.productSize.product'])
                ->whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->whereMonth('created_at', $request->month)
                ->whereYear('created_at', $request->year)
                ->orderBy('created_at', 'desc')
                ->get()
                ->toArray();

            $data['data'] = $sales_order;
            $data['month'] = date('F', mktime(0, 0, 0, $request->month, 10));
            $data['year'] = $request->year;

            return Excel::download(new SalesOrderExport($data), 'export.xlsx');
        } else {
            return response()->json(['message' => 'Invalid Request'], 400);
        }
    }

    public function stock(Request $request)
    {
        try {
            /**
             * Validation request
             */
            if (!is_null($request->year) && !is_null($request->month)) {
                /**
                 * Variable Widget
                 */
                $data['total_stock_in'] = count(
                    StockInOut::whereNull('deleted_by')
                        ->whereNull('deleted_at')
                        ->whereMonth('date', $request->month)
                        ->whereYear('date', $request->year)
                        ->where('type', 0)
                        ->get(),
                );

                $data['total_stock_out'] = count(
                    StockInOut::whereNull('deleted_by')
                        ->whereNull('deleted_at')
                        ->whereMonth('date', $request->month)
                        ->whereYear('date', $request->year)
                        ->where('type', 1)
                        ->get(),
                );

                $data['total_qty_stock_in'] = StockInOut::whereNull('deleted_by')
                    ->whereNull('deleted_at')
                    ->whereMonth('date', $request->month)
                    ->whereYear('date', $request->year)
                    ->where('type', 0)
                    ->sum('qty');

                $data['total_qty_stock_out'] = StockInOut::whereNull('deleted_by')
                    ->whereNull('deleted_at')
                    ->whereMonth('date', $request->month)
                    ->whereYear('date', $request->year)
                    ->where('type', 1)
                    ->sum('qty');

                $number_of_day = cal_days_in_month(CAL_GREGORIAN, $request->month, $request->year);
                if ($request->month == date('m')) {
                    for ($day = 1; $day <= date('d'); $day++) {
                        $stock_in_count_per_day = count(
                            StockInOut::whereNull('deleted_by')
                                ->whereNull('deleted_at')
                                ->whereDay('date', $day)
                                ->whereMonth('date', $request->month)
                                ->whereYear('date', $request->year)
                                ->where('type', 0)
                                ->get(),
                        );

                        $stock_out_count_per_day = count(
                            StockInOut::whereNull('deleted_by')
                                ->whereNull('deleted_at')
                                ->whereDay('date', $day)
                                ->whereMonth('date', $request->month)
                                ->whereYear('date', $request->year)
                                ->where('type', 1)
                                ->get(),
                        );

                        $stock_in_qty_per_day = StockInOut::whereNull('deleted_by')
                            ->whereNull('deleted_at')
                            ->whereDay('date', $day)
                            ->whereMonth('date', $request->month)
                            ->whereYear('date', $request->year)
                            ->where('type', 0)
                            ->sum('qty');

                        $stock_out_qty_per_day = StockInOut::whereNull('deleted_by')
                            ->whereNull('deleted_at')
                            ->whereDay('date', $day)
                            ->whereMonth('date', $request->month)
                            ->whereYear('date', $request->year)
                            ->where('type', 1)
                            ->sum('qty');

                        $data['days'][] = $day;
                        $data['stock_in_data'][] = $stock_in_count_per_day;
                        $data['stock_out_data'][] = $stock_out_count_per_day;
                        $data['stock_in_qty_data'][] = $stock_in_qty_per_day;
                        $data['stock_out_qty_data'][] = $stock_out_qty_per_day;
                    }
                } else {
                    for ($day = 1; $day <= $number_of_day; $day++) {
                        $stock_in_count_per_day = count(
                            StockInOut::whereNull('deleted_by')
                                ->whereNull('deleted_at')
                                ->whereDay('date', $day)
                                ->whereMonth('date', $request->month)
                                ->whereYear('date', $request->year)
                                ->where('type', 0)
                                ->get(),
                        );

                        $stock_out_count_per_day = count(
                            StockInOut::whereNull('deleted_by')
                                ->whereNull('deleted_at')
                                ->whereDay('date', $day)
                                ->whereMonth('date', $request->month)
                                ->whereYear('date', $request->year)
                                ->where('type', 1)
                                ->get(),
                        );

                        $stock_in_qty_per_day = StockInOut::whereNull('deleted_by')
                            ->whereNull('deleted_at')
                            ->whereDay('date', $day)
                            ->whereMonth('date', $request->month)
                            ->whereYear('date', $request->year)
                            ->where('type', 0)
                            ->sum('qty');

                        $stock_out_qty_per_day = StockInOut::whereNull('deleted_by')
                            ->whereNull('deleted_at')
                            ->whereDay('date', $day)
                            ->whereMonth('date', $request->month)
                            ->whereYear('date', $request->year)
                            ->where('type', 1)
                            ->sum('qty');

                        $data['days'][] = $day;
                        $data['stock_in_data'][] = $stock_in_count_per_day;
                        $data['stock_out_data'][] = $stock_out_count_per_day;
                        $data['stock_in_qty_data'][] = $stock_in_qty_per_day;
                        $data['stock_out_qty_data'][] = $stock_out_qty_per_day;
                    }
                }

                return response()->json($data, 200);
            } else {
                return response()->json(['message' => 'Invalid Request'], 400);
            }
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Show datatable of resource.
     */
    public function stockDataTable(Request $request)
    {
        /**
         * Validation request
         */
        if (!is_null($request->year) && !is_null($request->month)) {
            /**
             * Get All Stock
             */
            $stock = StockInOut::with(['productSize.product'])
                ->whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->whereMonth('date', $request->month)
                ->whereYear('date', $request->year)
                ->get();

            /**
             * Datatable Configuration
             */
            $dataTable = DataTables::of($stock)
                ->addIndexColumn()
                ->addColumn('product', function ($data) {
                    /**
                     * Return Relation Product Size and Product
                     */
                    return $data->productSize->product->name . ' - ' . $data->productSize->size;
                })
                ->addColumn('date', function ($data) {
                    /**
                     * Return Format Date
                     */
                    return date('d M Y', strtotime($data->date));
                })
                ->addColumn('qty', function ($data) {
                    if ($data->type == 0) {
                        return '<span class="text-success">+ ' . $data->qty . ' Pcs</span>';
                    } else {
                        return '<span class="text-danger">- ' . $data->qty . ' Pcs</span>';
                    }
                })
                ->only(['product', 'date', 'qty', 'description'])
                ->rawColumns(['qty'])
                ->make(true);

            return $dataTable;
        } else {
            return response()->json(['message' => 'Invalid Request'], 400);
        }
    }

    /**
     * Export Stock.
     */
    public function stockExport(Request $request)
    {
        /**
         * Validation request
         */
        if (!is_null($request->year) && !is_null($request->month)) {
            /**
             * Get All Stock
             */
            $stock = StockInOut::with(['productSize.product'])
                ->whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->whereMonth('date', $request->month)
                ->whereYear('date', $request->year)
                ->orderBy('date', 'desc')
                ->get()
                ->toArray();

            $data['data'] = $stock;
            $data['month'] = date('F', mktime(0, 0, 0, $request->month, 10));
            $data['year'] = $request->year;

            return Excel::download(new StockExport($data), 'export.xlsx');
        } else {
            return response()->json(['message' => 'Invalid Request'], 400);
        }
    }

    /**
     * Show datatable of resource.
     */
    public function coaDataTable(Request $request)
    {
        /**
         * Validation request
         */
        if (!is_null($request->year) && !is_null($request->month)) {
            /**
             * Get All Chart of Account
             */
            $chart_of_account = ChartofAccount::with(['accountNumber', 'createdBy'])
                ->whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->whereMonth('date', $request->month)
                ->whereYear('date', $request->year)
                ->get();

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
                ->only(['account_number', 'date', 'type', 'name', 'balance'])
                ->rawColumns(['balance'])
                ->make(true);

            return $dataTable;
        } else {
            return response()->json(['message' => 'Invalid Request'], 400);
        }
    }

    /**
     * Export Chart of Account.
     */
    public function coaExport(Request $request)
    {
        /**
         * Validation request
         */
        if (!is_null($request->year) && !is_null($request->month)) {
            /**
             * Get All Chart of Account
             */
            $chart_of_account = ChartofAccount::with(['accountNumber', 'createdBy'])
                ->whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->whereMonth('date', $request->month)
                ->whereYear('date', $request->year)
                ->orderBy('date', 'desc')
                ->get()
                ->toArray();

            $data['data'] = $chart_of_account;
            $data['month'] = date('F', mktime(0, 0, 0, $request->month, 10));
            $data['year'] = $request->year;

            return Excel::download(new ChartofAccountExport($data), 'export.xlsx');
        } else {
            return response()->json(['message' => 'Invalid Request'], 400);
        }
    }

    public function product()
    {
        try {
            /**
             * Variable Widget
             */
            $data['total_product'] = count(Product::whereNull('deleted_by')->whereNull('deleted_at')->get());
            $data['total_active_product'] = count(Product::whereNull('deleted_by')->whereNull('deleted_at')->where('status', 1)->get());
            $data['total_inactive_product'] = count(Product::whereNull('deleted_by')->whereNull('deleted_at')->where('status', 0)->get());
            $data['total_category_product'] = count(CategoryProduct::whereNull('deleted_by')->whereNull('deleted_at')->get());

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
