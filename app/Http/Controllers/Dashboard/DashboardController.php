<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product\CategoryProduct;
use App\Models\Product\Product;
use App\Models\Product\StockInOut;
use App\Models\SalesOrder\SalesOrder;
use App\Models\SalesOrder\SalesOrderItem;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function salesOrder(Request $request)
    {
        try {
            /**
             * Validation request
             */
            if (!is_null($request->year) && !is_null($request->month)) {
                /**
                 * Variable Widget
                 */
                $total_income = SalesOrder::whereNull('deleted_by')
                    ->whereNull('deleted_at')
                    ->whereMonth('created_at', $request->month)
                    ->whereYear('created_at', $request->year)
                    ->sum('grand_sell_price');

                $total_profit = SalesOrder::whereNull('deleted_by')
                    ->whereNull('deleted_at')
                    ->whereMonth('created_at', $request->month)
                    ->whereYear('created_at', $request->year)
                    ->sum('grand_profit_price');

                /**
                 * Convert string
                 */
                $data['total_income'] = 'Rp. ' . number_format($total_income, 0, ',', '.') . ',-';

                /**
                 * Convert string
                 */
                $data['total_profit'] = 'Rp. ' . number_format($total_profit, 0, ',', '.') . ',-';

                $data['total_sales_order'] = count(
                    SalesOrder::whereNull('deleted_by')
                        ->whereNull('deleted_at')
                        ->whereMonth('created_at', $request->month)
                        ->whereYear('created_at', $request->year)
                        ->get(),
                );

                $data['total_product_sold'] = SalesOrderItem::whereHas('salesOrder', function ($query) use ($request) {
                    return $query
                        ->whereNull('deleted_by')
                        ->whereNull('deleted_at')
                        ->whereMonth('created_at', $request->month)
                        ->whereYear('created_at', $request->year);
                })->sum('qty');

                $number_of_day = cal_days_in_month(CAL_GREGORIAN, $request->month, $request->year);
                if ($request->month == date('m')) {
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
                                ->whereDay('created_at', $day)
                                ->whereMonth('created_at', $request->month)
                                ->whereYear('created_at', $request->year)
                                ->where('type', 0)
                                ->get(),
                        );

                        $stock_out_count_per_day = count(
                            StockInOut::whereNull('deleted_by')
                                ->whereNull('deleted_at')
                                ->whereDay('created_at', $day)
                                ->whereMonth('created_at', $request->month)
                                ->whereYear('created_at', $request->year)
                                ->where('type', 1)
                                ->get(),
                        );

                        $stock_in_qty_per_day = StockInOut::whereNull('deleted_by')
                            ->whereNull('deleted_at')
                            ->whereDay('created_at', $day)
                            ->whereMonth('created_at', $request->month)
                            ->whereYear('created_at', $request->year)
                            ->where('type', 0)
                            ->sum('qty');

                        $stock_out_qty_per_day = StockInOut::whereNull('deleted_by')
                            ->whereNull('deleted_at')
                            ->whereDay('created_at', $day)
                            ->whereMonth('created_at', $request->month)
                            ->whereYear('created_at', $request->year)
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
                                ->whereDay('created_at', $day)
                                ->whereMonth('created_at', $request->month)
                                ->whereYear('created_at', $request->year)
                                ->where('type', 0)
                                ->get(),
                        );

                        $stock_out_count_per_day = count(
                            StockInOut::whereNull('deleted_by')
                                ->whereNull('deleted_at')
                                ->whereDay('created_at', $day)
                                ->whereMonth('created_at', $request->month)
                                ->whereYear('created_at', $request->year)
                                ->where('type', 1)
                                ->get(),
                        );

                        $stock_in_qty_per_day = StockInOut::whereNull('deleted_by')
                            ->whereNull('deleted_at')
                            ->whereDay('created_at', $day)
                            ->whereMonth('created_at', $request->month)
                            ->whereYear('created_at', $request->year)
                            ->where('type', 0)
                            ->sum('qty');

                        $stock_out_qty_per_day = StockInOut::whereNull('deleted_by')
                            ->whereNull('deleted_at')
                            ->whereDay('created_at', $day)
                            ->whereMonth('created_at', $request->month)
                            ->whereYear('created_at', $request->year)
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
