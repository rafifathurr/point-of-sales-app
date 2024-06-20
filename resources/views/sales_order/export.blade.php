<!DOCTYPE html>
<html>

<head>
    <title>Export Excel</title>
</head>

<body>
    <table width="100%">
        <thead>
            <tr>
                <th colspan="10" style="text-align:center;">
                    <h3>Report Sales Order Of {{ $sales_order['month'] . ' ' . $sales_order['year'] }}
                    </h3>
                </th>
            </tr>
        </thead>
        <thead>
            <tr>
                <th>
                    No
                </th>
                <th>
                    No. Invoice
                </th>
                <th>
                    Date Time
                </th>
                <th>
                    Purchase Type
                </th>
                <th>
                    Payment Method
                </th>
                <th>
                    Products
                </th>
                <th>
                    Sell Price
                </th>
                <th>
                    Capital Price
                </th>
                <th>
                    Discount
                </th>
                <th>
                    Profit
                </th>
                <th>
                    Total Sell Price
                </th>
            </tr>
        </thead>
        <tbody>
            @php
                $sell_price = 0;
                $capital_price = 0;
                $discount_price = 0;
                $total_profit_price = 0;
                $total_sell_price = 0;
            @endphp
            @foreach ($sales_order['data'] as $index => $sales_order_data)
                @php
                    $sell_price += intval($sales_order_data['total_sell_price']);
                    $capital_price += intval($sales_order_data['total_capital_price']);
                    $discount_price += intval($sales_order_data['discount_price']);
                    $total_profit_price += intval($sales_order_data['grand_profit_price']);
                    $total_sell_price += intval($sales_order_data['grand_sell_price']);
                @endphp
                @foreach ($sales_order_data['sales_order_item'] as $product_index => $sales_order_item_data)
                    <tr>
                        @if ($product_index === 0)
                            <td rowspan="{{ count($sales_order_data['sales_order_item']) }}">
                                {{ $index + 1 }}
                            </td>
                            <td style="text-align:left" rowspan="{{ count($sales_order_data['sales_order_item']) }}">
                                {{ $sales_order_data['invoice_number'] }}
                            </td>
                            <td rowspan="{{ count($sales_order_data['sales_order_item']) }}">
                                {{ date('d F Y H:i:s', strtotime($sales_order_data['created_at'])) }}
                            </td>
                            <td rowspan="{{ count($sales_order_data['sales_order_item']) }}">
                                {{ $sales_order_data['type'] == 0 ? 'Offline' : 'Online' }}
                            </td>
                            <td rowspan="{{ count($sales_order_data['sales_order_item']) }}">
                                {{ $sales_order_data['payment_method']['name'] }}
                            </td>
                        @endif
                        <td>
                            {{ $sales_order_item_data['product_size']['product']['name'] . ' - ' . $sales_order_item_data['product_size']['size'] . ' ' . $sales_order_item_data['qty'] . ' Pcs' }}
                        </td>
                        @if ($product_index === 0)
                            <td style="text-align:right" rowspan="{{ count($sales_order_data['sales_order_item']) }}">
                                Rp. {{ number_format($sales_order_data['total_sell_price'], 0, ',', '.') }},-
                            </td>
                            <td style="text-align:right" rowspan="{{ count($sales_order_data['sales_order_item']) }}">
                                Rp. {{ number_format($sales_order_data['total_capital_price'], 0, ',', '.') }},-
                            </td>
                            <td style="text-align:right" rowspan="{{ count($sales_order_data['sales_order_item']) }}">
                                Rp. {{ number_format($sales_order_data['discount_price'], 0, ',', '.') }},-
                            </td>
                            <td style="text-align:right" rowspan="{{ count($sales_order_data['sales_order_item']) }}">
                                Rp. {{ number_format($sales_order_data['grand_profit_price'], 0, ',', '.') }},-
                            </td>
                            <td style="text-align:right" rowspan="{{ count($sales_order_data['sales_order_item']) }}">
                                Rp. {{ number_format($sales_order_data['grand_sell_price'], 0, ',', '.') }},-
                            </td>
                        @endif
                    </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td style="text-align:center" colspan="6">
                    <b>Total</b>
                </td>
                <td style="text-align:right">
                    Rp. {{ number_format($sell_price, 0, ',', '.') }},-
                </td>
                <td style="text-align:right">
                    Rp. {{ number_format($capital_price, 0, ',', '.') }},-
                </td>
                <td style="text-align:right">
                    Rp. {{ number_format($discount_price, 0, ',', '.') }},-
                </td>
                <td style="text-align:right">
                    Rp. {{ number_format($total_profit_price, 0, ',', '.') }},-
                </td>
                <td style="text-align:right">
                    Rp. {{ number_format($total_sell_price, 0, ',', '.') }},-
                </td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
