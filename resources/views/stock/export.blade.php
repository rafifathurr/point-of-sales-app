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
                    <h3>Report Stock Of {{ $stock['month'] . ' ' . $stock['year'] }}
                    </h3>
                </th>
            </tr>
        </thead>
        <thead>
            <tr>
                <th>No</th>
                <th>Date</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stock['data'] as $index => $stock_data)
                <tr>
                    <td>
                        {{ $index + 1 }}
                    </td>
                    <td>
                        {{ date('d M Y', strtotime($stock_data['date'])) }}
                    </td>
                    <td>
                        {{ $stock_data['product_size']['product']['name'] . ' - ' . $stock_data['product_size']['size'] }}
                    </td>
                    <td>
                        {{ $stock_data['qty'] }}
                    </td>
                    <td>
                        {{ $stock_data['description'] }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
