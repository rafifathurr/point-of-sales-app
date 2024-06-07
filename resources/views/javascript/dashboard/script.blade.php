<script type="text/javascript">
    let failed = false;

    function dashboardSalesOrder() {
        let token = $('meta[name="csrf-token"]').attr('content');
        let year = $('#sales_order_year').val();
        let month = $('#sales_order_month').val();

        $.ajax({
            url: '{{ url('dashboard/sales-order') }}',
            type: 'POST',
            cache: false,
            data: {
                _token: token,
                month: month,
                year: year,
            },
            success: function(data) {
                failed = false;
                salesOrderWidget(data);
            },
            error: function(xhr, error, code) {
                failed = true;
                // sweetAlertError(xhr.responseJSON.message);
            }
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

    function dashboardProduct() {
        let token = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: '{{ url('dashboard/product') }}',
            type: 'POST',
            cache: false,
            data: {
                _token: token,
            },
            success: function(data) {
                failed = false;
                productWidget(data);
            },
            error: function(xhr, error, code) {
                failed = true;
                // sweetAlertError(xhr.responseJSON.message);
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
        let token = $('meta[name="csrf-token"]').attr('content');
        let year = $('#stock_year').val();
        let month = $('#stock_month').val();

        $.ajax({
            url: '{{ url('dashboard/stock') }}',
            type: 'POST',
            cache: false,
            data: {
                _token: token,
                month: month,
                year: year,
            },
            success: function(data) {
                failed = false;
                stockWidget(data);
            },
            error: function(xhr, error, code) {
                failed = true;
                // sweetAlertError(xhr.responseJSON.message);
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
                type: 'POST',
                cache: false,
                data: {
                    _token: token,
                    month: month,
                    year: year,
                },
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
        let token = $('meta[name="csrf-token"]').attr('content');
        let year = $('#stock_year').val();
        let month = $('#stock_month').val();

        $.ajax({
            xhrFields: {
                responseType: 'blob',
            },
            url: '{{ url('dashboard/stock/export') }}',
            type: 'POST',
            cache: false,
            data: {
                _token: token,
                month: month,
                year: year,
            },
            success: function(data) {
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

    if (failed) {
        sweetAlertError("Network Unstable");
    }
</script>
