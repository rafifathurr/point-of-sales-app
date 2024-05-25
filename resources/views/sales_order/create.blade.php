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
                    <div class="row">
                        <div class="col-lg-8 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body px-5 py-5">
                                    <div class="d-flex justify-content-between">
                                        <div class="p-2">
                                            <h4 class="card-title">Catalogue Product</h4>
                                        </div>
                                        <div class="p-0">
                                            <form action="">
                                                <div class="input-group w-100 mx-auto d-flex">
                                                    <input type="search" class="form-control p-3"
                                                        placeholder="Search Product" aria-describedby="search-icon-1">
                                                    <button type="submit" class="input-group-text">
                                                        <i class="fa fa-search"></i>
                                                    </button>
                                                </div>
                                            </form>
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
                                                        <div class="d-flex">
                                                            <div
                                                                class="p-1 bg-primary text-white px-2 py-1 mr-1 rounded">
                                                                {{ $per_product_size->product->categoryProduct->name }}
                                                            </div>
                                                            <div class="p-1 bg-secondary text-white px-2 py-1 rounded">
                                                                {{ $per_product_size->size }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="p-3 rounded-bottom">
                                                        @if ($per_product_size->stock < 10)
                                                            <span class="text-danger">Remainder
                                                                {{ $per_product_size->stock }} Pcs</span>
                                                        @else
                                                            <span class="text-danger">&nbsp;</span>
                                                        @endif
                                                        <h4 class="mt-3">{{ $per_product_size->product->name }}
                                                        </h4>
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
                                                                <h4 class="text-dark fw-bold mb-2">
                                                                    Rp.
                                                                    {{ number_format($product_sell_price, 0, ',', '.') }}
                                                                </h4>
                                                                <span class="text-secondary">
                                                                    <s>Rp.
                                                                        {{ number_format($per_product_size->sell_price, 0, ',', '.') }}</s>
                                                                </span>
                                                                <span class="text-danger fw-bold">
                                                                    {{ $per_product_size->discount->percentage }}%
                                                                </span>
                                                            @else
                                                                <h4 class="text-dark fw-bold mb-2">
                                                                    Rp.
                                                                    {{ number_format($per_product_size->sell_price, 0, ',', '.') }}
                                                                </h4>
                                                            @endif
                                                        </div>
                                                        <div class="my-3">
                                                            <a href="#"
                                                                class="btn btn-outline-primary rounded-pill shadow"><i
                                                                    class="fa fa-plus me-2"></i>
                                                                Add Product
                                                            </a>
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
                        <div class="col-lg-4 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body p-5">
                                    <div class="d-flex justify-content-between">
                                        <div class="p-2">
                                            <h4 class="card-title">Sales Order</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
</body>

</html>
