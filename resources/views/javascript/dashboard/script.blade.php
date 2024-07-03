<script type="text/javascript">
    let failed = false;

    function dashboardSalesOrder() {
        let year = $('#sales_order_year').val();
        let month = $('#sales_order_month').val();

        $.ajax({
            url: '{{ url('dashboard/sales-order') }}',
            type: 'GET',
            cache: false,
            data: {
                month: month,
                year: year,
            },
            success: function(data) {
                failed = false;
                salesOrderWidget(data);
            },
            error: function(xhr, error, code) {
                failed = true;
            }
        });

        $('#dt-sales-order').DataTable({
            autoWidth: false,
            responsive: true,
            processing: true,
            serverSide: true,
            orderable: false,
            destroy: true,
            ajax: {
                url: '{{ url('dashboard/sales-order/datatable') }}',
                type: 'GET',
                cache: false,
                data: {
                    month: month,
                    year: year,
                },
                error: function(xhr, error, code) {
                    failed = true;
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
                    data: 'total_sell_price',
                    defaultContent: '-',
                },
                {
                    data: 'total_capital_price',
                    defaultContent: '-',
                },
                {
                    data: 'discount_price',
                    defaultContent: '-',
                },
                {
                    data: 'grand_profit_price',
                    defaultContent: '-',
                },
                {
                    data: 'grand_sell_price',
                    defaultContent: '-',
                },
            ],
            order: [
                [2, 'desc']
            ]
        });
    }

    function salesOrderWidget(data) {
        $('#total_income').html(data.total_income);
        $('#total_profit').html(data.total_profit);
        $('#total_sales_order').html(data.total_sales_order);
        $('#total_product_sold').html(data.total_product_sold);

        var areaData = {
            labels: data.days,
            datasets: [{
                    data: data.online_data,
                    backgroundColor: '#4747A1',
                    borderWidth: 2,
                    label: "Online"
                },
                {
                    data: data.offline_data,
                    backgroundColor: '#F09397',
                    borderWidth: 2,
                    label: "Offline"
                }
            ]
        };
        var areaOptions = {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                filler: {
                    propagate: false
                }
            },
            scales: {
                xAxes: [{
                    display: true,
                    ticks: {
                        display: true,
                        padding: 10,
                        fontColor: "#6C7383"
                    },
                    gridLines: {
                        display: false,
                        drawBorder: false,
                        color: 'transparent',
                        zeroLineColor: '#eeeeee'
                    }
                }],
                yAxes: [{
                    display: true,
                    ticks: {
                        display: true,
                        autoSkip: false,
                        maxRotation: 0,
                        max: data.total_sales_order,
                        padding: 18,
                        fontColor: "#6C7383"
                    },
                    gridLines: {
                        display: true,
                        color: "#f2f2f2",
                        drawBorder: false
                    }
                }]
            },
            legend: {
                display: true
            },
            tooltips: {
                enabled: true
            },
            elements: {
                line: {
                    tension: .35
                },
                point: {
                    radius: 0
                }
            }
        }
        var revenueChartCanvas = $("#purchase-type-chart").get(0).getContext("2d");
        var revenueChart = new Chart(revenueChartCanvas, {
            type: 'bar',
            data: areaData,
            options: areaOptions
        });

        var SalesChartCanvas = $("#sales-chart").get(0).getContext("2d");
        var SalesChart = new Chart(SalesChartCanvas, {
            type: 'bar',
            data: {
                labels: data.days,
                datasets: [{
                    data: data.sales_data,
                    backgroundColor: '#7978e9',
                    label: "Total Sales Order"
                }, ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    filler: {
                        propagate: false
                    }
                },
                scales: {
                    xAxes: [{
                        display: true,
                        ticks: {
                            display: true,
                            padding: 10,
                            fontColor: "#6C7383"
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false,
                            color: 'transparent',
                            zeroLineColor: '#eeeeee'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        ticks: {
                            display: true,
                            autoSkip: false,
                            maxRotation: 0,
                            max: data.total_sales_order,
                            padding: 18,
                            fontColor: "#6C7383"
                        },
                        gridLines: {
                            display: true,
                            color: "#f2f2f2",
                            drawBorder: false
                        }
                    }]
                },
                legend: {
                    display: false
                },
                tooltips: {
                    enabled: true
                },
                elements: {
                    line: {
                        tension: .35
                    },
                    point: {
                        radius: 0
                    }
                }
            },
        });
    }

    function exportSalesOrder() {
        let year = $('#sales_order_year').val();
        let month = $('#sales_order_month').val();

        sweetAlertProcess();

        $.ajax({
            xhrFields: {
                responseType: 'blob',
            },
            url: '{{ url('dashboard/sales-order/export') }}',
            type: 'GET',
            cache: false,
            data: {
                month: month,
                year: year,
            },
            success: function(data) {
                Swal.close();
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(data);
                link.download = 'Report_Sales_Order_' + month + '_' + year + '.xlsx';
                link.click();
            },
            error: function(xhr, error, code) {
                sweetAlertError(xhr.responseJSON.message);
            }
        });
    }

    function dashboardProduct() {

        $.ajax({
            url: '{{ url('dashboard/product') }}',
            type: 'GET',
            cache: false,
            success: function(data) {
                failed = false;
                productWidget(data);
            },
            error: function(xhr, error, code) {
                failed = true;
            }
        });
    }

    function productWidget(data) {
        $('#total_product').html(data.total_product);
        $('#total_active_product').html(data.total_active_product);
        $('#total_inactive_product').html(data.total_inactive_product);
        $('#total_category_product').html(data.total_category_product);
    }

    function dashboardStock() {
        let year = $('#stock_year').val();
        let month = $('#stock_month').val();

        $.ajax({
            url: '{{ url('dashboard/stock') }}',
            type: 'GET',
            cache: false,
            data: {
                month: month,
                year: year,
            },
            success: function(data) {
                failed = false;
                stockWidget(data);
            },
            error: function(xhr, error, code) {
                failed = true;
            }
        });

        $('#dt-stock').DataTable({
            autoWidth: false,
            responsive: true,
            processing: true,
            serverSide: true,
            orderable: false,
            destroy: true,
            ajax: {
                url: '{{ url('dashboard/stock/datatable') }}',
                type: 'GET',
                cache: false,
                data: {
                    month: month,
                    year: year,
                },
                error: function(xhr, error, code) {
                    failed = true;
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
                    data: 'product',
                    defaultContent: '-',
                },
                {
                    data: 'qty',
                    defaultContent: '-',
                },
                {
                    data: 'description',
                    defaultContent: '-',
                }
            ],
            order: [
                [1, 'desc']
            ]
        });
    }

    function stockWidget(data) {
        $('#total_stock_in').html(data.total_stock_in);
        $('#total_stock_out').html(data.total_stock_out);
        $('#total_qty_stock_in').html(data.total_qty_stock_in);
        $('#total_qty_stock_out').html(data.total_qty_stock_out);

        var areaData = {
            labels: data.days,
            datasets: [{
                    data: data.stock_in_data,
                    backgroundColor: '#57b657',
                    borderWidth: 2,
                    label: "Stock In"
                },
                {
                    data: data.stock_out_data,
                    backgroundColor: '#ff4747',
                    borderWidth: 2,
                    label: "Stock Out"
                }
            ]
        };
        var areaOptions = {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                filler: {
                    propagate: false
                }
            },
            scales: {
                xAxes: [{
                    display: true,
                    ticks: {
                        display: true,
                        padding: 10,
                        fontColor: "#6C7383"
                    },
                    gridLines: {
                        display: false,
                        drawBorder: false,
                        color: 'transparent',
                        zeroLineColor: '#eeeeee'
                    }
                }],
                yAxes: [{
                    display: true,
                    ticks: {
                        display: true,
                        autoSkip: false,
                        maxRotation: 0,
                        max: data.total_sales_order,
                        padding: 18,
                        fontColor: "#6C7383"
                    },
                    gridLines: {
                        display: true,
                        color: "#f2f2f2",
                        drawBorder: false
                    }
                }]
            },
            legend: {
                display: true
            },
            tooltips: {
                enabled: true
            },
            elements: {
                line: {
                    tension: .35
                },
                point: {
                    radius: 0
                }
            }
        }
        var revenueChartCanvas = $("#stock-chart").get(0).getContext("2d");
        var revenueChart = new Chart(revenueChartCanvas, {
            type: 'bar',
            data: areaData,
            options: areaOptions
        });

        var areaData = {
            labels: data.days,
            datasets: [{
                    data: data.stock_in_qty_data,
                    backgroundColor: '#57b657',
                    borderWidth: 2,
                    label: "Stock In"
                },
                {
                    data: data.stock_out_qty_data,
                    backgroundColor: '#ff4747',
                    borderWidth: 2,
                    label: "Stock Out"
                }
            ]
        };
        var areaOptions = {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                filler: {
                    propagate: false
                }
            },
            scales: {
                xAxes: [{
                    display: true,
                    ticks: {
                        display: true,
                        padding: 10,
                        fontColor: "#6C7383"
                    },
                    gridLines: {
                        display: false,
                        drawBorder: false,
                        color: 'transparent',
                        zeroLineColor: '#eeeeee'
                    }
                }],
                yAxes: [{
                    display: true,
                    ticks: {
                        display: true,
                        autoSkip: false,
                        maxRotation: 0,
                        padding: 18,
                        fontColor: "#6C7383"
                    },
                    gridLines: {
                        display: true,
                        color: "#f2f2f2",
                        drawBorder: false
                    }
                }]
            },
            legend: {
                display: true
            },
            tooltips: {
                enabled: true
            },
            elements: {
                line: {
                    tension: .35
                },
                point: {
                    radius: 0
                }
            }
        }
        var revenueChartCanvas = $("#stock-qty-chart").get(0).getContext("2d");
        var revenueChart = new Chart(revenueChartCanvas, {
            type: 'bar',
            data: areaData,
            options: areaOptions
        });
    }

    function exportStock() {
        let year = $('#stock_year').val();
        let month = $('#stock_month').val();

        sweetAlertProcess();

        $.ajax({
            xhrFields: {
                responseType: 'blob',
            },
            url: '{{ url('dashboard/stock/export') }}',
            type: 'GET',
            cache: false,
            data: {
                month: month,
                year: year,
            },
            success: function(data) {
                Swal.close();
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(data);
                link.download = 'Report_Stock_' + month + '_' + year + '.xlsx';
                link.click();
            },
            error: function(xhr, error, code) {
                sweetAlertError(xhr.responseJSON.message);
            }
        });
    }

    function dashboardCoa() {
        let year = $('#coa_year').val();
        let month = $('#coa_month').val();

        $.ajax({
            url: '{{ url('dashboard/coa') }}',
            type: 'GET',
            cache: false,
            data: {
                month: month,
                year: year,
            },
            success: function(data) {
                failed = false;
                coaWidget(data);
            },
            error: function(xhr, error, code) {
                failed = true;
            }
        });

        $('#dt-coa').DataTable({
            autoWidth: false,
            responsive: true,
            processing: true,
            serverSide: true,
            orderable: false,
            destroy: true,
            ajax: {
                url: '{{ url('dashboard/coa/datatable') }}',
                type: 'GET',
                cache: false,
                data: {
                    month: month,
                    year: year,
                },
                error: function(xhr, error, code) {
                    failed = true;
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
                    data: 'type',
                    defaultContent: '-',
                },
                {
                    data: 'balance',
                    defaultContent: '-',
                },
            ],
            order: [
                [0, 'asc']
            ]
        });
    }

    function coaWidget(data) {
        $('#total_debt_coa').html(data.widget.total_debt_coa);
        $('#total_credit_coa').html(data.widget.total_credit_coa);
        $('#nominal_debt_coa').html(data.widget.nominal_debt_coa);
        $('#nominal_credit_coa').html(data.widget.nominal_credit_coa);

        var areaData = {
            labels: data.perday.days,
            datasets: [{
                data: data.perday.coa_debt_data,
                backgroundColor: '#ff4747',
                borderWidth: 2,
                label: "Debt"
            }, {
                data: data.perday.coa_credit_data,
                backgroundColor: '#57b657',
                borderWidth: 2,
                label: "Credit"
            }]
        };
        var areaOptions = {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                filler: {
                    propagate: false
                }
            },
            scales: {
                xAxes: [{
                    display: true,
                    ticks: {
                        display: true,
                        padding: 10,
                        fontColor: "#6C7383"
                    },
                    gridLines: {
                        display: false,
                        drawBorder: false,
                        color: 'transparent',
                        zeroLineColor: '#eeeeee'
                    }
                }],
                yAxes: [{
                    display: true,
                    ticks: {
                        display: true,
                        autoSkip: false,
                        maxRotation: 0,
                        max: data.total_sales_order,
                        padding: 18,
                        fontColor: "#6C7383"
                    },
                    gridLines: {
                        display: true,
                        color: "#f2f2f2",
                        drawBorder: false
                    }
                }]
            },
            legend: {
                display: true
            },
            tooltips: {
                enabled: true
            },
            elements: {
                line: {
                    tension: .35
                },
                point: {
                    radius: 0
                }
            }
        }
        var revenueChartCanvas = $("#coa-chart").get(0).getContext("2d");
        var revenueChart = new Chart(revenueChartCanvas, {
            type: 'bar',
            data: areaData,
            options: areaOptions
        });

        var coaChartCanvas = $("#coa-type-chart").get(0).getContext("2d");
        var coaChart = new Chart(coaChartCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Debt', 'Credit'],
                datasets: [{
                    data: [data.percentage.coa_debt_data, data.percentage.coa_credit_data, ],
                    backgroundColor: ['#ff4747', '#57b657']
                }]
            },
            options: {
                maintainAspectRatio: true,
                responsive: true,
                legend: {
                    display: true,
                },
            },
        });
    }

    function exportCoa() {
        let year = $('#coa_year').val();
        let month = $('#coa_month').val();

        sweetAlertProcess();

        $.ajax({
            xhrFields: {
                responseType: 'blob',
            },
            url: '{{ url('dashboard/coa/export') }}',
            type: 'GET',
            cache: false,
            data: {
                month: month,
                year: year,
            },
            success: function(data) {
                Swal.close();
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(data);
                link.download = 'Report_Chart_of_Account_' + month + '_' + year + '.xlsx';
                link.click();
            },
            error: function(xhr, error, code) {
                sweetAlertError(xhr.responseJSON.message);
            }
        });
    }

    if (failed) {
        sweetAlertError("Network Unstable");
    }
</script>
