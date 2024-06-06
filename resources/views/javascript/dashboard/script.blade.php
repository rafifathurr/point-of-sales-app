<script type="text/javascript">
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
                salesOrderWidget(data);
            },
            error: function(xhr, error, code) {
                console.log(xhr, error, code);
                sweetAlertError(xhr.responseJSON.message);
            }
        });
    }

    function salesOrderWidget(data) {
        $('#total_income').html(data.total_income);
        $('#total_profit').html(data.total_profit);
        $('#total_sales_order').html(data.total_sales_order);
        $('#total_product_sold').html(data.total_product_sold);

        if ($("#purchase-type-chart").length) {
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
                            stepSize: 10,
                            min: 0,
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
        }

        if ($("#sales-chart").length) {
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
                                stepSize: 10,
                                min: 0,
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
                productWidget(data);
            },
            error: function(xhr, error, code) {
                console.log(xhr, error, code);
                sweetAlertError(xhr.responseJSON.message);
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
                stockWidget(data);
            },
            error: function(xhr, error, code) {
                console.log(xhr, error, code);
                sweetAlertError(xhr.responseJSON.message);
            }
        });
    }

    function stockWidget(data) {
        $('#total_stock_in').html(data.total_stock_in);
        $('#total_stock_out').html(data.total_stock_out);
        $('#total_qty_stock_in').html(data.total_qty_stock_in);
        $('#total_qty_stock_out').html(data.total_qty_stock_out);

        if ($("#stock-chart").length) {
            var areaData = {
                labels: data.days,
                datasets: [{
                        data: data.stock_in_data,
                        borderColor: '#57b657',
                        fill: false,
                        borderWidth: 2,
                        label: "Stock In"
                    },
                    {
                        data: data.stock_out_data,
                        borderColor: '#ff4747',
                        fill: false,
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
                            stepSize: 10,
                            min: 0,
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
                type: 'line',
                data: areaData,
                options: areaOptions
            });
        }

        if ($("#stock-qty-chart").length) {
            var areaData = {
                labels: data.days,
                datasets: [{
                        data: data.stock_in_qty_data,
                        borderColor: '#57b657',
                        fill: false,
                        borderWidth: 2,
                        label: "Stock In"
                    },
                    {
                        data: data.stock_out_qty_data,
                        borderColor: '#ff4747',
                        fill: false,
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
                            stepSize: 100,
                            min: 0,
                            max: 500,
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
                type: 'line',
                data: areaData,
                options: areaOptions
            });
        }
    }
</script>
