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
                    <h3>Report Chart of Account Of {{ $coa['month'] . ' ' . $coa['year'] }}
                    </h3>
                </th>
            </tr>
        </thead>
        <thead>
            <tr>
                <th>No</th>
                <th>Date</th>
                <th>Account Number</th>
                <th>Name</th>
                <th>Debt</th>
                <th>Credit</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($coa['data'] as $index => $coa_data)
                <tr>
                    <td>
                        {{ $index + 1 }}
                    </td>
                    <td>
                        {{ date('d M Y', strtotime($coa_data['date'])) }}
                    </td>
                    <td>
                        {{ $coa_data['account_number']['account_number'] }}
                    </td>
                    <td>
                        {{ $coa_data['name'] }}
                    </td>
                    <td style="text-align:right">
                        @if ($coa_data['type'] == 0)
                            {{ 'Rp. ' . number_format($coa_data['balance'], 0, ',', '.') . ',-' }}
                        @endif
                    </td>
                    <td style="text-align:right">
                        @if ($coa_data['type'] == 1)
                            {{ 'Rp. ' . number_format($coa_data['balance'], 0, ',', '.') . ',-' }}
                        @endif
                    </td>
                    <td>
                        {{ $coa_data['description'] }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
