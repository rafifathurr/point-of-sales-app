<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Invoice #{{ $sales_order->invoice_number }}</title>

    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .invoice-box.rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .invoice-box.rtl table {
            text-align: right;
        }

        .invoice-box.rtl table tr td:nth-child(2) {
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="3">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/renata_label_icon.png'))) }}"
                                    style="width: 100%; max-width: 300px" />
                            </td>

                            <td>
                                <b>Invoice</b> : #{{ $sales_order->invoice_number }}<br />
                                <b>Date & Time</b>: {{ date('d M Y H:i:s', strtotime($sales_order->created_at)) }}<br />
                                <b>Created</b>: {{ $sales_order->createdBy->name }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="3">
                    <table>
                        <tr>
                            <td>
                                Renata Label.<br />
                                Jakarta, Indonesia<br />
                            </td>

                            <td>
                                {{ $sales_order->customer->name ?? '' }}<br />
                                {{ $sales_order->customer->phone ?? '' }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td colspan="3">Purchase Type</td>
            </tr>

            <tr class="details">
                <td colspan="3">{{ $sales_order->type == 0 ? 'Offline' : 'Online' }}</td>
            </tr>

            <tr class="heading">
                <td colspan="3">Payment Method</td>
            </tr>

            <tr class="details">
                <td colspan="3">{{ $sales_order->paymentMethod->name }}</td>
            </tr>

            <tr class="heading">
                <td>Product</td>
                <td align="center">Discount</td>
                <td align="center">Price</td>
            </tr>

            @foreach ($sales_order->salesOrderItem as $sales_order_item)
                <tr class="item">
                    <td>
                        {{ $sales_order_item->productSize->product->name . ' - ' . $sales_order_item->productSize->size . ' ' . $sales_order_item->qty . ' Pcs' }}
                    </td>
                    <td align="right">
                        Rp. {{ number_format($sales_order_item->discount_price, 0, ',', '.') }} ,-
                    </td>
                    <td align="right">
                        Rp. {{ number_format($sales_order_item->total_sell_price, 0, ',', '.') }}
                        ,-
                    </td>
                </tr>
            @endforeach

            <tr class="total">
                <td colspan="2"><b>Total</b></td>
                <td align="right">
                    Rp. {{ number_format($sales_order->grand_sell_price, 0, ',', '.') }}
                    ,-
                </td>
            </tr>

        </table>
    </div>
</body>

</html>
