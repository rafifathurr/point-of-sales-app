@extends('layouts.section')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body p-5">
                        <h4 class="card-title">Detail Product #{{ $product->id }}</h4>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Name</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $product->name }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Category Product</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $product->categoryProduct->name ?? '-' }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Supplier</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $product->supplier->name ?? '-' }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Status</label>
                            <div class="col-sm-9 col-form-label">
                                @if ($product->status == 1)
                                    <span class="badge badge-success pl-3 pr-3">Active</span>
                                @elseif($product->status == 0)
                                    <span class="badge badge-danger pl-3 pr-3">Inactive</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Picture</label>
                            <div class="col-sm-9 col-form-label">
                                <img width="50%" src="{{ asset($product->picture) }}" alt="" class="border border-1-default">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Description</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $product->description ?? '-' }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Updated By</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $product->updatedBy->name }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Updated At</label>
                            <div class="col-sm-9 col-form-label">
                                {{ date('d F Y H:i:s', strtotime($product->updated_at)) }}
                            </div>
                        </div>
                        <div class="table-responsive mt-5">
                            <table class="table table-bordered datatable" id="product_size">
                                <thead>
                                    <tr>
                                        <th width="15%">
                                            Size
                                        </th>
                                        <th>
                                            Stock
                                        </th>
                                        @if ($show_capital_price)
                                            <th>
                                                Capital Price
                                            </th>
                                        @endif
                                        <th>
                                            Sell Price
                                        </th>
                                        <th width="10%">
                                            Discount
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="table_body">
                                    @foreach ($product->productSize as $product_size)
                                        <tr>
                                            <td>
                                                {{ $product_size->size }}
                                            </td>
                                            <td>
                                                {{ $product_size->stock }} Pcs
                                            </td>
                                            @if ($show_capital_price)
                                                <td align="right">
                                                    Rp. {{ number_format($product_size->capital_price, 0, ',', '.') }} ,-
                                                </td>
                                            @endif
                                            <td align="right">
                                                @if ($product_size->discount->percentage > 0)
                                                    <s>Rp. {{ number_format($product_size->sell_price, 0, ',', '.') }}
                                                        ,-</s>
                                                    @php
                                                        $product_sell_price =
                                                            ($product_size->sell_price *
                                                                (100 - $product_size->discount->percentage)) /
                                                            100;
                                                    @endphp
                                                    <span class="ml-2"> Rp.
                                                        {{ number_format($product_sell_price, 0, ',', '.') }} ,-</span>
                                                @else
                                                    <span> Rp.
                                                        {{ number_format($product_size->sell_price, 0, ',', '.') }}
                                                        ,-</span>
                                                @endif
                                            </td>
                                            <td align="center">
                                                {{ $product_size->discount->percentage }}%
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="float-right mt-5">
                            <a href="{{ route('product.index') }}" class="btn btn-sm btn-danger">
                                Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('javascript-bottom')
        @include('javascript.product.script')
    @endpush
@endsection
