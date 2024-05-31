<div class="row g-4 mt-5">
    <div class="d-flex mx-auto justify-content-center">
        <img width="40%" src="{{ asset('images/search-product.webp') }}" alt="">
        <div class="p-5">
            <h3>
                <b>Oops, product not found!</b>
            </h3>
            <h4 class="mt-3">Try searching for other keywords</h4>
            <button type="button" class="btn btn-primary mt-3" id="focus-search"><b>Change Keyword Product</b></button>
        </div>
    </div>
</div>
<script>
    $('#focus-search').on('click', function(event) {
        $('#search_keyword').focus();
    });
</script>