@extends('layouts.section')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body p-5">
                        <h4 class="card-title">Detail Sales Order #{{ $sales_order->id }}</h4>
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
                                {{ isset($sales_order->customer) ? $sales_order->customer->name . ' - ' . $sales_order->customer->point . ' Point' : '-' }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Purchase Type</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $sales_order->type == 0 ? 'Offline' : 'Online' }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Payment Method</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $sales_order->paymentMethod->name }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Updated By</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $sales_order->updatedBy->name }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Updated At</label>
                            <div class="col-sm-9 col-form-label">
                                {{ date('d M Y H:i:s', strtotime($sales_order->updated_at)) }}
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
                                        @if ($show_capital_price)
                                            <th>
                                                Total Capital Price
                                            </th>
                                            <th>
                                                Total Profit Price
                                            </th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody id="table_body">
                                    @foreach ($sales_order->salesOrderItem as $sales_order_item)
                                        <tr>
                                            <td>
                                                {{ $sales_order_item->productSize->product->name . ' - ' . $sales_order_item->productSize->size }}
                                            </td>
                                            <td>
                                                {{ $sales_order_item->qty }} Pcs
                                            </td>
                                            <td align="right">
                                                Rp. {{ number_format($sales_order_item->sell_price, 0, ',', '.') }} ,-
                                            </td>
                                            <td align="right">
                                                Rp. {{ number_format($sales_order_item->discount_price, 0, ',', '.') }} ,-
                                            </td>
                                            <td align="right">
                                                Rp. {{ number_format($sales_order_item->total_sell_price, 0, ',', '.') }}
                                                ,-
                                            </td>
                                            @if ($show_capital_price)
                                                <td align="right">
                                                    Rp.
                                                    {{ number_format(intval($sales_order_item->capital_price) * intval($sales_order_item->qty), 0, ',', '.') }}
                                                    ,-
                                                </td>
                                                <td align="right">
                                                    Rp.
                                                    {{ number_format($sales_order_item->total_profit_price, 0, ',', '.') }}
                                                    ,-
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" align="right">
                                            <b> Total</b>
                                        </td>
                                        <td align="right">
                                            Rp. {{ number_format($sales_order->grand_sell_price, 0, ',', '.') }}
                                            ,-
                                        </td>
                                        @if ($show_capital_price)
                                            <td align="right">
                                                Rp. {{ number_format($sales_order->total_capital_price, 0, ',', '.') }}
                                                ,-
                                            </td>
                                            <td align="right">
                                                Rp. {{ number_format($sales_order->grand_profit_price, 0, ',', '.') }}
                                                ,-
                                            </td>
                                        @endif
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="float-right mt-5">
                            <a href="{{ route('sales-order.index') }}" class="btn btn-sm btn-danger">
                                Back
                            </a>
                            <a href="{{ route('sales-order.export', ['id'=>$sales_order->id]) }}" class="btn btn-sm btn-success" target="_blank">
                                Print
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
