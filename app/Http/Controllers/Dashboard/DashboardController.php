<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder\SalesOrder;
use App\Models\SalesOrder\SalesOrderItem;

class DashboardController extends Controller
{
    /**
     * Display a dashboard of the resource.
     */
    public function dashboard()
    {
        $dashboard['total_income'] = SalesOrder::whereMonth('created_at', date('m'))->where('deleted_at', null)->whereYear('created_at', date('Y'))->sum('grand_sell_price');
        $dashboard['total_profit'] = SalesOrder::whereMonth('created_at', date('m'))->where('deleted_at', null)->whereYear('created_at', date('Y'))->sum('grand_profit_price');
        $dashboard['total_sales_order'] = count(SalesOrder::whereMonth('created_at', date('m'))->where('deleted_at', null)->whereYear('created_at', date('Y'))->get());
        $dashboard['total_product_sold'] = SalesOrderItem::whereHas('salesOrder', function ($query) {
            return $query->whereMonth('created_at', date('m'))->where('deleted_at', null)->whereYear('created_at', date('Y'));
        })->sum('qty');
        return view('dashboard', compact('dashboard'));
    }
}
