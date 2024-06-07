@extends('layouts.section')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin transparent">
                <div class="card px-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between my-3">
                            <div class="p-0">
                                <h4 class="py-3"><b>Sales Order Statistic</b></h4>
                            </div>
                            <div class="p-0">
                                <div class="row ml-auto">
                                    <div class="col-md-4 pb-3 pl-0">
                                        <select class="form-control" id="sales_order_month">
                                            @foreach ($dashboard['months'] as $month_num => $month)
                                                <option value="{{ $month_num }}"
                                                    @if ($month_num == date('m')) selected @endif>{{ $month }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 pb-3 pl-0">
                                        <select class="form-control" id="sales_order_year">
                                            @foreach ($dashboard['years'] as $year)
                                                <option value="{{ $year['year'] }}"
                                                    @if ($year['year'] == date('Y')) selected @endif>{{ $year['year'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 my-auto pb-3 pl-0">
                                        <button class="btn btn-primary" onclick="dashboardSalesOrder()" title="Filter">
                                            Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3 stretch-card transparent">
                                <div class="card card-tale">
                                    <div class="card-body">
                                        <h5 class="mb-4 text-bold"><b>Total Income</b></h5>
                                        <span class="text-right">
                                            <h5 class="mb-2" id="total_income"></h5>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3 stretch-card transparent">
                                <div class="card card-dark-blue">
                                    <div class="card-body">
                                        <h5 class="mb-4 text-bold"><b>Total Profit</b></h5>
                                        <span class="text-right">
                                            <h5 class="mb-2" id="total_profit"></h5>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3 stretch-card transparent">
                                <div class="card card-light-blue">
                                    <div class="card-body">
                                        <h5 class="mb-4 text-bold"><b>Total Sales Order</b></h5>
                                        <span class="text-right">
                                            <h5 class="mb-2" id="total_sales_order"></h5>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3 stretch-card transparent">
                                <div class="card card-light-danger">
                                    <div class="card-body">
                                        <h5 class="mb-4 text-bold"><b>Total Products Sold</b></h5>
                                        <span class="text-right">
                                            <h5 class="mb-2" id="total_product_sold"></h5>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row py-3 pb-3">
                            <div class="col-md-6">
                                <h4 class="mt-4 mb-4"><b>Sales Order Statistic</b></h4>
                                <canvas id="sales-chart"></canvas>
                            </div>
                            <div class="col-md-6">
                                <h4 class="mt-4 mb-4"><b>Purchase Type Statistic</b></h4>
                                <canvas id="purchase-type-chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card px-3 mt-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between my-3">
                            <div class="p-0">
                                <h4 class="py-3"><b>Stock In & Out Summary</b></h4>
                            </div>
                            <div class="p-0">
                                <div class="input-group w-100 mx-auto d-flex">
                                    <div class="p-0">
                                        <div class="row ml-auto">
                                            <div class="col-md-4 pb-3 pl-0">
                                                <select class="form-control" id="stock_month">
                                                    @foreach ($dashboard['months'] as $month_num => $month)
                                                        <option value="{{ $month_num }}"
                                                            @if ($month_num == date('m')) selected @endif>{{ $month }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 pb-3 pl-0">
                                                <select class="form-control" id="stock_year">
                                                    @foreach ($dashboard['years'] as $year)
                                                        <option value="{{ $year['year'] }}"
                                                            @if ($year['year'] == date('Y')) selected @endif>{{ $year['year'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 my-auto pb-3 pl-0">
                                                <button class="btn btn-primary" onclick="dashboardStock()" title="Filter">
                                                    Filter
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 stretch-card transparent">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5 class="mb-4 text-bold"><b>Total Stock In</b></h5>
                                        <span class="text-right">
                                            <h5 class="mb-2" id="total_stock_in"></h5>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 stretch-card transparent">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <h5 class="mb-4 text-bold"><b>Total Stock Out</b></h5>
                                        <span class="text-right">
                                            <h5 class="mb-2" id="total_stock_out"></h5>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 stretch-card transparent">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5 class="mb-4 text-bold"><b>Total Qty Stock In</b></h5>
                                        <span class="text-right">
                                            <h5 class="mb-2" id="total_qty_stock_in"></h5>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 stretch-card transparent">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <h5 class="mb-4 text-bold"><b>Total Qty Stock Out</b></h5>
                                        <span class="text-right">
                                            <h5 class="mb-2" id="total_qty_stock_out"></h5>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row p-3">
                            <div class="col-md-6">
                                <h5 class="my-4"><b>Stock In & Out Statistic</b></h5>
                                <canvas id="stock-chart"></canvas>
                            </div>
                            <div class="col-md-6">
                                <h5 class="my-4"><b>Stock In & Out Qty Statistic</b></h5>
                                <canvas id="stock-qty-chart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-12 p-3 mt-3 border-top border-top-1">
                            <div class="d-flex justify-content-between my-3">
                                <div class="p-0 my-auto">
                                    <h5><b>Stock In & Out Report</b></h5>
                                </div>
                                <div class="p-0">
                                    <div class="input-group w-100 mx-auto d-flex">
                                        <button class="btn btn-success" onclick="exportStock()" title="Export Report">
                                            Export Report
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered datatable" id="dt-stock">
                                    <thead>
                                        <tr>
                                            <th>
                                                #
                                            </th>
                                            <th>
                                                Date
                                            </th>
                                            <th>
                                                Product
                                            </th>
                                            <th>
                                                Qty
                                            </th>
                                            <th>
                                                Description
                                            </th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card px-3 mt-3">
                    <div class="card-body">
                        <h4 class="py-3"><b>Product Summary</b></h4>
                        <div class="row">
                            <div class="col-md-3 stretch-card transparent">
                                <div class="card card-dark-blue">
                                    <div class="card-body">
                                        <h5 class="mb-4 text-bold"><b>Total Product</b></h5>
                                        <span class="text-right">
                                            <h5 class="mb-2" id="total_product"></h5>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 stretch-card transparent">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5 class="mb-4 text-bold"><b>Total Active Product</b></h5>
                                        <span class="text-right">
                                            <h5 class="mb-2" id="total_active_product"></h5>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 stretch-card transparent">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <h5 class="mb-4 text-bold"><b>Total Inactive Product</b></h5>
                                        <span class="text-right">
                                            <h5 class="mb-2" id="total_inactive_product"></h5>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 stretch-card transparent">
                                <div class="card card-light-blue">
                                    <div class="card-body">
                                        <h5 class="mb-4 text-bold"><b>Total Category Product</b></h5>
                                        <span class="text-right">
                                            <h5 class="mb-2" id="total_category_product"></h5>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="row p-3">
                            <div class="col-md-6">
                                <h4 class="mt-4 mb-4"><b>Statistic Purchase Type</b></h4>
                                <canvas id="order-chart"></canvas>
                            </div>
                            <div class="col-md-6">
                                <canvas id="sales-chart"></canvas>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('javascript-bottom')
        @include('javascript.dashboard.script')
        <script>
            dashboardSalesOrder();
            dashboardProduct();
            dashboardStock();
        </script>
    @endpush
@endsection
