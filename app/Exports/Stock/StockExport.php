<?php

namespace App\Exports\Stock;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StockExport implements FromView
{
    public $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('stock.export', [
            'stock' => $this->data
        ]);
    }
}
