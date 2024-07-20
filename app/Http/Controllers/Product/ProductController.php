<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product\CategoryProduct;
use App\Models\Product\Discount;
use App\Models\Product\Product;
use App\Models\Product\ProductSize;
use App\Models\Product\Supplier;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /**
         * Datatable Route
         */
        $datatable_route = route('product.dataTable');

        /**
         * Create Access Based Role
         */
        $can_create = User::find(Auth::user()->id)->hasRole(['super-admin', 'admin']);

        return view('product.index', compact('datatable_route', 'can_create'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        /**
         * Get All Category Product
         */
        $category_product = CategoryProduct::whereNull('deleted_by')->whereNull('deleted_at')->get();

        /**
         * Get All Supplier
         */
        $supplier = Supplier::whereNull('deleted_by')->whereNull('deleted_at')->get();

        return view('product.create', compact('category_product', 'supplier'));
    }

    /**
     * Show datatable of resource.
     */
    public function dataTable()
    {
        /**
         * Get All Product
         */
        $products = Product::whereNull('deleted_by')->whereNull('deleted_at')->get();

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($products)
            ->addIndexColumn()
            ->addColumn('category', function ($data) {
                /**
                 * Return Relation Category Product
                 */
                if(!is_null($data->category_product_id)){
                    return $data->categoryProduct->name;
                }else{
                    return $data->category_product_id;
                }
            })
            ->addColumn('status', function ($data) {
                /**
                 * Validation Status
                 */
                if ($data->status == 1) {
                    return '<span class="badge badge-success pl-3 pr-3">Active</span>';
                } else {
                    if ($data->status == 0) {
                        return '<span class="badge badge-danger pl-3 pr-3">Inactive</span>';
                    }
                }
            })
            ->addColumn('action', function ($data) {
                $btn_action = '<div align="center">';
                $btn_action .= '<a href="' . route('product.show', ['id' => $data->id]) . '" class="btn btn-sm btn-primary" title="Detail">Detail</a>';

                /**
                 * Validation Role Has Access Edit and Delete
                 */
                if (User::find(Auth::user()->id)->hasRole(['super-admin', 'admin'])) {
                    $btn_action .= '<a href="' . route('product.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-warning ml-2" title="Edit">Edit</a>';
                    $btn_action .= '<button class="btn btn-sm btn-danger ml-2" onclick="destroyRecord(' . $data->id . ')" title="Delete">Delete</button>';
                }
                $btn_action .= '</div>';
                return $btn_action;
            })
            ->only(['name', 'category', 'status', 'action'])
            ->rawColumns(['status', 'action'])
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
                'name' => 'required|string',
                'category_product' => 'required',
                'supplier' => 'required',
                'picture' => 'required',
            ]);

            /**
             * Validation Unique Field Record
             */
            $name_check = Product::whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->where('name', 'like', '%' . $request->name . '%')
                ->first();

            /**
             * Validation Unique Field Record
             */
            if (is_null($name_check)) {
                /**
                 * Set Default Status Inactive
                 */
                $product_status_active = false;

                /**
                 * Begin Transaction
                 */
                DB::beginTransaction();

                /**
                 * Create Product Record
                 */
                $product = Product::lockforUpdate()->create([
                    'name' => $request->name,
                    'slug' => Str::slug($request->name),
                    'category_product_id' => $request->category_product,
                    'supplier_id' => $request->supplier,
                    'description' => $request->description,
                    'status' => 0,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ]);

                /**
                 * Validation Create Product Record
                 */
                if ($product) {
                    /**
                     * Path Configuration
                     */
                    $path = 'public/uploads/product';
                    $path_store = 'storage/uploads/product';

                    /**
                     * Validation Check Path
                     */
                    if (!Storage::exists($path)) {
                        Storage::makeDirectory($path);
                    }

                    /**
                     * File Name Configuration
                     */
                    $exploded_name = explode(' ', strtolower($request->name));
                    $name_product_config = implode('_', $exploded_name);
                    $file = $request->file('picture');
                    $file_name = $product->id . '_' . $name_product_config . '.' . $file->getClientOriginalExtension();

                    /**
                     * Upload File
                     */
                    $file->storePubliclyAs($path, $file_name);

                    /**
                     * Validation File Success Uploaded
                     */
                    if (Storage::exists($path . '/' . $file_name)) {
                        /**
                         * Update Product with File Picture
                         */
                        $product_update = Product::where('id', $product->id)->update([
                            'picture' => $path_store . '/' . $file_name,
                        ]);

                        /**
                         * Validation Update Product Record
                         */
                        if ($product_update) {
                            /**
                             * Each of Size Product and Discount
                             */
                            foreach ($request->product_size as $size) {
                                /**
                                 * Create Product Size Record
                                 */
                                $product_size = ProductSize::lockforUpdate()->create([
                                    'product_id' => $product->id,
                                    'size' => $size['size'],
                                    'slug' => Str::slug($size['size']),
                                    'stock' => $size['stock'],
                                    'capital_price' => isset($size['capital_price']) ? $size['capital_price'] : null,
                                    'sell_price' => $size['sell_price'],
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);

                                /**
                                 * Validation Stock Product
                                 */
                                if (intval($size['stock']) > 0) {
                                    if (isset($size['capital_price'])) {
                                        $product_status_active = true;
                                    }
                                }

                                /**
                                 * Validation Create Product Size Record
                                 */
                                if ($product_size) {
                                    /**
                                     * Create Product Discount Record
                                     */
                                    $product_discount = Discount::lockforUpdate()->create([
                                        'product_size_id' => $product_size->id,
                                        'percentage' => $size['percentage'],
                                        'created_by' => Auth::user()->id,
                                        'updated_by' => Auth::user()->id,
                                    ]);

                                    /**
                                     * Validation Create Product Discount Record
                                     */
                                    if (!$product_discount) {
                                        /**
                                         * Failed Store Record
                                         */
                                        DB::rollBack();
                                        return redirect()
                                            ->back()
                                            ->with(['failed' => 'Failed Store Product Discount'])
                                            ->withInput();
                                    }
                                } else {
                                    /**
                                     * Failed Store Record
                                     */
                                    DB::rollBack();
                                    return redirect()
                                        ->back()
                                        ->with(['failed' => 'Failed Store Product Size'])
                                        ->withInput();
                                }
                            }

                            /**
                             * Validation Product Status From Stock Record
                             */
                            if ($product_status_active) {
                                /**
                                 * Update Product with Status
                                 */
                                $product_status_update = Product::where('id', $product->id)->update([
                                    'status' => 1,
                                ]);

                                /**
                                 * Validation Update Product Status Record
                                 */
                                if ($product_status_update) {
                                    DB::commit();
                                    return redirect()
                                        ->route('product.index')
                                        ->with(['success' => 'Successfully Add Product']);
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
                                DB::commit();
                                return redirect()
                                    ->route('product.index')
                                    ->with(['success' => 'Successfully Add Product']);
                            }
                        } else {
                            /**
                             * Failed Store Record
                             */
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Failed Update Picture Product'])
                                ->withInput();
                        }
                    } else {
                        /**
                         * Failed Store Record
                         */
                        DB::rollBack();
                        return redirect()
                            ->back()
                            ->with(['failed' => 'Failed Upload Product'])
                            ->withInput();
                    }
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Failed Add Product'])
                        ->withInput();
                }
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Name Already Exist'])
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
             * Get Product Record from id
             */
            $product = Product::with(['supplier', 'categoryProduct', 'productSize.discount'])->find($id);

            /**
             * Validation Product id
             */
            if (!is_null($product)) {
                /**
                 * Show Capital Price Access Based Role
                 */
                $show_capital_price = User::find(Auth::user()->id)->hasRole(['super-admin', 'admin']);

                return view('product.detail', compact('product', 'show_capital_price'));
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
     * Show detail the specified resource.
     */
    public function getProductSize(Request $request)
    {
        try {
            /**
             * Get Product Size Record from id
             */
            $product = ProductSize::with(['product', 'discount'])->find($request->product);

            /**
             * Validation Product Size id
             */
            if (!is_null($product)) {
                return response()->json($product, 200);
            } else {
                return response()->json(null, 404);
            }
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            /**
             * Get Product Record from id
             */
            $product = Product::with(['supplier', 'categoryProduct', 'productSize.discount', 'createdBy', 'updatedBy'])->find($id);

            /**
             * Validation Product id
             */
            if (!is_null($product)) {
                /**
                 * Get All Category Product
                 */
                $category_product = CategoryProduct::whereNull('deleted_by')->whereNull('deleted_at')->get();

                /**
                 * Get All Supplier
                 */
                $supplier = Supplier::whereNull('deleted_by')->whereNull('deleted_at')->get();

                return view('product.edit', compact('product', 'category_product', 'supplier'));
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
                'name' => 'required|string',
                'category_product' => 'required',
                'supplier' => 'required',
            ]);

            /**
             * Validation Unique Field Record
             */
            $name_check = Product::whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->where('name', 'like', '%' . $request->name . '%')
                ->where('id', '!=', $id)
                ->first();

            /**
             * Validation Unique Field Record
             */
            if (is_null($name_check)) {
                /**
                 * Get Product Record from id
                 */
                $product = Product::find($id);

                /**
                 * Validation Product id
                 */
                if (!is_null($product)) {
                    /**
                     * Set Default Status Inactive
                     */
                    $product_status_active = false;

                    /**
                     * Begin Transaction
                     */
                    DB::beginTransaction();

                    /**
                     * Update Product Record
                     */
                    $product_update = Product::where('id', $id)->update([
                        'name' => $request->name,
                        'slug' => Str::slug($request->name),
                        'category_product_id' => $request->category_product,
                        'supplier_id' => $request->supplier,
                        'description' => $request->description,
                        'updated_by' => Auth::user()->id,
                    ]);

                    /**
                     * Validation Update Product Record
                     */
                    if ($product_update) {
                        /**
                         * Validation update has request file
                         */
                        if (!empty($request->allFiles())) {
                            /**
                             * Path Configuration
                             */
                            $path = 'public/uploads/product';
                            $path_store = 'storage/uploads/product';

                            /**
                             * Validation Check Path
                             */
                            if (!Storage::exists($path)) {
                                Storage::makeDirectory($path);
                            }

                            /**
                             * Get Filename Picture Record
                             */
                            $picture_record_exploded = explode('/', $product->picture);
                            $file_name_record = $picture_record_exploded[count($picture_record_exploded) - 1];

                            /**
                             * Remove Has File Exist
                             */
                            if (Storage::exists($path . '/' . $file_name_record)) {
                                Storage::delete($path . '/' . $file_name_record);
                            }

                            /**
                             * File Name Configuration
                             */
                            $exploded_name = explode(' ', strtolower($request->name));
                            $name_product_config = implode('_', $exploded_name);
                            $file = $request->file('picture');
                            $file_name = $id . '_' . $name_product_config . '.' . $file->getClientOriginalExtension();

                            /**
                             * Upload File
                             */
                            $file->storePubliclyAs($path, $file_name);

                            /**
                             * Validation File Success Uploaded
                             */
                            if (Storage::exists($path . '/' . $file_name)) {
                                /**
                                 * Update Product with File Picture
                                 */
                                $product_picture_update = $product->update([
                                    'picture' => $path_store . '/' . $file_name,
                                ]);

                                /**
                                 * Validation Update Product Picture Record
                                 */
                                if ($product_picture_update) {
                                    /**
                                     * Get id of Product Size Record
                                     */
                                    $product_size_id_record = ProductSize::where('product_id', $id)->pluck('id')->toArray();

                                    /**
                                     * Update Last Record of Product Discount
                                     */
                                    $remove_product_discount = Discount::whereIn('product_size_id', $product_size_id_record)->update([
                                        'deleted_by' => Auth::user()->id,
                                        'deleted_at' => date('Y-m-d H:i:s'),
                                    ]);

                                    /**
                                     * Validation Update Last Record of Product Discount
                                     */
                                    if ($remove_product_discount) {
                                        /**
                                         * Update Last Record of Product Size
                                         */
                                        $remove_product_size = ProductSize::where('product_id', $id)->update([
                                            'deleted_by' => Auth::user()->id,
                                            'deleted_at' => date('Y-m-d H:i:s'),
                                        ]);

                                        /**
                                         * Validation Update Last Record of Product Size
                                         */
                                        if ($remove_product_size) {
                                            /**
                                             * Check Status of Product Contain 0 Stock
                                             */
                                            $array_inactive_status = [];

                                            /**
                                             * Each of Size Product and Discount
                                             */
                                            foreach ($request->product_size as $size) {
                                                /**
                                                 * Check Has Capital Price Request
                                                 */
                                                if (isset($size['capital_price'])) {
                                                    /**
                                                     * Create New Product Size Record
                                                     */
                                                    $product_size = ProductSize::lockforUpdate()->create([
                                                        'product_id' => $id,
                                                        'size' => $size['size'],
                                                        'slug' => Str::slug($size['size']),
                                                        'stock' => $size['stock'],
                                                        'capital_price' => $size['capital_price'],
                                                        'sell_price' => $size['sell_price'],
                                                        'created_by' => Auth::user()->id,
                                                        'updated_by' => Auth::user()->id,
                                                    ]);
                                                } else {
                                                    /**
                                                     * Create New Product Size Record
                                                     */
                                                    $product_size = ProductSize::lockforUpdate()->create([
                                                        'product_id' => $id,
                                                        'size' => $size['size'],
                                                        'slug' => Str::slug($size['size']),
                                                        'stock' => $size['stock'],
                                                        'sell_price' => $size['sell_price'],
                                                        'created_by' => Auth::user()->id,
                                                        'updated_by' => Auth::user()->id,
                                                    ]);
                                                }

                                                /**
                                                 * Validation Stock Product
                                                 */
                                                if (intval($size['stock']) > 0) {
                                                    if (isset($size['capital_price'])) {
                                                        $product_status_active = true;
                                                    }
                                                } else {
                                                    array_push($array_inactive_status, false);
                                                }

                                                /**
                                                 * Validation Create New Product Size Record
                                                 */
                                                if ($product_size) {
                                                    /**
                                                     * Create Product Discount Record
                                                     */
                                                    $product_discount = Discount::lockforUpdate()->create([
                                                        'product_size_id' => $product_size->id,
                                                        'percentage' => $size['percentage'],
                                                        'created_by' => Auth::user()->id,
                                                        'updated_by' => Auth::user()->id,
                                                    ]);

                                                    /**
                                                     * Validation Create Product Discount Record
                                                     */
                                                    if (!$product_discount) {
                                                        /**
                                                         * Failed Store Record
                                                         */
                                                        DB::rollBack();
                                                        return redirect()
                                                            ->back()
                                                            ->with(['failed' => 'Failed Store Product Discount'])
                                                            ->withInput();
                                                    }
                                                } else {
                                                    /**
                                                     * Failed Store Record
                                                     */
                                                    DB::rollBack();
                                                    return redirect()
                                                        ->back()
                                                        ->with(['failed' => 'Failed Store Product Size'])
                                                        ->withInput();
                                                }
                                            }

                                            /**
                                             * Validation Product Status From Stock Record
                                             */
                                            if ($product_status_active && $product->status == 0) {
                                                /**
                                                 * Update Product with Status
                                                 */
                                                $product_status_update = $product->update([
                                                    'status' => 1,
                                                ]);

                                                /**
                                                 * Validation Update Product Status Record
                                                 */
                                                if ($product_status_update) {
                                                    DB::commit();
                                                    return redirect()
                                                        ->route('product.index')
                                                        ->with(['success' => 'Successfully Update Product']);
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
                                                if (count($array_inactive_status) == count($request->product_size) && $product->status == 1) {
                                                    /**
                                                     * Update Product with Status
                                                     */
                                                    $product_status_update = $product->update([
                                                        'status' => 0,
                                                    ]);

                                                    /**
                                                     * Validation Update Product Status Record
                                                     */
                                                    if ($product_status_update) {
                                                        DB::commit();
                                                        return redirect()
                                                            ->route('product.index')
                                                            ->with(['success' => 'Successfully Update Product']);
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
                                                    DB::commit();
                                                    return redirect()
                                                        ->route('product.index')
                                                        ->with(['success' => 'Successfully Update Product']);
                                                }
                                            }
                                        } else {
                                            /**
                                             * Failed Store Record
                                             */
                                            DB::rollBack();
                                            return redirect()
                                                ->back()
                                                ->with(['failed' => 'Failed Update Store Product'])
                                                ->withInput();
                                        }
                                    } else {
                                        /**
                                         * Failed Store Record
                                         */
                                        DB::rollBack();
                                        return redirect()
                                            ->back()
                                            ->with(['failed' => 'Failed Update Store Product'])
                                            ->withInput();
                                    }
                                } else {
                                    /**
                                     * Failed Store Record
                                     */
                                    DB::rollBack();
                                    return redirect()
                                        ->back()
                                        ->with(['failed' => 'Failed Update Picture Product'])
                                        ->withInput();
                                }
                            } else {
                                /**
                                 * Failed Store Record
                                 */
                                DB::rollBack();
                                return redirect()
                                    ->back()
                                    ->with(['failed' => 'Failed Upload Product'])
                                    ->withInput();
                            }
                        } else {
                            /**
                             * Get id of Product Size Record
                             */
                            $product_size_id_record = ProductSize::where('product_id', $id)->pluck('id')->toArray();

                            /**
                             * Update Last Record of Product Discount
                             */
                            $remove_product_discount = Discount::whereIn('product_size_id', $product_size_id_record)->update([
                                'deleted_by' => Auth::user()->id,
                                'deleted_at' => date('Y-m-d H:i:s'),
                            ]);

                            /**
                             * Validation Update Last Record of Product Discount
                             */
                            if ($remove_product_discount) {
                                /**
                                 * Update Last Record of Product Size
                                 */
                                $remove_product_size = ProductSize::where('product_id', $id)->update([
                                    'deleted_by' => Auth::user()->id,
                                    'deleted_at' => date('Y-m-d H:i:s'),
                                ]);

                                /**
                                 * Validation Update Last Record of Product Size
                                 */
                                if ($remove_product_size) {
                                    /**
                                     * Check Status of Product Contain 0 Stock
                                     */
                                    $array_inactive_status = [];

                                    /**
                                     * Each of Size Product and Discount
                                     */
                                    foreach ($request->product_size as $size) {
                                        /**
                                         * Check Has Capital Price Request
                                         */
                                        if (!is_null($size['capital_price'])) {
                                            /**
                                             * Create New Product Size Record
                                             */
                                            $product_size = ProductSize::lockforUpdate()->create([
                                                'product_id' => $id,
                                                'size' => $size['size'],
                                                'slug' => Str::slug($size['size']),
                                                'stock' => $size['stock'],
                                                'capital_price' => $size['capital_price'],
                                                'sell_price' => $size['sell_price'],
                                                'created_by' => Auth::user()->id,
                                                'updated_by' => Auth::user()->id,
                                            ]);
                                        } else {
                                            /**
                                             * Create New Product Size Record
                                             */
                                            $product_size = ProductSize::lockforUpdate()->create([
                                                'product_id' => $id,
                                                'size' => $size['size'],
                                                'slug' => Str::slug($size['size']),
                                                'stock' => $size['stock'],
                                                'sell_price' => $size['sell_price'],
                                                'created_by' => Auth::user()->id,
                                                'updated_by' => Auth::user()->id,
                                            ]);
                                        }

                                        /**
                                         * Validation Stock Product
                                         */
                                        if (intval($size['stock']) > 0) {
                                            if (isset($size['capital_price'])) {
                                                $product_status_active = true;
                                            }
                                        } else {
                                            array_push($array_inactive_status, false);
                                        }

                                        /**
                                         * Validation Create New Product Size Record
                                         */
                                        if ($product_size) {
                                            /**
                                             * Create Product Discount Record
                                             */
                                            $product_discount = Discount::lockforUpdate()->create([
                                                'product_size_id' => $product_size->id,
                                                'percentage' => $size['percentage'],
                                                'created_by' => Auth::user()->id,
                                                'updated_by' => Auth::user()->id,
                                            ]);

                                            /**
                                             * Validation Create Product Discount Record
                                             */
                                            if (!$product_discount) {
                                                /**
                                                 * Failed Store Record
                                                 */
                                                DB::rollBack();
                                                return redirect()
                                                    ->back()
                                                    ->with(['failed' => 'Failed Store Product Discount'])
                                                    ->withInput();
                                            }
                                        } else {
                                            /**
                                             * Failed Store Record
                                             */
                                            DB::rollBack();
                                            return redirect()
                                                ->back()
                                                ->with(['failed' => 'Failed Store Product Size'])
                                                ->withInput();
                                        }
                                    }

                                    /**
                                     * Validation Product Status From Stock Record
                                     */
                                    if ($product_status_active && $product->status == 0) {
                                        /**
                                         * Update Product with Status
                                         */
                                        $product_status_update = $product->update([
                                            'status' => 1,
                                        ]);

                                        /**
                                         * Validation Update Product Status Record
                                         */
                                        if ($product_status_update) {
                                            DB::commit();
                                            return redirect()
                                                ->route('product.index')
                                                ->with(['success' => 'Successfully Update Product']);
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
                                        if (count($array_inactive_status) == count($request->product_size) && $product->status == 1) {
                                            /**
                                             * Update Product with Status
                                             */
                                            $product_status_update = $product->update([
                                                'status' => 0,
                                            ]);

                                            /**
                                             * Validation Update Product Status Record
                                             */
                                            if ($product_status_update) {
                                                DB::commit();
                                                return redirect()
                                                    ->route('product.index')
                                                    ->with(['success' => 'Successfully Update Product']);
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
                                            DB::commit();
                                            return redirect()
                                                ->route('product.index')
                                                ->with(['success' => 'Successfully Update Product']);
                                        }
                                    }
                                } else {
                                    /**
                                     * Failed Store Record
                                     */
                                    DB::rollBack();
                                    return redirect()
                                        ->back()
                                        ->with(['failed' => 'Failed Update Store Product'])
                                        ->withInput();
                                }
                            } else {
                                /**
                                 * Failed Store Record
                                 */
                                DB::rollBack();
                                return redirect()
                                    ->back()
                                    ->with(['failed' => 'Failed Update Store Product'])
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
                            ->with(['failed' => 'Failed Add Product'])
                            ->withInput();
                    }
                } else {
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Invalid Request!']);
                }
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Name Already Exist'])
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
             * Update Product Record
             */
            $product_destroy = Product::where('id', $id)->update([
                'deleted_by' => Auth::user()->id,
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

            /**
             * Validation Update Product Record
             */
            if ($product_destroy) {
                DB::commit();
                session()->flash('success', 'Product Successfully Deleted');
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                session()->flash('failed', 'Failed Delete Product');
            }
        } catch (Exception $e) {
            session()->flash('failed', $e->getMessage());
        }
    }
}
