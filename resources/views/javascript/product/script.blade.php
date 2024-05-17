<script type="text/javascript">
    $("form").submit(function(e) {
        e.preventDefault();
        if ($("input[name='product_item_check[]']").val() === undefined) {
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

    function dataTable() {
        const url = $('#url_dt').val();
        $('#dt-product').DataTable({
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
                    data: 'name',
                    defaultContent: '-',
                },
                {
                    data: 'category',
                    defaultContent: '-',
                },
                {
                    data: 'status',
                    width: '15%',
                    defaultContent: '-',
                    class: 'text-center'
                },
                {
                    data: 'action',
                    width: '20%',
                    defaultContent: '-',
                    orderable: false,
                    searchable: false
                },
            ]
        });
    }

    function addSizeProduct(super_admin) {
        let size = $('#size').val();
        let weight = $('#weight').val();
        let stock = $('#stock_item').val();
        let capital_price = $('#capital_price').val();
        let sell_price = $('#sell_price').val();
        let discount = $('#discount').val();

        let size_id = size.toLowerCase();
        let index = $("#product_size tbody tr").length - 1;

        if (super_admin) {
            if (size != '' && weight != '' && stock != '' && capital_price != '' && sell_price != '') {

                let form_product = $("#form_size_product");
                let tr = $("<tr></tr>");
                let td_size = $("<td>" +
                    "<input type='text' class='form-control' name='product_size[" + index + "][size]' value='" +
                    size +
                    "' required>" +
                    "</td>");
                let td_weight = $("<td>" +
                    "<div class='d-flex'>" +
                    "<input type='number' class='form-control' name='product_size[" + index +
                    "][weight]' min='0' value='" +
                    weight +
                    "' required>" +
                    "<span class='input-group-text bg-default p-2'>Gram</span>" +
                    "</div>" +
                    "</td>");
                let td_stock = $("<td>" +
                    "<div class='d-flex'>" +
                    "<input type='number' class='form-control' name='product_size[" + index +
                    "][stock]' min='0' value='" +
                    stock +
                    "' required>" +
                    "<span class='input-group-text bg-default p-2'>Pcs</span>" +
                    "</div>" +
                    "</td>");
                let td_capital_price = $("<td>" +
                    "<div class='d-flex'>" +
                    "<span class='input-group-text bg-default p-2'>Rp.</span>" +
                    "<input type='number' class='form-control' name='product_size[" + index +
                    "][capital_price]' min='0' value='" +
                    capital_price +
                    "' required>" +
                    "</div>" +
                    "</td>");

                let td_discount = '';

                let td_sell_price = $("<td>" +
                    "<div class='d-flex'>" +
                    "<span class='input-group-text bg-default p-2'>Rp.</span>" +
                    "<input type='number' class='form-control' name='product_size[" + index +
                    "][sell_price]' min='0' value='" +
                    sell_price +
                    "' required>" +
                    "</div>" +
                    "</td>");

                if (discount != '') {
                    td_discount = $("<td>" +
                        "<div class='d-flex'>" +
                        "<input type='number' class='form-control' name='product_size[" + index +
                        "][percentage]' min='0' max='100' value='" +
                        discount +
                        "' required>" +
                        "<span class='input-group-text bg-default p-2'>%</span>" +
                        "</div>" +
                        "</td>");
                } else {
                    discount = 0;
                    td_discount = $("<td>" +
                        "<div class='d-flex'>" +
                        "<input type='number' class='form-control' name='product_size[" + index +
                        "][percentage]' min='0' max='100' value='" +
                        discount +
                        "' required>" +
                        "<span class='input-group-text bg-default p-2'>%</span>" +
                        "</div>" +
                        "</td>");
                }

                let td_del = $(
                    "<td align='center'>" +
                    "<button type='button' class='delete-row btn btn-sm btn-danger' value='Delete'>Delete</button>" +
                    "<input type='hidden' class='form-control' name='product_item_check[]' value='" +
                    size +
                    "'>" +
                    "</td>"
                );

                // Append Tr Element
                (tr.append(td_size).append(td_weight).append(td_stock).append(td_capital_price).append(td_sell_price)
                    .append(td_discount).append(td_del)).insertAfter(form_product)

                // Append To Table
                $("#product_size tbody").append(tr);

                // Reset Field Value
                $('#size').val('');
                $('#weight').val('');
                $('#stock_item').val('');
                $('#capital_price').val('');
                $('#sell_price').val('');
                $('#discount').val('');
            } else {
                sweetAlertWarning('Please Complete The Record!');
            }
        } else {
            if (size != '' && weight != '' && stock != '' && sell_price != '') {

                let form_product = $("#form_size_product");
                let tr = $("<tr></tr>");
                let td_size = $("<td>" +
                    "<input type='text' class='form-control' name='product_size[" + index + "][size]' value='" +
                    size +
                    "' required>" +
                    "</td>");
                let td_weight = $("<td>" +
                    "<div class='d-flex'>" +
                    "<input type='number' class='form-control' name='product_size[" + index +
                    "][weight]' min='0' value='" +
                    weight +
                    "' required>" +
                    "<span class='input-group-text bg-default p-2'>Gram</span>" +
                    "</div>" +
                    "</td>");
                let td_stock = $("<td>" +
                    "<div class='d-flex'>" +
                    "<input type='number' class='form-control' name='product_size[" + index +
                    "][stock]' min='0' value='" +
                    stock +
                    "' required>" +
                    "<span class='input-group-text bg-default p-2'>Pcs</span>" +
                    "</div>" +
                    "</td>");

                let td_discount = '';

                let td_sell_price = $("<td>" +
                    "<div class='d-flex'>" +
                    "<span class='input-group-text bg-default p-2'>Rp.</span>" +
                    "<input type='number' class='form-control' name='product_size[" + index +
                    "][sell_price]' min='0' value='" +
                    sell_price +
                    "' required>" +
                    "</div>" +
                    "</td>");

                if (discount != '') {
                    td_discount = $("<td>" +
                        "<div class='d-flex'>" +
                        "<input type='number' class='form-control' name='product_size[" + index +
                        "][percentage]' min='0' max='100' value='" +
                        discount +
                        "' required>" +
                        "<span class='input-group-text bg-default p-2'>%</span>" +
                        "</div>" +
                        "</td>");
                } else {
                    discount = 0;
                    td_discount = $("<td>" +
                        "<div class='d-flex'>" +
                        "<input type='number' class='form-control' name='product_size[" + index +
                        "][percentage]' min='0' max='100' value='" +
                        discount +
                        "' required>" +
                        "<span class='input-group-text bg-default p-2'>%</span>" +
                        "</div>" +
                        "</td>");
                }

                let td_del = $(
                    "<td align='center'>" +
                    "<button type='button' class='delete-row btn btn-sm btn-danger' value='Delete'>Delete</button>" +
                    "<input type='hidden' class='form-control' name='product_item_check[]' value='" +
                    size +
                    "'>" +
                    "</td>"
                );

                // Append Tr Element
                (tr.append(td_size).append(td_weight).append(td_stock).append(td_sell_price)
                    .append(td_discount).append(td_del)).insertAfter(form_product)

                // Append To Table
                $("#product_size tbody").append(tr);

                // Reset Field Value
                $('#size').val('');
                $('#weight').val('');
                $('#stock_item').val('');
                $('#sell_price').val('');
                $('#discount').val('');
            } else {
                sweetAlertWarning('Please Complete The Record!');
            }
        }
    }

    // Find and remove selected table rows
    $("table#product_size").on("click", ".delete-row", function(event) {
        $(this).closest("tr").remove();
    });

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
                    url: '{{ url('product') }}/' + id,
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
