<?php

namespace App\Exports\ChartofAccount;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ChartofAccountExport implements FromView
{
    public $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('chart_of_account.export', [
            'coa' => $this->data
        ]);
    }
}
