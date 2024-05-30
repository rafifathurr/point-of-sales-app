<div class="row g-4 justify-content-start mt-5">
    <div class="d-flex">
        <div class="py-3">
            <img width="80%" src="{{ asset('images/search-product.webp') }}" alt="">
        </div>
        <div class="pr-3 py-3 my-auto">
            <h2>
                <b>Oops, product not found!</b>
            </h2>
            <h3 class="mt-3">Try searching for other keywords</h3>
            <button type="button" class="btn btn-primary mt-3" id="focus-search"><b>Change Keyword Product</b></button>
        </div>
    </div>
</div>
<script>
    $('#focus-search').on('click', function(event) {
        $('#search_keyword').focus();
    });
</script>