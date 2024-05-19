<script type="text/javascript">
    $("form").submit(function(e) {
        e.preventDefault();
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
    });

    function dataTable() {
        const url = $('#url_dt').val();
        $('#dt-stock').DataTable({
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
                    data: 'product',
                    defaultContent: '-',
                },
                {
                    data: 'date',
                    defaultContent: '-',
                },
                {
                    data: 'qty',
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

    function getProduct(product) {
        $.get("{{ route('product.getProductSize') }}", {
            product: product,
        }).done(function(data) {
            $("#qty").removeAttr('readonly');
            $("#qty").attr({
                "max": data.stock
            });
        }).fail(function(xhr, status, error) {
            sweetAlertError(error);
        });
    }

    function validationQty(element) {
        if (element.value < element.min && element.value != '') {
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
    }

    function destroyRecord(id) {
        const url = $('#url_destroy').val();
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
                    url: url + '/' + id,
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
