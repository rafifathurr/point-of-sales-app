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
                    <form action="{{ route('sales-order.store') }}" id="form_order" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-7 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body px-5 py-5">
                                        <div class="d-flex justify-content-between">
                                            <div class="p-2">
                                                <h4 class="card-title">Liat of Catalogue Product</h4>
                                            </div>
                                            <div class="p-0">
                                                <div class="input-group w-100 mx-auto d-flex">
                                                    <input type="text" id="search_keyword" oninput="catalogue()"
                                                        class="form-control p-3" placeholder="Search Product"
                                                        aria-describedby="search-icon-1">
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row g-4 mt-5" id="waiting-container">
                                            <div class="col-md-12">
                                                <h5 class="text-center">
                                                    <b>Please Waiting...</b>
                                                </h5>
                                            </div>
                                        </div>
                                        <div id="catalogue">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="p-3">
                                            <h4 class="card-title">Add Sales Order</h4>
                                            <hr>
                                            <div class="form-group">
                                                <label for="type">Purchase Type <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" id="type" name="type" required>
                                                    <option disabled hidden selected>Choose Purchase Type</option>
                                                    <option value="0"
                                                        @if (!is_null(old('type')) && old('type') == 0) selected @endif>
                                                        Offline</option>
                                                    <option value="1"
                                                        @if (!is_null(old('type')) && old('type') == 1) selected @endif>Online
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="payment_type">Payment Type <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" id="payment_type" name="payment_type"
                                                    onchange="settingPaymentType(this)" required>
                                                    <option disabled hidden selected>Choose Payment Type</option>
                                                    <option value="0"
                                                        @if (!is_null(old('payment_type')) && old('payment_type') == 0) selected @endif>
                                                        Payment Non Point
                                                    </option>
                                                    <option value="1"
                                                        @if (!is_null(old('payment_type')) && old('payment_type') == 1) selected @endif>
                                                        Payment Point
                                                    </option>
                                                </select>
                                            </div>
                                            <div id="payment_method_form"></div>
                                            <div class="form-group">
                                                <label for="customer_phone">Customer Phone</label>
                                                <select class="form-control" id="customer_phone"
                                                    style="height: 2.875rem !important;"
                                                    onchange="customerChange(this)">
                                                    @foreach ($customer as $cst)
                                                        @if (!is_null(old('customer')) && old('customer') == $cst->id)
                                                            <option value="{{ $cst->id }}" selected>
                                                                {{ $cst->phone }}
                                                            </option>
                                                        @else
                                                            <option value="{{ $cst->id }}">
                                                                {{ $cst->phone }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="customer">Customer Name</label>
                                                <select class="form-control" id="customer" name="customer" readonly>
                                                    @foreach ($customer as $cst)
                                                        @if (!is_null(old('customer')) && old('customer') == $cst->id)
                                                            <option value="{{ $cst->id }}" selected>
                                                                {{ $cst->name }}
                                                            </option>
                                                        @else
                                                            <option value="{{ $cst->id }}">
                                                                {{ $cst->name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="total_percentage">Customer Point</label>
                                                <input type="number" class="form-control" id="customer_point"
                                                    value="{{ old('customer_point') }}" readonly>
                                                <input type="hidden" class="form-control" id="point_result"
                                                    name="point_result" value="{{ old('point_result') }}">
                                            </div>
                                            <div class="text-right mb-3">
                                                <button type="button" onclick="resetSelected()"
                                                    class="btn btn-sm btn-warning text-white mr-2">
                                                    Reset Customer</span>
                                                </button>
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
                                                        @foreach (old('sales_order_item') as $product_id => $sales_order_item_product)
                                                            @foreach ($sales_order_item_product['product_size'] as $product_size_id => $sales_order_item)
                                                                <tr id='product_size_{{ $product_size_id }}'>
                                                                    <td>
                                                                        {{ $sales_order_item['product_name'] }}
                                                                        <input type="hidden"
                                                                            name="sales_order_item[{{ $product_id }}][product]"
                                                                            value="{{ $product_id }}">
                                                                        <input type="hidden"
                                                                            name="sales_order_item[{{ $product_id }}][product_size][{{ $product_size_id }}][product_size]"
                                                                            value="{{ $sales_order_item['product_size'] }}">
                                                                        <input type="hidden"
                                                                            id="product_size_{{ $product_size_id }}"
                                                                            name="sales_order_item[{{ $product_id }}][product_size][{{ $product_size_id }}][product_name]"
                                                                            value="{{ $sales_order_item['product_name'] }}">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number"
                                                                            class="form-control text-center"
                                                                            id="qty_{{ $product_size_id }}"
                                                                            max = '{{ $sales_order_item['stock'] }}'
                                                                            min='1'
                                                                            oninput = "validationQty(this, {{ $product_size_id }})"
                                                                            value="{{ $sales_order_item['qty'] }}"
                                                                            name="sales_order_item[{{ $product_id }}][product_size][{{ $product_size_id }}][qty]">
                                                                        <input type='hidden'
                                                                            name = 'sales_order_item[{{ $product_id }}][product_size][{{ $product_size_id }}][stock]'
                                                                            value = '{{ $sales_order_item['stock'] }}'>
                                                                        <input type="hidden"
                                                                            id="capital_price_{{ $product_size_id }}"
                                                                            name="sales_order_item[{{ $product_id }}][product_size][{{ $product_size_id }}][capital_price]"
                                                                            value="{{ $sales_order_item['capital_price'] }}">
                                                                        <input type="hidden"
                                                                            id="sell_price_{{ $product_size_id }}"
                                                                            name="sales_order_item[{{ $product_id }}][product_size][{{ $product_size_id }}][sell_price]"
                                                                            value="{{ $sales_order_item['sell_price'] }}">
                                                                        <input type="hidden"
                                                                            id="discount_{{ $product_size_id }}"
                                                                            name="sales_order_item[{{ $product_id }}][product_size][{{ $product_size_id }}][discount_price]"
                                                                            value="{{ $sales_order_item['discount_price'] }}">
                                                                    </td>
                                                                    <td align="right">
                                                                        Rp. <span
                                                                            id="price_show_{{ $product_size_id }}">{{ number_format($sales_order_item['total_sell_price'], 0, ',', '.') }}</span>
                                                                        <input type="hidden"
                                                                            id="total_sell_price_{{ $product_size_id }}"
                                                                            name="sales_order_item[{{ $product_id }}][product_size][{{ $product_size_id }}][total_sell_price]"
                                                                            value="{{ $sales_order_item['total_sell_price'] }}">
                                                                        <input type="hidden"
                                                                            id="total_profit_price_{{ $product_size_id }}"
                                                                            name="sales_order_item[{{ $product_id }}][product_size][{{ $product_size_id }}][total_profit_price]"
                                                                            value="{{ $sales_order_item['total_profit_price'] }}">
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
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td>
                                                            <b>Total</b>
                                                        </td>
                                                        <td>
                                                            &nbsp;
                                                            <input type="hidden" name="total_capital_price"
                                                                id="total_capital_price"
                                                                value="{{ old('total_capital_price') }}">
                                                            <input type="hidden" name="total_sell_price"
                                                                id="total_sell_price"
                                                                value="{{ old('total_sell_price') }}">
                                                            <input type="hidden" name="discount_price"
                                                                id="discount_price"
                                                                value="{{ old('discount_price') }}">
                                                            <input type="hidden" name="grand_sell_price"
                                                                id="grand_sell_price"
                                                                value="{{ old('grand_sell_price') }}">
                                                            <input type="hidden" name="grand_profit_price"
                                                                id="grand_profit_price"
                                                                value="{{ old('grand_profit_price') }}">
                                                        </td>
                                                        <td align="right">
                                                            <span
                                                                id="total_price_all_product_show">{{ !is_null(old('grand_sell_price')) ? 'Rp. ' . number_format(old('grand_sell_price'), 0, ',', '.') : 'Rp. 0' }}</span>
                                                        </td>
                                                        <td>
                                                            &nbsp;
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <hr>
                                        <div class="float-right mt-3">
                                            <a href="{{ route('sales-order.index') }}" class="btn btn-sm btn-danger">
                                                Back
                                            </a>
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
    <script>
        $('#customer_phone').val('').trigger('change');
        $('#customer').val('').trigger('change');
    </script>
</body>

</html>
