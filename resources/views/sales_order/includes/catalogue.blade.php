<div class="row g-4 justify-content-start mt-5">
    @foreach ($product_size as $per_product_size)
        <div class="col-md-6 col-lg-4 col-xl-4 py-3">
            <div class="rounded position-relative shadow">
                <div class="w-100 rounded-top border-bottom border-bottom-secondary bg-image"
                    style="background-image:url('{{ asset($per_product_size->product->picture) }}')">
                </div>
                <div class="position-absolute" style="top: 10px; left: 10px;">
                    <div class="p-1 bg-primary text-white px-2 py-1 mr-1 rounded shadow">
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
                                    ($per_product_size->sell_price * (100 - $per_product_size->discount->percentage)) /
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
                        <button type="button" onclick="addProduct({{ $per_product_size->id }})"
                            class="btn btn-sm btn-block btn-outline-primary rounded"
                            id="button_product_size_{{ $per_product_size->id }}">
                            {{-- <i class="fa fa-plus me-2"></i> &nbsp; --}}
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
@if ($update)
    <script>
        $('.pagination a').on('click', function(event) {
            event.preventDefault();

            $('li').removeClass('active');
            $(this).parent('li').addClass('active');

            let page = $(this).attr('href').split('page=')[1];
            catalogueUpdate(page);
        });
    </script>
@else
    <script>
        $('.pagination a').on('click', function(event) {
            event.preventDefault();

            $('li').removeClass('active');
            $(this).parent('li').addClass('active');

            let page = $(this).attr('href').split('page=')[1];
            catalogue(page);
        });
    </script>
@endif
