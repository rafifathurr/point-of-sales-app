<script type="text/javascript">
    function dashboard() {
        let token = $('meta[name="csrf-token"]').attr('content');
        let year = $('#year').val();
        let month = $('#month').val();

        $.ajax({
            url: '{{ url('dashboard') }}',
            type: 'POST',
            cache: false,
            data: {
                _token: token,
                month: month,
                year: year,
            },
            success: function(data) {
                configWidget(data);
            },
            error: function(xhr, error, code) {
                console.log(xhr, error, code);
                sweetAlertError(xhr.responseJSON.message);
            }
        });
    }

    function configWidget(data) {
        $('#total_income').html(data.total_income);
        $('#total_profit').html(data.total_profit);
        $('#total_sales_order').html(data.total_sales_order);
        $('#total_product_sold').html(data.total_product_sold);
    }
</script>
