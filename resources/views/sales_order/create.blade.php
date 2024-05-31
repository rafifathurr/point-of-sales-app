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
                        @csrf
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
                                                    <input type="text" id="search_keyword" oninput="catalogue()"
                                                        class="form-control p-3" placeholder="Search Product"
                                                        aria-describedby="search-icon-1">
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div id="catalogue">
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
                                                <label for="payment_method">Payment Method <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" id="payment_method" name="payment_method"
                                                    required>
                                                    <option disabled hidden selected>Choose Payment Method</option>
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
                                            <div class="form-group">
                                                <label for="customer">Customer Member</label>
                                                <select class="form-control" id="customer" name="customer">
                                                    <option disabled hidden selected>Choose Customer Member</option>
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
                                                                    {{ $sales_order_item['product_name'] }}
                                                                    <input type="hidden" id="product_size_20"
                                                                        name="sales_order_item[{{ $product_size_id }}][product_name]"
                                                                        value="{{ $sales_order_item['product_name'] }}">
                                                                </td>
                                                                <td>
                                                                    <input type="number"
                                                                        class="form-control text-center"
                                                                        id="qty_{{ $product_size_id }}"
                                                                        max = '{{ $sales_order_item['stock'] }}' min='1'
                                                                        value="{{ $sales_order_item['qty'] }}"
                                                                        name="sales_order_item[{{ $product_size_id }}][qty]">
                                                                    <input type='hidden'
                                                                        name = 'sales_order_item[{{ $product_size_id }}][stock]'
                                                                        value = '{{ $sales_order_item['stock'] }}'>
                                                                    <input type="hidden"
                                                                        id="capital_price_{{ $product_size_id }}"
                                                                        name="sales_order_item[{{ $product_size_id }}][capital_price]"
                                                                        value="{{ $sales_order_item['capital_price'] }}">
                                                                    <input type="hidden"
                                                                        id="sell_price_{{ $product_size_id }}"
                                                                        name="sales_order_item[{{ $product_size_id }}][sell_price]"
                                                                        value="{{ $sales_order_item['sell_price'] }}">
                                                                    <input type="hidden"
                                                                        id="discount_{{ $product_size_id }}"
                                                                        name="sales_order_item[{{ $product_size_id }}][discount_price]"
                                                                        value="{{ $sales_order_item['discount_price'] }}">
                                                                </td>
                                                                <td align="right">
                                                                    Rp. <span
                                                                        id="price_show_{{ $product_size_id }}">{{ number_format($sales_order_item['total_sell_price'], 0, ',', '.') }}</span>
                                                                    <input type="hidden"
                                                                        id="total_sell_price_{{ $product_size_id }}"
                                                                        name="sales_order_item[{{ $product_size_id }}][total_sell_price]"
                                                                        value="{{ $sales_order_item['total_sell_price'] }}">
                                                                    <input type="hidden"
                                                                        id="total_profit_price_{{ $product_size_id }}"
                                                                        name="sales_order_item[{{ $product_size_id }}][total_profit_price]"
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
                                            <button type="submit" class="btn btn-sm btn-primary ml-2">Submit</button>
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
        catalogue();
    </script>
</body>

</html>
