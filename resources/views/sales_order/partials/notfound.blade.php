<div class="row g-4 mt-5">
    <div class="d-flex mx-auto justify-content-center">
        <img width="30%" src="{{ asset('images/search-product.webp') }}" alt="">
        <div class="p-5 my-auto">
            <h3>
                <b>Oops, product not found!</b>
            </h3>
        </div>
    </div>
</div>
<script>
    $('#focus-search').on('click', function(event) {
        $('#search_keyword').focus();
    });
</script>