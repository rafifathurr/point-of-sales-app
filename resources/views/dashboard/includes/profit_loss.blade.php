<div class="table-responsive">
    <table class="table table-bordered datatable" id="dt-profit-loss">
        <thead>
            <tr>
                <th width="5%">
                    #
                </th>
                <th>
                    Name
                </th>
                <th>
                    Date
                </th>
                <th>
                    Balance
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    1
                </td>
                <td>
                    Profit Balance
                </td>
                <td>
                    &nbsp;
                </td>
                <td align="right">
                    Rp. {{ number_format($total_profit, 0, ',', '.') }},-
                </td>
            </tr>
            @foreach ($coa_debt as $index => $item)
                <tr>
                    <td>
                        {{ $index + 2 }}
                    </td>
                    <td>
                        {{ $item->name }}
                    </td>
                    <td>
                        {{ date('d F Y', strtotime($item->date)) }}
                    </td>
                    <td align="right">
                        Rp. {{ number_format($item->balance, 0, ',', '.') }},-
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td style="text-align:center" colspan="3">
                    <b>Total</b>
                </td>
                <td align="right">
                    Rp. {{ number_format($total_profit_loss, 0, ',', '.') }},-
                </td>
            </tr>
        </tfoot>
    </table>
</div>
