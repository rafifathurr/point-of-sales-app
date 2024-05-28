@extends('layouts.section')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body p-5">
                        <h4 class="card-title">Add {{ $title }}</h4>
                        <form class="forms-sample" method="post" action="{{ $store_route }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="product_size">Product <span class="text-danger">*</span></label>
                                        <select class="form-control" id="product_size" name="product_size"
                                            @if (!$stock_in_status) onchange="getProduct(this.value)" @endif
                                            required>
                                            <option disabled selected>Choose Product</option>
                                            @foreach ($product as $product_size)
                                                @if (!is_null(old('product_size')) && old('product_size') == $product_size->id)
                                                    <option value="{{ $product_size->id }}" selected>
                                                        {{ $product_size->product->name }} - {{ $product_size->size }}
                                                    </option>
                                                @else
                                                    <option value="{{ $product_size->id }}">
                                                        {{ $product_size->product->name }} - {{ $product_size->size }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date">Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="date" max="{{ date('Y-m-d') }}"
                                            value="{{ old('date') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="qty">Qty <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" oninput="validationQty(this)"
                                            name="qty" id="qty" min="1" value="{{ old('qty') }}"
                                            @if (!$stock_in_status) readonly @endif required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" name="description" id="description" cols="10" rows="3"
                                    placeholder="Description">{{ old('description') }}</textarea>
                            </div>
                            <div class="float-right">
                                <a href="{{ $index_route }}" class="btn btn-sm btn-danger">
                                    Back
                                </a>
                                <button type="submit" class="btn btn-sm btn-primary mr-2">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('javascript-bottom')
        @include('javascript.stock.script')
    @endpush
@endsection
