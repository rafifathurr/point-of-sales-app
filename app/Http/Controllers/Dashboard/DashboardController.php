<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder\SalesOrder;
use App\Models\SalesOrder\SalesOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a dashboard of the resource.
     */
    public function dashboard(Request $request)
    {
        if ($request->ajax()) {

            /**
             * Validation request
             */
            if (!is_null($request->year) && !is_null($request->month)) {
                /**
                 * Variable Widget
                 */
                $total_income = SalesOrder::whereMonth('created_at', $request->month)
                    ->where('deleted_at', null)
                    ->whereYear('created_at', $request->year)
                    ->sum('grand_sell_price');

                $total_profit = SalesOrder::whereMonth('created_at', $request->month)
                    ->where('deleted_at', null)
                    ->whereYear('created_at', $request->year)
                    ->sum('grand_profit_price');

                /**
                 * Convert string
                 */
                $dashboard['total_income'] = 'Rp. ' . number_format($total_income, 0, ',', '.') . ',-';

                /**
                 * Convert string
                 */
                $dashboard['total_profit'] = 'Rp. ' . number_format($total_profit, 0, ',', '.') . ',-';

                $dashboard['total_sales_order'] = count(
                    SalesOrder::whereMonth('created_at', $request->month)
                        ->where('deleted_at', null)
                        ->whereYear('created_at', $request->year)
                        ->get(),
                );

                $dashboard['total_product_sold'] = SalesOrderItem::whereHas('salesOrder', function ($query) use ($request) {
                    return $query
                        ->whereMonth('created_at', $request->month)
                        ->where('deleted_at', null)
                        ->whereYear('created_at', $request->year);
                })->sum('qty');

                return response()->json($dashboard, 200);
            } else {
                return response()->json(['message' => 'Invalid Request'], 400);
            }
        } else {
            for ($month = 1; $month <= date('m'); $month++) {
                $dashboard['months'][$month] = date('F', mktime(0, 0, 0, $month, 10));
            }

            $dashboard['years'] = SalesOrder::select(DB::raw('YEAR(created_at) as year'))->whereNull('deleted_at')->groupBy(DB::raw('YEAR(created_at)'))->orderBy(DB::raw('YEAR(created_at)'), 'ASC')->get()->toArray();

            return view('dashboard.index', compact('dashboard'));
        }
    }
}
