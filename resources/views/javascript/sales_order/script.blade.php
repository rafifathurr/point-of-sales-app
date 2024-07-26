<script type="text/javascript">
    let update = false;
    $('#customer_phone').select2();
    $('#customer').css("pointer-events", "none");
    $('.select2-selection--single').css('height', '2.875rem');

    $("#form_order").submit(function(e) {
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
                    $('#form_order').unbind('submit').submit();
                }
            })
        }
    });

    $("#form_import").submit(function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are You Sure Want To Import Record?',
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
                $('#form_import').unbind('submit').submit();
            }
        })
    });

    function resetSelected() {
        $('#customer_phone').val('').trigger('change');
        $('#customer').val('').trigger('change');
        $('#customer_point').val('');
        $('#point_result').val('');
        catalogue();
    }

    function customerChange(element) {
        $('#customer').val(element.value);

        if (element.value != '') {
            $.get('{{ url('customer') }}/' + element.value, {}).done(function(data) {
                $('#customer_point').val(data.customer.point);
                $('#point_result').val(data.customer.point);
                catalogue();
            }).fail(function(xhr, status, error) {
                alertError(error);
            });
        } else {
            $('#payment_type').val(0).trigger('change');
        }
    }

    function catalogue(page) {
        let query = $('#search_keyword').val();
        if (!update) {
            if ($('#payment_type').val() == 0) {
                $('#waiting-container').addClass('d-block');
                $('#catalogue').html('');
                if (page != undefined) {
                    $.get("{{ route('sales-order.catalogueProduct') }}", {
                        page: page,
                        payment_type: $('#payment_type').val(),
                        price: null,
                        query: query
                    }).done(function(data) {
                        $('#waiting-container').removeClass('d-block');
                        $('#waiting-container').addClass('d-none');
                        $('#catalogue').html(data);
                    }).fail(function(xhr, status, error) {
                        sweetAlertError(error);
                    });
                } else {
                    $.get("{{ route('sales-order.catalogueProduct') }}", {
                        payment_type: $('#payment_type').val(),
                        price: null,
                        query: query
                    }).done(function(data) {
                        $('#waiting-container').removeClass('d-block');
                        $('#waiting-container').addClass('d-none');
                        $('#catalogue').html(data);
                    }).fail(function(xhr, status, error) {
                        sweetAlertError(error);
                    });
                }
            } else {
                if ($('#point_result').val() != '') {
                    $('#waiting-container').addClass('d-block');
                    $('#catalogue').html('');
                    if (page != undefined) {
                        $.get("{{ route('sales-order.catalogueProduct') }}", {
                            page: page,
                            payment_type: $('#payment_type').val(),
                            price: $('#point_result').val(),
                            query: query
                        }).done(function(data) {
                            $('#waiting-container').removeClass('d-block');
                            $('#waiting-container').addClass('d-none');
                            $('#catalogue').html(data);
                        }).fail(function(xhr, status, error) {
                            sweetAlertError(error);
                        });
                    } else {
                        $.get("{{ route('sales-order.catalogueProduct') }}", {
                            payment_type: $('#payment_type').val(),
                            price: $('#point_result').val(),
                            query: query
                        }).done(function(data) {
                            $('#waiting-container').removeClass('d-block');
                            $('#waiting-container').addClass('d-none');
                            $('#catalogue').html(data);
                        }).fail(function(xhr, status, error) {
                            sweetAlertError(error);
                        });
                    }
                }
            }
        } else {
            if ($('#payment_type').val() == 0) {
                $('#waiting-container').addClass('d-block');
                $('#catalogue').html('');
                if (page != undefined) {
                    $.get("{{ route('sales-order.catalogueProduct') }}", {
                        page: page,
                        payment_type: $('#payment_type').val(),
                        price: null,
                        update: update,
                        query: query
                    }).done(function(data) {
                        $('#waiting-container').removeClass('d-block');
                        $('#waiting-container').addClass('d-none');
                        $('#catalogue').html(data);
                    }).fail(function(xhr, status, error) {
                        sweetAlertError(error);
                    });
                } else {
                    $.get("{{ route('sales-order.catalogueProduct') }}", {
                        payment_type: $('#payment_type').val(),
                        price: null,
                        update: update,
                        query: query
                    }).done(function(data) {
                        $('#waiting-container').removeClass('d-block');
                        $('#waiting-container').addClass('d-none');
                        $('#catalogue').html(data);
                    }).fail(function(xhr, status, error) {
                        sweetAlertError(error);
                    });
                }
            } else {
                if ($('#point_result').val() != '') {
                    $('#waiting-container').addClass('d-block');
                    $('#catalogue').html('');
                    if (page != undefined) {
                        $.get("{{ route('sales-order.catalogueProduct') }}", {
                            page: page,
                            payment_type: $('#payment_type').val(),
                            price: $('#point_result').val(),
                            update: update,
                            query: query
                        }).done(function(data) {
                            $('#waiting-container').removeClass('d-block');
                            $('#waiting-container').addClass('d-none');
                            $('#catalogue').html(data);
                        }).fail(function(xhr, status, error) {
                            sweetAlertError(error);
                        });
                    } else {
                        $.get("{{ route('sales-order.catalogueProduct') }}", {
                            payment_type: $('#payment_type').val(),
                            price: $('#point_result').val(),
                            update: update,
                            query: query
                        }).done(function(data) {
                            $('#waiting-container').removeClass('d-block');
                            $('#waiting-container').addClass('d-none');
                            $('#catalogue').html(data);
                        }).fail(function(xhr, status, error) {
                            sweetAlertError(error);
                        });
                    }
                }
            }
        }
    }

    function settingPaymentType(element) {
        if (element.value == 0) {
            $('#point_result').val('');
            $.get("{{ route('sales-order.paymentMethodForm') }}", {}).done(function(data) {
                catalogue();
                $('#payment_method_form').html(data);

                if (update) {
                    $('#payment_method').val($('#payment_method_record').val()).trigger('change');
                }
            }).fail(function(xhr, status, error) {
                sweetAlertError(error);
            });
        } else {
            catalogue();
            $('#payment_method_form').html('');
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
                [0, 'desc']
            ]
        });
    }

    function addProduct(product) {
        let token = $('meta[name="csrf-token"]').attr('content');
        if ($("#product_size_" + product).length == 0) {
            $.post("{{ route('product.getProductSize') }}", {
                _token: token,
                product: product,
            }).done(function(data) {
                let sell_price = 0;
                let discount = 0;
                if (data.discount.percentage > 0) {
                    discount = parseInt(data.sell_price) * (parseInt(data.discount.percentage)) / 100;
                    sell_price = parseInt(data.sell_price) * (100 - parseInt(data.discount.percentage)) /
                        100;
                } else {
                    sell_price = data.sell_price;
                }
                let profit_price = parseInt(sell_price) - data.capital_price;
                let tr = $("<tr id='product_size_" + data.id + "'></tr>");
                let td_product_size = $("<td>" +
                    data.product.name + ' - ' + data.size +
                    "<input type='hidden' name = 'sales_order_item[" + data.product.id + "][product]'" +
                    "value = '" + data.product.id + "' > " +
                    "<input type='hidden' name = 'sales_order_item[" + data.product.id +
                    "][product_size][" +
                    data.id + "][product_size]'" +
                    "value = '" + data.id + "' > " +
                    "<input type='hidden' name = 'sales_order_item[" + data.product.id +
                    "][product_size][" +
                    data.id + "][product_name]'" +
                    "value = '" + data.product.name + ' - ' + data.size + "' > " +
                    "</td>");
                let td_qty = $("<td>" +
                    "<input type='number' class='form-control text-center' name='sales_order_item[" +
                    data
                    .product.id + "][product_size][" + data
                    .id + "][qty]' " +
                    "id='qty_" + data.id + "'" +
                    "max = '" + data.stock + "' min='1' value='1'" +
                    "oninput = 'validationQty(this, " + data.id + ")'" +
                    "required> " +
                    "<input type='hidden'" +
                    "name = 'sales_order_item[" + data.product.id + "][product_size][" + data.id +
                    "][stock]'" +
                    "value = '" + data.stock + "' > " +
                    "<input type='hidden' id='capital_price_" + data.id +
                    "' name = 'sales_order_item[" +
                    data.product.id + "][product_size][" +
                    data.id + "][capital_price]'" +
                    "value = '" + data.capital_price + "' > " +
                    "<input type='hidden' id='sell_price_" + data.id + "' " +
                    "name = 'sales_order_item[" + data.product.id + "][product_size][" + data.id +
                    "][sell_price]'" +
                    "value = '" + data.sell_price + "' > " +
                    "<input type='hidden' id='discount_" + data.id + "' name = 'sales_order_item[" +
                    data
                    .product.id + "][product_size][" +
                    data.id + "][discount_price]'" +
                    "value = '" + discount + "' > " +
                    "</td>"
                );
                let td_price = $("<td align='right'>" +
                    "Rp. <span id='price_show_" + data.id + "'>" +
                    currencyFormat(sell_price) +
                    "</span>" +
                    "<input type='hidden' id='total_sell_price_" + data.id +
                    "' name = 'sales_order_item[" +
                    data.product.id + "][product_size][" +
                    data.id + "][total_sell_price]'" +
                    "value = '" + sell_price + "' > " +
                    "<input type ='hidden' id = 'total_profit_price_" + data.id + "'" +
                    "value = '" + profit_price + "' name = 'sales_order_item[" + data.product.id +
                    "][product_size][" +
                    data.id + "][total_profit_price]'" +
                    "> " +
                    "</td>"
                );
                let td_del = $(
                    "<td align='center'>" +
                    "<button type='button' class='delete-row btn btn-sm btn-danger' value='Delete'><i class='fas fa-trash'></i></button>" +
                    "<input type='hidden' class='form-control' name='sales_order_item_check[]' value='" +
                    data.id +
                    "'>" +
                    "</td>"
                );
                // Append Tr Element
                (tr.append(td_product_size).append(td_qty).append(td_price).append(td_del));
                // Append To Table
                $("#product_size tbody").append(tr);
                getAccumulationPrice();
            }).fail(function(xhr, status, error) {
                sweetAlertError(error);
            });
        } else {
            let last_qty = $("#qty_" + product).val();
            let product_size_capital_price = $("#capital_price_" + product).val();
            let product_size_sell_price = $("#sell_price_" + product).val();
            let product_size_discount_price = $("#discount_" + product).val();
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
            $('#price_show_' + product).html(currencyFormat(total_sell_price));
            $('#qty_' + product).val(total_qty);
            $('#total_sell_price_' + product).val(total_sell_price);
            $('#total_profit_price_' + product).val(total_profit_price);
            getAccumulationPrice();
        }

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

        if ($('#payment_type').val() == 1) {
            let customer_point = $('#customer_point').val();
            let customer_point_result = parseInt(customer_point) - grand_sell_price_all_product;
            $('#point_result').val(customer_point_result);

            if (customer_point_result < parseInt(customer_point)) {
                $("input[name='sales_order_item_check[]']")
                    .map(function() {
                        $("#qty_" + $(this).val()).attr('readonly', true);
                    });
            } else {
                $("input[name='sales_order_item_check[]']")
                    .map(function() {
                        $("#qty_" + $(this).val()).attr('readonly', false);
                    });
            }

            catalogue();
        }
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
