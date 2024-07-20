<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToArray;
use Illuminate\Support\Str;

class SalesOrderImport implements ToArray
{
    use Importable;
    private $data;

    public function __construct()
    {
        $this->data = [];
    }
    public function array(array $rows)
    {
        $total_sell_price_order = 0;
        $discount_price_order = 0;
        $grand_sell_price_order = 0;
        foreach ($rows as $index => $row) {
            if ($index > 0) {
                if ($row[0] != null) {
                    $explode_name_product = explode(' - ', $row[0]);

                    $product_slug = Str::slug($explode_name_product[0]);
                    $product_size_slug = Str::slug($explode_name_product[1]);

                    if (empty($this->data[$row[1]])) {
                        $total_sell_price_order = 0;
                        $discount_price_order = 0;
                        $grand_sell_price_order = 0;

                        $this->data[$row[1]] = [
                            'invoice' => $row[1],
                            'type' => 1,
                            'total_sell_price' => 0,
                            'discount_price' => 0,
                            'grand_sell_price' => 0,
                            'total_point' => 0,
                            'sales_order_item' => [],
                        ];

                        $sell_price = intval($row[3]) * intval($row[4]);

                        if ($row[6] != null) {
                            $total_sell_price = (intval($row[3]) - intval($row[5])) * intval($row[4]) - intval($row[6]);
                        } else {
                            $total_sell_price = (intval($row[3]) - intval($row[5])) * intval($row[4]);
                        }

                        $this->data[$row[1]]['sales_order_item'][$product_slug][$product_size_slug] = [
                            'product_size_slug' => $product_size_slug,
                            'qty' => $row[4],
                            'capital_price' => $row[2],
                            'sell_price' => $row[3],
                            'discount_price' => $row[5],
                            'total_sell_price' => $total_sell_price,
                        ];

                        $total_sell_price_order += $sell_price;
                        $discount_price_order += intval($row[5]) * intval($row[4]);
                        $grand_sell_price_order += $total_sell_price;
                        $this->data[$row[1]]['total_sell_price'] = $total_sell_price_order;
                        $this->data[$row[1]]['discount_price'] = $discount_price_order;
                        $this->data[$row[1]]['grand_sell_price'] = $grand_sell_price_order;
                    } else {
                        $sell_price = intval($row[3]) * intval($row[4]);

                        if ($row[6] != null) {
                            $total_sell_price = (intval($row[3]) - intval($row[5])) * intval($row[4]) - intval($row[6]);
                        } else {
                            $total_sell_price = (intval($row[3]) - intval($row[5])) * intval($row[4]);
                        }

                        $this->data[$row[1]]['sales_order_item'][$product_slug][$product_size_slug] = [
                            'product_size_slug' => $product_size_slug,
                            'qty' => $row[4],
                            'capital_price' => $row[2],
                            'sell_price' => $row[3],
                            'discount_price' => $row[5],
                            'total_sell_price' => $total_sell_price,
                        ];

                        $total_sell_price_order += $sell_price;
                        $discount_price_order += intval($row[5]) * intval($row[4]);
                        $grand_sell_price_order += $total_sell_price;
                        $this->data[$row[1]]['total_sell_price'] = $total_sell_price_order;
                        $this->data[$row[1]]['discount_price'] = $discount_price_order;
                        $this->data[$row[1]]['grand_sell_price'] = $grand_sell_price_order;
                    }
                }
            }
        }
    }

    public function getArray(): array
    {
        return $this->data;
    }
}
