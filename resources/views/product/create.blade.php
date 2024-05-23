@extends('layouts.section')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body p-5">
                        <h4 class="card-title">Add Product</h4>
                        <form class="forms-sample" method="post" action="{{ route('product.store') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                                    value="{{ old('name') }}" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="category_product">Category Product <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" id="category_product" name="category_product" required>
                                            <option selected hidden>Choose Category Product</option>
                                            @foreach ($category_product as $category)
                                                @if (!is_null(old('category_product')) && old('category_product') == $category->id)
                                                    <option value="{{ $category->id }}" selected>{{ $category->name }}
                                                    </option>
                                                @else
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="supplier">Supplier <span class="text-danger">*</span></label>
                                        <select class="form-control" id="supplier" name="supplier" required>
                                            <option selected hidden>Choose Supplier</option>
                                            @foreach ($supplier as $sup)
                                                @if (!is_null(old('supplier')) && old('supplier') == $sup->id)
                                                    <option value="{{ $sup->id }}" selected>{{ $sup->name }}
                                                    </option>
                                                @else
                                                    <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="picture">Picture <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="picture" name="picture"
                                    placeholder="Attach Picture" value="{{ old('picture') }}"
                                    accept="image/jpeg,image/jpg,image/png" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" name="description" id="description" cols="10" rows="3"
                                    placeholder="Description">{{ old('description') }}</textarea>
                            </div>
                            <div class="table-responsive mt-5">
                                <table class="table table-bordered datatable" id="product_size">
                                    <thead>
                                        <tr>
                                            <th width="10%">
                                                Size
                                            </th>
                                            <th>
                                                Weight
                                            </th>
                                            <th>
                                                Stock
                                            </th>
                                            <th>
                                                Capital Price
                                            </th>
                                            <th>
                                                Sell Price
                                            </th>
                                            <th width="13%">
                                                Discount
                                            </th>
                                            <th width="5%">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="table_body">
                                        <tr id="form_size_product">
                                            <td>
                                                <input type="text" class="form-control" id="size" id="size">
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <input type="number" class="form-control" id="weight"
                                                        min="0">
                                                    <span class="input-group-text bg-default p-2">Gram</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <input type="number" class="form-control" id="stock_item"
                                                        min="0">
                                                    <span class="input-group-text bg-default p-2">Pcs</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <span class="input-group-text bg-default p-2">Rp.</span>
                                                    <input type="number" class="form-control" id="capital_price"
                                                        min="0">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <span class="input-group-text bg-default p-2">Rp.</span>
                                                    <input type="number" class="form-control" id="sell_price"
                                                        min="0">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <input type="number" class="form-control" id="discount" min="0"
                                                        max="100">
                                                    <span class="input-group-text bg-default p-2">%</span>
                                                </div>
                                            </td>
                                            <td align="center">
                                                <button type="button" class="btn btn-sm btn-primary"
                                                    onclick="addSizeProduct()">Add</button>
                                            </td>
                                        </tr>
                                        @if (!is_null(old('product_size')))
                                            @foreach (old('product_size') as $index => $product_size)
                                                <tr>
                                                    <td>
                                                        <input type='text' class='form-control'
                                                            name='product_size[{{ $index }}][size]'
                                                            value='{{ $product_size['size'] }}' required>
                                                    </td>
                                                    <td>
                                                        <div class='d-flex'>
                                                            <input type='number' class='form-control'
                                                                name='product_size[{{ $index }}][weight]'
                                                                min='0' value='{{ $product_size['weight'] }}'
                                                                required>
                                                            <span class='input-group-text bg-default p-2'>Gram</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class='d-flex'>
                                                            <input type='number' class='form-control'
                                                                name='product_size[{{ $index }}][stock]'
                                                                min='0' value='{{ $product_size['stock'] }}'
                                                                required>
                                                            <span class='input-group-text bg-default p-2'>Pcs</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class='d-flex'>
                                                            <span class='input-group-text bg-default p-2'>Rp.</span>
                                                            <input type='number' class='form-control'
                                                                name='product_size[{{ $index }}][capital_price]'
                                                                min='0'
                                                                value='{{ $product_size['capital_price'] }}' required>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class='d-flex'>
                                                            <span class='input-group-text bg-default p-2'>Rp.</span>
                                                            <input type='number' class='form-control'
                                                                name='product_size[{{ $index }}][sell_price]'
                                                                min='0' value='{{ $product_size['sell_price'] }}'
                                                                required>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class='d-flex'>
                                                            <input type='number' class='form-control'
                                                                name='product_size[{{ $index }}][percentage]'
                                                                min='0' max='100'
                                                                value='{{ $product_size['percentage'] }}' required>
                                                            <span class='input-group-text bg-default p-2'>%</span>
                                                        </div>
                                                    </td>
                                                    <td align='center'>
                                                        <button type='button' class='delete-row btn btn-sm btn-danger'
                                                            value='Delete'>Delete</button>
                                                        <input type='hidden' class='form-control'
                                                            name='product_item_check[]'
                                                            value='{{ $product_size['size'] }}'>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="float-right mt-5">
                                <a href="{{ route('product.index') }}" class="btn btn-sm btn-danger">
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
        @include('javascript.product.script')
    @endpush
@endsection
