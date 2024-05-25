@extends('layouts.section')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body p-5">
                        <h4 class="card-title">Detail Sales Order #{{ $stock->id }}</h4>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Invoice Number</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $sales_order->invoice_number }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Date & Time</label>
                            <div class="col-sm-9 col-form-label">
                                {{ date('d M Y H:i:s', strtotime($sales_order->created_at)) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Customer</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $sales_order->customer->name ?? '-' }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Purchase Type</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $sales_order->type == 0 ? 'Offline' : 'Online' }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Updated By</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $stock->updatedBy->name }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Updated At</label>
                            <div class="col-sm-9 col-form-label">
                                {{ date('d M Y H:i:s', strtotime($stock->updated_at)) }}
                            </div>
                        </div>
                        <div class="table-responsive mt-5">
                            <table class="table table-bordered datatable" id="product_size">
                                <thead>
                                    <tr>
                                        <th width="15%">
                                            Product
                                        </th>
                                        <th>
                                            Qty
                                        </th>
                                        <th>
                                            Sell Price
                                        </th>
                                        <th>
                                            Discount Price
                                        </th>
                                        <th>
                                            Total Sell Price
                                        </th>
                                        <th>
                                            Total Capital Price
                                        </th>
                                        <th>
                                            Total Profit Price
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="table_body">
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                        <div class="float-right mt-5">
                            <a href="{{ route('sales-order.index') }}" class="btn btn-sm btn-danger">
                                Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('javascript-bottom')
        @include('javascript.sales_order.script')
    @endpush
@endsection
