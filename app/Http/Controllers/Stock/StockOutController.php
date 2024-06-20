<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\Product\ProductSize;
use App\Models\Product\StockInOut;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StockOutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /**
         * Datatable Route
         */
        $datatable_route = route('stock-out.dataTable');

        /**
         * Create Route
         */
        $create_route = route('stock-out.create');

        /**
         * Destroy Route
         */
        $destroy_route = url('stock-out');

        /**
         * Set Title of Stock Out
         */
        $title = 'Stock Out';

        /**
         * Create Access Based Role
         */
        $can_create = User::find(Auth::user()->id)->hasRole('admin');

        return view('stock.index', compact('datatable_route', 'create_route', 'destroy_route', 'title', 'can_create'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        /**
         * Get All Product
         */
        $product = ProductSize::with(['product'])
            ->whereNull('deleted_by')
            ->whereNull('deleted_at')
            ->whereNotNull('capital_price')
            ->where('stock', '>', 0)
            ->get();

        /**
         * Store Route
         */
        $store_route = route('stock-out.store');

        /**
         * Index Route
         */
        $index_route = route('stock-out.index');

        /**
         * Set Title of Stock Out
         */
        $title = 'Stock Out';

        /**
         * Stock In Status Classification
         */
        $stock_in_status = false;

        return view('stock.create', compact('product', 'store_route', 'index_route', 'title', 'stock_in_status'));
    }

    /**
     * Show datatable of resource.
     */
    public function dataTable()
    {
        /**
         * Get All Stock Out
         */
        $stock_out = StockInOut::with(['productSize.product'])
            ->where('type', 1)
            ->whereNull('deleted_by')
            ->whereNull('deleted_at')
            ->get();

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($stock_out)
            ->addIndexColumn()
            ->addColumn('product', function ($data) {
                /**
                 * Return Relation Product Size and Product
                 */
                return $data->productSize->product->name . ' - ' . $data->productSize->size;
            })
            ->addColumn('date', function ($data) {
                /**
                 * Return Format Date
                 */
                return date('d F Y', strtotime($data->date));
            })
            ->addColumn('qty', function ($data) {
                return '<span class="text-danger">- ' . $data->qty . ' Pcs</span>';
            })
            ->addColumn('action', function ($data) {
                $btn_action = '<div align="center">';
                $btn_action .= '<a href="' . route('stock-out.show', ['id' => $data->id]) . '" class="btn btn-sm btn-primary" title="Detail">Detail</a>';

                /**
                 * Validation Role Has Access Edit and Delete
                 */
                if (User::find(Auth::user()->id)->hasRole('admin')) {
                    $btn_action .= '<a href="' . route('stock-out.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-warning ml-2" title="Edit">Edit</a>';
                    $btn_action .= '<button class="btn btn-sm btn-danger ml-2" onclick="destroyRecord(' . $data->id . ')" title="Delete">Delete</button>';
                }
                $btn_action .= '</div>';
                return $btn_action;
            })
            ->only(['product', 'date', 'qty', 'action'])
            ->rawColumns(['qty', 'action'])
            ->make(true);

        return $dataTable;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            /**
             * Validation Request Body Variables
             */
            $request->validate([
                'product_size' => 'required',
                'date' => 'required',
                'qty' => 'required',
            ]);

            /**
             * Begin Transaction
             */
            DB::beginTransaction();

            /**
             * Create Stock Out Record
             */
            $stock_out = StockInOut::lockforUpdate()->create([
                'product_size_id' => $request->product_size,
                'qty' => $request->qty,
                'type' => 1,
                'date' => $request->date,
                'description' => $request->description,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ]);

            /**
             * Validation Create Stock Out Record
             */
            if ($stock_out) {
                /**
                 * Check Last Stock
                 */
                $product_size_stock_record = ProductSize::with(['product'])
                    ->where('id', $stock_out->product_size_id)
                    ->first();

                /**
                 * Check Empty Stock of Product
                 */
                $has_stock_product_record = ProductSize::where('product_id', $product_size_stock_record->product->id)
                    ->whereNull('deleted_by')
                    ->whereNull('deleted_at')
                    ->where('id', '!=', $stock_out->product_size_id)
                    ->where('stock', '>', 0)
                    ->get()
                    ->toArray();

                /**
                 * Calculation Stock
                 */
                $stock_out_result = intval($product_size_stock_record->stock) - intval($request->qty);

                /**
                 * Update Stock of Product
                 */
                $product_size_update = ProductSize::where('id', $stock_out->product_size_id)->update([
                    'stock' => $stock_out_result,
                    'updated_by' => Auth::user()->id,
                ]);

                /**
                 * Validation Update Stock of Product
                 */
                if ($product_size_update) {
                    /**
                     * Validation Status and Stock of Product
                     */
                    if ($product_size_stock_record->product->status == 1 && empty($has_stock_product_record) && $stock_out_result == 0) {
                        /**
                         * Update Status of Product
                         */
                        $product_status_update = Product::where('id', $product_size_stock_record->product_id)->update([
                            'status' => 0,
                            'updated_by' => Auth::user()->id,
                        ]);

                        /**
                         * Validation Update Status of Product
                         */
                        if ($product_status_update) {
                            DB::commit();
                            return redirect()
                                ->route('stock-out.index')
                                ->with(['success' => 'Successfully Add Stock Out']);
                        } else {
                            /**
                             * Failed Store Record
                             */
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Failed Update Status Product'])
                                ->withInput();
                        }
                    } else {
                        /**
                         * Update of Product
                         */
                        $product_update = Product::where('id', $product_size_stock_record->product_id)->update([
                            'updated_by' => Auth::user()->id,
                        ]);

                        /**
                         * Validation Update of Product
                         */
                        if ($product_update) {
                            DB::commit();
                            return redirect()
                                ->route('stock-out.index')
                                ->with(['success' => 'Successfully Add Stock Out']);
                        } else {
                            /**
                             * Failed Store Record
                             */
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Failed Update Status Product'])
                                ->withInput();
                        }
                    }
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Failed Update Stock Product'])
                        ->withInput();
                }
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with(['failed' => 'Failed Add Stock Out'])
                    ->withInput();
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            /**
             * Get Stock Out Record from id
             */
            $stock = StockInOut::with(['productSize.product', 'createdBy', 'updatedBy'])->find($id);

            /**
             * Validation Product id
             */
            if (!is_null($stock)) {
                /**
                 * Index Route
                 */
                $index_route = route('stock-out.index');

                /**
                 * Set Title of Stock Out
                 */
                $title = 'Stock Out';

                return view('stock.detail', compact('stock', 'index_route', 'title'));
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Invalid Request!']);
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            /**
             * Get Stock Out Record from id
             */
            $stock = StockInOut::with(['productSize.product', 'createdBy', 'updatedBy'])->find($id);

            /**
             * Validation Stock Out id
             */
            if (!is_null($stock)) {
                /**
                 * Get All Product
                 */
                $product = ProductSize::with(['product'])
                    ->whereNull('deleted_by')
                    ->whereNull('deleted_at')
                    ->get();

                /**
                 * Update Route
                 */
                $update_route = route('stock-out.update', ['id' => $id]);

                /**
                 * Index Route
                 */
                $index_route = route('stock-out.index');

                /**
                 * Set Title of Stock Out
                 */
                $title = 'Stock Out';

                return view('stock.edit', compact('stock', 'product', 'update_route', 'index_route', 'title'));
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Invalid Request!']);
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            /**
             * Validation Request Body Variables
             */
            $request->validate([
                'product_size' => 'required',
                'date' => 'required',
                'qty' => 'required',
            ]);

            /**
             * Begin Transaction
             */
            DB::beginTransaction();

            /**
             * Update Stock Out Record
             */
            $stock_out_update = StockInOut::where('id', $id)->update([
                'product_size_id' => $request->product_size,
                'qty' => $request->qty,
                'date' => $request->date,
                'description' => $request->description,
                'updated_by' => Auth::user()->id,
            ]);

            /**
             * Validation Update Stock Out Record
             */
            if ($stock_out_update) {
                DB::commit();
                return redirect()
                    ->route('stock-out.index')
                    ->with(['success' => 'Successfully Update Stock Out']);
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with(['failed' => 'Failed Add Stock Out'])
                    ->withInput();
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            /**
             * Begin Transaction
             */
            DB::beginTransaction();

            /**
             * Update Stock Out Record
             */
            $stock_out_destroy = StockInOut::where('id', $id)->update([
                'deleted_by' => Auth::user()->id,
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

            /**
             * Validation Update Product Record
             */
            if ($stock_out_destroy) {
                DB::commit();
                session()->flash('success', 'Stock Out Successfully Deleted');
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                session()->flash('failed', 'Failed Delete Stock Out');
            }
        } catch (Exception $e) {
            session()->flash('failed', $e->getMessage());
        }
    }
}
