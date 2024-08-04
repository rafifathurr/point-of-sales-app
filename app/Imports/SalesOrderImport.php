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
            /**
             * Get Record After Header
             */
            if ($index > 0) {

                /**
                 * Validation Excel not Empty
                 */
                if ($row[0] != null) {
                    /**
                     * Generate Slug Product and Product Size
                     */
                    $explode_name_product = explode(' - ', $row[0]);
                    $product_slug = Str::slug($explode_name_product[0]);
                    $product_size_slug = Str::slug($explode_name_product[1]);

                    /**
                     * Validation Array with Spesific Index of Invoice
                     */
                    if (empty($this->data[$row[2]])) {
                        $total_sell_price_order = 0;
                        $discount_price_order = 0;
                        $grand_sell_price_order = 0;

                        /**
                         * Setting Up new Array Record from Row Excel
                         */
                        $this->data[$row[2]] = [
                            'invoice' => $row[2],
                            'date' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(intval($row[1]))->format('Y-m-d'),
                            'type' => 1,
                            'total_sell_price' => 0,
                            'discount_price' => 0,
                            'grand_sell_price' => 0,
                            'total_point' => 0,
                            'sales_order_item' => [],
                        ];

                        /**
                         * Get Sell Price with multiply Qty
                         */
                        $sell_price = intval($row[4]) * intval($row[5]);

                        /**
                         * Validation Sales Order Has Service Fee
                         */
                        if ($row[7] != null) {
                            /**
                             * Calculation Total Sell Price by minus Discount and Service Fee
                             */
                            $total_sell_price = (intval($row[4]) - intval($row[6])) * intval($row[5]) - intval($row[7]);
                        } else {
                            /**
                             * Calculation Total Sell Price by minus Discount
                             */
                            $total_sell_price = (intval($row[4]) - intval($row[6])) * intval($row[5]);
                        }

                        /**
                         * Update Record of Array
                         */
                        $this->data[$row[2]]['sales_order_item'][$product_slug][$product_size_slug] = [
                            'product_size_slug' => $product_size_slug,
                            'qty' => $row[5],
                            'capital_price' => $row[3],
                            'sell_price' => $row[4],
                            'discount_price' => $row[6],
                            'total_sell_price' => $total_sell_price,
                        ];

                        /**
                         * Total All Accumulation Price
                         */
                        $total_sell_price_order += $sell_price;
                        $discount_price_order += intval($row[6]) * intval($row[5]);
                        $grand_sell_price_order += $total_sell_price;

                        /**
                         * Update Record of Array
                         */
                        $this->data[$row[2]]['total_sell_price'] = $total_sell_price_order;
                        $this->data[$row[2]]['discount_price'] = $discount_price_order;
                        $this->data[$row[2]]['grand_sell_price'] = $grand_sell_price_order;
                    } else {
                        /**
                         * Get Sell Price with multiply Qty
                         */
                        $sell_price = intval($row[4]) * intval($row[5]);

                        /**
                         * Validation Sales Order Has Service Fee
                         */
                        if ($row[7] != null) {
                            /**
                             * Calculation Total Sell Price by minus Discount and Service Fee
                             */
                            $total_sell_price = (intval($row[4]) - intval($row[6])) * intval($row[5]) - intval($row[7]);
                        } else {
                            /**
                             * Calculation Total Sell Price by minus Discount
                             */
                            $total_sell_price = (intval($row[4]) - intval($row[6])) * intval($row[5]);
                        }

                        $this->data[$row[2]]['sales_order_item'][$product_slug][$product_size_slug] = [
                            'product_size_slug' => $product_size_slug,
                            'qty' => $row[5],
                            'capital_price' => $row[3],
                            'sell_price' => $row[4],
                            'discount_price' => $row[6],
                            'total_sell_price' => $total_sell_price,
                        ];

                        /**
                         * Total All Accumulation Price
                         */
                        $total_sell_price_order += $sell_price;
                        $discount_price_order += intval($row[6]) * intval($row[5]);
                        $grand_sell_price_order += $total_sell_price;

                        /**
                         * Update Record of Array
                         */
                        $this->data[$row[2]]['total_sell_price'] = $total_sell_price_order;
                        $this->data[$row[2]]['discount_price'] = $discount_price_order;
                        $this->data[$row[2]]['grand_sell_price'] = $grand_sell_price_order;
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
