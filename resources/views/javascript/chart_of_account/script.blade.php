<script type="text/javascript">
    $("#form-store").submit(function(e) {
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
                $('#form-store').unbind('submit').submit();
            }
        })
    });

    $("#form-edit").submit(function(e) {
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
                $('#form-edit').unbind('submit').submit();
            }
        })
    });

    function dataTable() {
        const url = $('#url_dt').val();
        $('#dt-coa').DataTable({
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
                    data: 'date',
                    defaultContent: '-',
                },
                {
                    data: 'account_number',
                    defaultContent: '-',
                },
                {
                    data: 'name',
                    defaultContent: '-',
                },
                {
                    data: 'debt',
                    className: 'text-right',
                    defaultContent: '-',
                },
                {
                    data: 'credit',
                    className: 'text-right',
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
            footerCallback: function(row, data, start, end, display) {
                let total_debt = 0;
                let total_credit = 0;
                data.forEach(function(element) {
                    total_debt += element.num_debt;
                    total_credit += element.num_credit;
                });

                $('#total_debt').html(currencyFormat(total_debt));
                $('#total_credit').html(currencyFormat(total_credit));
                $('#accumulation_coa').html(currencyFormat(total_credit - total_debt));
            },
            order: [
                [0, 'desc']
            ]
        });
    }

    function currencyFormat(value) {
        return value.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
    }

    function openModal(category, id) {
        sweetAlertProcess();
        $.get('{{ url('coa') }}/' + id, {}).done(function(data) {
            Swal.close();
            $('#' + category).modal('show');
            $('#id_' + category).html(id);
            let url_update = $('#url_edit').val() + '/' + id;
            $('#form-edit').attr('action', url_update);
            Object.entries(data).forEach(([key, value], index) => {
                $('#' + key + '_' + category).val(value);
            });
        }).fail(function(xhr, status, error) {
            sweetAlertError(error);
        });
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
                    url: '{{ url('coa') }}/' + id,
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
