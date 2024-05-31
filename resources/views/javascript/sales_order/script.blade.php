<script type="text/javascript">

    $("form").submit(function(e) {
        e.preventDefault();
        if ($("input[name='sales_order_item_check[]']").val() === undefined) {
            sweetAlertWarning('Please Complete The Record!');
        } else {
            Swal.fire({
                title: 'Are You Sure Want To Save Record?',
                icon: 'question',
                showCancelButton: true,
                allowOutsideClick: false,
                customClass: {
                    confirmButton: 'btn btn-primary mr-2 mb-3',
                    cancelButton: 'btn btn-danger mb-3',
                },
                buttonsStyling: false,
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    sweetAlertProcess();
                    $('form').unbind('submit').submit();
                }
            })
        }
    });

    function catalogue(page) {

        $('#catalogue').html('');
        let query = $('#search_keyword').val();

        if (page != undefined) {
            $.get("{{ route('sales-order.catalogueProduct') }}", {
                page: page,
                query: query
            }).done(function(data) {

                $('#catalogue').html(data);

            }).fail(function(xhr, status, error) {
                sweetAlertError(error);
            });
        } else {
            $.get("{{ route('sales-order.catalogueProduct') }}", {
                query: query
            }).done(function(data) {

                $('#catalogue').html(data);

            }).fail(function(xhr, status, error) {
                sweetAlertError(error);
            });
        }

    }

    function dataTable() {
        const url = $('#url_dt').val();
        $('#dt-sales-order').DataTable({
            autoWidth: false,
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: url,
                error: function(xhr, error, code) {
                    sweetAlertError(xhr.statusText);
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    width: '5%',
                    searchable: false
                },
                {
                    data: 'invoice_number',
                    defaultContent: '-',
                },
                {
                    data: 'created_at',
                    defaultContent: '-',
                },
                {
                    data: 'type',
                    defaultContent: '-',
                },
                {
                    data: 'payment_method',
                    defaultContent: '-',
                },
                {
                    data: 'grand_sell_price',
                    defaultContent: '-',
                },
                {
                    data: 'action',
                    width: '20%',
                    defaultContent: '-',
                    orderable: false,
                    searchable: false
                },
            ],
            order: [
                [2, 'desc']
            ]
        });
    }

    function addProduct(product) {
        let token = $('meta[name="csrf-token"]').attr('content');

        $.post("{{ route('product.getProductSize') }}", {
            _token: token,
            product: product,
        }).done(function(data) {

            if ($("#qty_" + data.id).length == 0) {

                let sell_price = 0;
                let discount = 0;

                if (data.discount.percentage > 0) {
                    discount = parseInt(data.sell_price) * (parseInt(data.discount.percentage)) / 100;
                    sell_price = parseInt(data.sell_price) * (100 - parseInt(data.discount.percentage)) / 100;
                } else {
                    sell_price = data.sell_price;
                }

                let profit_price = parseInt(sell_price) - data.capital_price;

                let tr = $("<tr id='produxt_size_" + data.id + "'></tr>");

                let td_product_size = $("<td>" +
                    data.product.name + ' - ' + data.size +
                    "<input type='hidden' name = 'sales_order_item[" +
                    data.id + "][product_name]'" +
                    "value = '" + data.product.name + ' - ' + data.size + "' > " +
                    "</td>");

                let td_qty = $("<td>" +
                    "<input type='number' class='form-control text-center' name='sales_order_item[" + data
                    .id + "][qty]' " +
                    "id='qty_" + data.id + "'" +
                    "max = '" + data.stock + "' min='1' value='1'" +
                    "oninput = 'validationQty(this, " + data.id + ")'" +
                    "required> " +
                    "<input type='hidden'" +
                    "name = 'sales_order_item[" + data.id + "][stock]'" +
                    "value = '" + data.stock + "' > " +
                    "<input type='hidden' id='capital_price_" + data.id + "' name = 'sales_order_item[" +
                    data.id + "][capital_price]'" +
                    "value = '" + data.capital_price + "' > " +
                    "<input type='hidden' id='sell_price_" + data.id + "' " +
                    "name = 'sales_order_item[" + data.id + "][sell_price]'" +
                    "value = '" + data.sell_price + "' > " +
                    "<input type='hidden' id='discount_" + data.id + "' name = 'sales_order_item[" +
                    data.id + "][discount_price]'" +
                    "value = '" + discount + "' > " +
                    "</td>"
                );

                let td_price = $("<td align='right'>" +
                    "Rp. <span id='price_show_" + data.id + "'>" +
                    currencyFormat(sell_price) +
                    "</span>" +
                    "<input type='hidden' id='total_sell_price_" + data.id + "' name = 'sales_order_item[" +
                    data.id + "][total_sell_price]'" +
                    "value = '" + sell_price + "' > " +
                    "<input type ='hidden' id = 'total_profit_price_" + data.id + "'" +
                    "value = '" + profit_price + "' name = 'sales_order_item[" +
                    data.id + "][total_profit_price]'" +
                    "> " +
                    "</td>"
                );

                let td_del = $(
                    "<td align='center'>" +
                    "<button type='button' class='delete-row btn btn-sm btn-danger' value='Delete'>Del</button>" +
                    "<input type='hidden' class='form-control' name='sales_order_item_check[]' value='" +
                    data.id +
                    "'>" +
                    "</td>"
                );

                // Append Tr Element
                (tr.append(td_product_size).append(td_qty).append(td_price).append(td_del));

                // Append To Table
                $("#product_size tbody").append(tr);
            } else {
                let last_qty = $("#qty_" + data.id).val();
                let product_size_capital_price = $("#capital_price_" + data.id).val();
                let product_size_sell_price = $("#sell_price_" + data.id).val();
                let product_size_discount_price = $("#discount_" + data.id).val();

                let total_sell_price = 0;
                let total_discount_price = 0;
                let total_qty = parseInt(last_qty) + 1;

                if (parseInt(product_size_discount_price) > 0) {
                    total_discount_price = parseInt(product_size_discount_price) * total_qty;
                    total_sell_price = (parseInt(product_size_sell_price) * total_qty) - total_discount_price;
                } else {
                    total_sell_price = parseInt(product_size_sell_price) * total_qty;
                }

                let total_profit_price = total_sell_price - (parseInt(product_size_capital_price) * total_qty);

                $('#price_show_' + data.id).html(currencyFormat(total_sell_price));
                $('#qty_' + data.id).val(total_qty);
                $('#total_sell_price_' + data.id).val(total_sell_price);
                $('#total_profit_price_' + data.id).val(total_profit_price);

            }

            getAccumulationPrice();

        }).fail(function(xhr, status, error) {
            sweetAlertError(error);
        });
    }

    // Currency Format
    function currencyFormat(value) {
        return value.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
    }

    // Find and remove selected table rows
    $("table#product_size").on("click", ".delete-row", function(event) {
        $(this).closest("tr").remove();
        getAccumulationPrice();
    });

    function validationQty(element, key) {
        if ((element.value < element.min && element.value != '') || element.value == '') {
            $('#' + element.id).val(element.min);
        }

        if (element.max != '') {
            if (element.value.length == element.max.length) {
                if (element.value > element.max) {
                    $('#' + element.id).val(element.max);
                }
            } else {
                if (element.value.length > element.max.length) {
                    $('#' + element.id).val(element.max);
                }
            }
        }

        let total_qty = $('#' + element.id).val();
        let product_size_capital_price = $("#capital_price_" + key).val();
        let product_size_sell_price = $("#sell_price_" + key).val();
        let product_size_discount_price = $("#discount_" + key).val();

        let total_sell_price = 0;
        let total_discount_price = 0;

        if (parseInt(product_size_discount_price) > 0) {
            total_discount_price = parseInt(product_size_discount_price) * total_qty;
            total_sell_price = (parseInt(product_size_sell_price) * total_qty) - total_discount_price;
        } else {
            total_sell_price = parseInt(product_size_sell_price) * total_qty;
        }

        let total_profit_price = total_sell_price - (parseInt(product_size_capital_price) * total_qty);

        $('#price_show_' + key).html(currencyFormat(total_sell_price));
        $('#qty_' + key).val(total_qty);
        $('#total_sell_price_' + key).val(total_sell_price);
        $('#total_profit_price_' + key).val(total_profit_price);

        getAccumulationPrice();

    }

    function getAccumulationPrice() {
        let total_capital_price_all_product = 0;
        let total_sell_price_all_product = 0;
        let discount_price = 0;
        let grand_sell_price_all_product = 0;
        let grand_profit_price_all_product = 0;

        $("input[name='sales_order_item_check[]']")
            .map(function() {
                total_capital_price_all_product += parseInt($('#capital_price_' + $(this).val())
                    .val()) * $("#qty_" + $(this).val()).val();

                total_sell_price_all_product += parseInt($('#sell_price_' + $(this).val())
                    .val()) * $("#qty_" + $(this).val()).val();

                discount_price += parseInt($('#discount_' + $(this).val())
                    .val()) * $("#qty_" + $(this).val()).val();

                grand_sell_price_all_product += parseInt($(
                    '#total_sell_price_' + $(this).val()).val());

                grand_profit_price_all_product +=
                    parseInt($('#total_profit_price_' + $(this).val()).val());
            });

        $('#total_price_all_product_show').html('Rp. ' + currencyFormat(grand_sell_price_all_product));
        $('#total_capital_price').val(total_capital_price_all_product);
        $('#total_sell_price').val(total_sell_price_all_product);
        $('#discount_price').val(discount_price);
        $('#grand_sell_price').val(grand_sell_price_all_product);
        $('#grand_profit_price').val(grand_profit_price_all_product);
    }

    function destroyRecord(id) {
        let token = $('meta[name="csrf-token"]').attr('content');

        Swal.fire({
            title: 'Are You Sure Want To Delete Record?',
            icon: 'question',
            showCancelButton: true,
            allowOutsideClick: false,
            customClass: {
                confirmButton: 'btn btn-primary mr-2 mb-3',
                cancelButton: 'btn btn-danger mb-3',
            },
            buttonsStyling: false,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                sweetAlertProcess();
                $.ajax({
                    url: '{{ url('sales-order') }}/' + id,
                    type: 'DELETE',
                    cache: false,
                    data: {
                        _token: token
                    },
                    success: function(data) {
                        location.reload();
                    },
                    error: function(xhr, error, code) {
                        sweetAlertError(error);
                    }
                });
            }
        })
    }
</script>
