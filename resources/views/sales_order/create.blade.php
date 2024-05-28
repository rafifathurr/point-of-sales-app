<!DOCTYPE html>
<html lang="en">

@include('layouts.head')

<body>
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        @include('layouts.navbar')
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <div class="main-panel w-100">
                <div class="content-wrapper">
                    <form action="{{ route('sales-order.store') }}" id="form_page" method="post">
                        <div class="row">
                            <div class="col-md-7 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body px-5 py-5">
                                        <div class="d-flex justify-content-between">
                                            <div class="p-2">
                                                <h4 class="card-title">Catalogue Product</h4>
                                            </div>
                                            <div class="p-0">
                                                <div class="input-group w-100 mx-auto d-flex">
                                                    <input type="search" class="form-control p-3"
                                                        placeholder="Search Product" aria-describedby="search-icon-1">
                                                    <button type="button" class="input-group-text"
                                                        onclick="searchKeyword()">
                                                        <i class="fa fa-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row g-4 justify-content-center mt-5">
                                            @foreach ($product_size as $per_product_size)
                                                <div class="col-md-6 col-lg-4 col-xl-4 py-3">
                                                    <div class="rounded position-relative shadow">
                                                        <div class="w-100 rounded-top border-bottom border-bottom-secondary bg-image"
                                                            style="background-image:url('{{ '../' . $per_product_size->product->picture }}')">
                                                        </div>
                                                        <div class="position-absolute" style="top: 10px; left: 10px;">
                                                            <div
                                                                class="p-1 bg-primary text-white px-2 py-1 mr-1 rounded shadow">
                                                                <b>{{ $per_product_size->product->categoryProduct->name }}</b>
                                                            </div>
                                                        </div>
                                                        <div class="p-3 rounded-bottom">
                                                            <div class="d-flex">
                                                                <div class="bg-secondary px-2 py-1 mr-2 rounded">
                                                                    <p class="text-white my-auto">
                                                                        <b>{{ $per_product_size->size }}</b>
                                                                    </p>
                                                                </div>
                                                                @if ($per_product_size->stock < 10)
                                                                    <p class="text-danger my-auto">Remainder
                                                                        {{ $per_product_size->stock }} Pcs</p>
                                                                @else
                                                                    <p class="text-danger">&nbsp;</p>
                                                                @endif
                                                            </div>
                                                            <h5 class="mt-3">
                                                                <b>{{ $per_product_size->product->name }}</b>
                                                            </h5>
                                                            <div class="mt-3">
                                                                @if ($per_product_size->discount->percentage > 0)
                                                                    @php
                                                                        $product_sell_price =
                                                                            ($per_product_size->sell_price *
                                                                                (100 -
                                                                                    $per_product_size->discount
                                                                                        ->percentage)) /
                                                                            100;
                                                                    @endphp
                                                                    <h5 class="text-dark mb-2">
                                                                        Rp.
                                                                        {{ number_format($product_sell_price, 0, ',', '.') }}
                                                                    </h5>
                                                                    <div class="d-flex">
                                                                        <span class="text-muted mr-2 my-auto">
                                                                            <s>Rp.
                                                                                {{ number_format($per_product_size->sell_price, 0, ',', '.') }}</s>
                                                                        </span>
                                                                        <div class="bg-danger text-white p-1 rounded">
                                                                            {{ $per_product_size->discount->percentage }}%
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <h5 class="text-dark mb-2">
                                                                        Rp.
                                                                        {{ number_format($per_product_size->sell_price, 0, ',', '.') }}
                                                                    </h5>
                                                                @endif
                                                            </div>
                                                            <div class="my-3">
                                                                <button type="button"
                                                                    onclick="addProduct({{ $per_product_size->id }})"
                                                                    class="btn btn-sm btn-block btn-outline-primary rounded">
                                                                    <i class="fa fa-plus me-2"></i> &nbsp;
                                                                    Add Product
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="row justify-content-center">
                                            <div class="mt-3">
                                                {{ $product_size->links() }}
                                            </div>
                                        </div>
                                        <div class="float-right mt-3">
                                            <a href="{{ route('sales-order.index') }}" class="btn btn-sm btn-danger">
                                                Back
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="p-3">
                                            <h4 class="card-title">Sales Order</h4>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="type">Purchase Type <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-control" id="type" name="type"
                                                            required>
                                                            <option disabled hidden>Choose Purchase Type</option>
                                                            <option value="0"
                                                                @if (!is_null(old('type')) && old('type') == 0) selected @endif>
                                                                Offline</option>
                                                            <option value="1"
                                                                @if (!is_null(old('type')) && old('type') == 1) selected @endif>Online
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="payment_method">Payment Method <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-control" id="payment_method"
                                                            name="payment_method" required>
                                                            <option disabled hidden>Choose Payment Method</option>
                                                            @foreach ($payment_method as $pm)
                                                                @if (!is_null(old('payment_method')) && old('payment_method') == $pm->id)
                                                                    <option value="{{ $pm->id }}" selected>
                                                                        {{ $pm->name }}
                                                                    </option>
                                                                @else
                                                                    <option value="{{ $pm->id }}">
                                                                        {{ $pm->name }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive mb-5">
                                            <table class="table datatable" id="product_size">
                                                <thead>
                                                    <tr>
                                                        <th width="20%">
                                                            Product
                                                        </th>
                                                        <th width="30%">
                                                            Qty
                                                        </th>
                                                        <th>
                                                            Total Price
                                                        </th>
                                                        <th width="20%">
                                                            Action
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_body">
                                                    @if (!is_null(old('sales_order_item')))
                                                        @foreach (old('sales_order_item') as $product_size_id => $sales_order_item)
                                                            <tr id='produxt_size_{{ $product_size_id }}'>
                                                                <td>
                                                                    {{ $sales_order_item->product_name }}
                                                                    <input type="hidden" id="product_size_20"
                                                                        name="sales_order_item[{{ $product_size_id }}][product_name]"
                                                                        value="{{ $sales_order_item->product_name }}">
                                                                </td>
                                                                <td>
                                                                    <input type="number"
                                                                        class="form-control text-center"
                                                                        id="qty_{{ $product_size_id }}"
                                                                        value="{{ $sales_order_item->qty }}"
                                                                        name="sales_order_item[product_size_id][qty]">
                                                                    <input type="hidden"
                                                                        id="capital_price_{{ $product_size_id }}"
                                                                        name="sales_order_item[product_size_id][capital_price]"
                                                                        value="{{ $sales_order_item->capital_price }}">
                                                                    <input type="hidden"
                                                                        id="sell_price_{{ $product_size_id }}"
                                                                        name="sales_order_item[product_size_id][sell_price]"
                                                                        value="{{ $sales_order_item->sell_price }}">
                                                                    <input type="hidden"
                                                                        id="discount_{{ $product_size_id }}"
                                                                        name="sales_order_item[product_size_id][discount_price]"
                                                                        value="{{ $sales_order_item->discount_price }}">
                                                                </td>
                                                                <td align="right">
                                                                    Rp. <span
                                                                        id="price_show_{{ $product_size_id }}">{{ number_format($sales_order_item->total_sell_price, 0, ',', '.') }}</span>
                                                                    <input type="hidden"
                                                                        id="total_sell_price_{{ $product_size_id }}"
                                                                        name="sales_order_item[product_size_id][total_sell_price]"
                                                                        value="{{ $sales_order_item->total_sell_price }}">
                                                                    <input type="hidden"
                                                                        id="total_profit_price_{{ $product_size_id }}"
                                                                        name="sales_order_item[product_size_id][total_profit_price]"
                                                                        value="{{ $sales_order_item->total_profit_price }}">
                                                                </td>
                                                                <td align="center">
                                                                    <button type="button"
                                                                        class="delete-row btn btn-sm btn-danger"
                                                                        title="Delete">Del</button>
                                                                    <input type="hidden"
                                                                        name="sales_order_item_check[]"
                                                                        value="{{ $product_size_id }}">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td>
                                                            <b>Total</b>
                                                        </td>
                                                        <td>

                                                        </td>
                                                        <td>

                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <hr>
                                        <div class="float-right mt-3">
                                            <button type="submit" class="btn btn-sm btn-primary mr-2">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- partial:partials/_footer.html -->
                @include('layouts.footer')
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    @include('layouts.script')
    @include('javascript.sales_order.script')
</body>

</html>
