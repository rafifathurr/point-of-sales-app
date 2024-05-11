<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product\CategoryProduct;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CategoryProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /**
         * Datatable Route
         */
        $datatable_route = route('category-product.dataTable');

        return view('category_product.index', compact('datatable_route'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('category_product.create');
    }

    /**
     * Show datatable of resource.
     */
    public function dataTable()
    {
        /**
         * Get All Category Product
         */
        $category_product = CategoryProduct::whereNull('deleted_by')
            ->whereNull('deleted_at')
            ->get();

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($category_product)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                $btn_action = '<div align="center">';
                $btn_action .= '<a href="' . route('category-product.show', ['id' => $data->id]) . '" class="btn btn-sm btn-primary" title="Detail">Detail</a>';
                $btn_action .= '<a href="' . route('category-product.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-warning ml-2" title="Edit">Edit</a>';
                $btn_action .= '<button class="btn btn-sm btn-danger ml-2" onclick="destroyRecord(' . $data->id . ')" title="Delete">Delete</button>';
                $btn_action .= '</div>';
                return $btn_action;
            })
            ->only(['name', 'action'])
            ->rawColumns(['action'])
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
            ]);

            /**
             * Validation Unique Field Record
             */
            $name_check = CategoryProduct::whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->where('name', 'like', '%' . $request->name . '%')
                ->first();

            /**
             * Validation Unique Field Record
             */
            if (is_null($name_check)) {

                /**
                 * Begin Transaction
                 */
                DB::beginTransaction();

                /**
                 * Create Category Product Record
                 */
                $category_product = CategoryProduct::create([
                    'name' => $request->name,
                    'description' => $request->description,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ]);

                /**
                 * Validation Create Category Product Record
                 */
                if ($category_product) {
                    DB::commit();
                    return redirect()->route('category-product.index')->with(['success' => 'Successfully Add Category Product']);
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    return redirect()->back()->with(['failed' => 'Failed Add Category Product'])->withInput();
                }
            } else {
                return redirect()->back()->with(['failed' => 'Name Already Exist'])->withInput();
            }
        } catch (Exception $e) {
            return redirect()->back()->with(['failed' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {

            /**
             * Get Category Product Record from id
             */
            $category_product = CategoryProduct::find($id);

            /**
             * Validation Category Product id
             */
            if (!is_null($category_product)) {
                return view('category_product.detail', compact('category_product'));
            } else {
                return redirect()->back()->with(['failed' => 'Invalid Request!']);
            }
        } catch (Exception $e) {
            return redirect()->back()->with(['failed' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {

            /**
             * Get Category Product Record from id
             */
            $category_product = CategoryProduct::find($id);

            /**
             * Validation Category Product id
             */
            if (!is_null($category_product)) {
                return view('category_product.edit', compact('category_product'));
            } else {
                return redirect()->back()->with(['failed' => 'Invalid Request!']);
            }
        } catch (Exception $e) {
            return redirect()->back()->with(['failed' => $e->getMessage()]);
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
            ]);

            /**
             * Validation Unique Field Record
             */
            $name_check = CategoryProduct::whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->where('name', 'like', '%' . $request->name . '%')
                ->where('id', '!=', $id)
                ->first();

            /**
             * Validation Unique Field Record
             */
            if (is_null($name_check)) {

                /**
                 * Get Category Product from id
                 */
                $category_product = CategoryProduct::find($id);

                /**
                 * Validation Category Product id
                 */
                if (!is_null($category_product)) {

                    /**
                     * Begin Transaction
                     */
                    DB::beginTransaction();

                    /**
                     * Update Category Product Record
                     */
                    $category_product_update = CategoryProduct::where('id', $id)
                        ->update([
                            'name' => $request->name,
                            'description' => $request->description,
                            'updated_by' => Auth::user()->id,
                        ]);

                    /**
                     * Validation Update Category Product Record
                     */
                    if ($category_product_update) {
                        DB::commit();
                        return redirect()->route('category-product.index')->with(['success' => 'Successfully Update Category Product']);
                    } else {

                        /**
                         * Failed Store Record
                         */
                        DB::rollBack();
                        return redirect()->back()->with(['failed' => 'Failed Update Category Product'])->withInput();
                    }
                } else {
                    return redirect()->back()->with(['failed' => 'Invalid Request!']);
                }
            } else {
                return redirect()->back()->with(['failed' => 'Name Already Exist'])->withInput();
            }
        } catch (Exception $e) {
            return redirect()->back()->with(['failed' => $e->getMessage()])->withInput();
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
             * Update Category Product Record
             */
            $category_product_destroy = CategoryProduct::where('id', $id)
                ->update([
                    'deleted_by' => Auth::user()->id,
                    'deleted_at' => date('Y-m-d H:i:s'),
                ]);

            /**
             * Validation Update Category Product Record
             */
            if ($category_product_destroy) {
                DB::commit();
                session()->flash('success', 'Category Product Successfully Deleted');
            } else {

                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                session()->flash('failed', 'Failed Delete Category Product');
            }
        } catch (Exception $e) {
            return redirect()->back()->with(['failed' => $e->getMessage()])->withInput();
        }
    }
}
