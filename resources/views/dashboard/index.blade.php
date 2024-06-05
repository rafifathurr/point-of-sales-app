@extends('layouts.section')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin transparent">
                <div class="card px-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="p-0">
                                <h3 class="py-3"><b>Sales Order Statistic</b></h3>
                            </div>
                            <div class="p-0">
                                <div class="input-group w-100 mx-auto d-flex">
                                    <div class="p-0">
                                        <select class="form-control w-100" id="month">
                                            @foreach ($dashboard['months'] as $month_num => $month)
                                                <option value="{{ $month_num }}"
                                                    @if ($month_num == date('m')) selected @endif>{{ $month }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="px-3">
                                        <select class="form-control w-100" id="year">
                                            @foreach ($dashboard['years'] as $year)
                                                <option value="{{ $year['year'] }}"
                                                    @if ($year['year'] == date('Y')) selected @endif>{{ $year['year'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button class="btn btn-primary" onclick="dashboard()">
                                        Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 stretch-card transparent">
                                <div class="card card-tale">
                                    <div class="card-body">
                                        <h4 class="mb-4 text-bold"><b>Total Income</b></h4>
                                        <span class="text-right">
                                            <h4 class="mb-2" id="total_income"></h4>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 stretch-card transparent">
                                <div class="card card-dark-blue">
                                    <div class="card-body">
                                        <h4 class="mb-4 text-bold"><b>Total Profit</b></h4>
                                        <span class="text-right">
                                            <h4 class="mb-2" id="total_profit"></h4>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 stretch-card transparent">
                                <div class="card card-light-blue">
                                    <div class="card-body">
                                        <h4 class="mb-4 text-bold"><b>Total Sales Order</b></h4>
                                        <span class="text-right">
                                            <h4 class="mb-2" id="total_sales_order">
                                            </h4>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 stretch-card transparent">
                                <div class="card card-light-danger">
                                    <div class="card-body">
                                        <h4 class="mb-4 text-bold"><b>Total Products Sold</b></h4>
                                        <span class="text-right">
                                            <h4 class="mb-2" id="total_product_sold">
                                            </h4>
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
            dashboard();
        </script>
    @endpush
@endsection
