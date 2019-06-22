<?php

namespace App\Http\Controllers\Api\V1\Supplier\WebService;

use App\Exceptions\ApiException;
use App\Http\Controllers\ApiController;
use App\Product;
use App\ProductSupplier;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;

class ProductController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $product_id)
    {
        if (!ProductSupplier::where(['supplier_id' => $request->input('supplier_id'), 'product_id' => $product_id])->exists())
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی شما دسترسی به این قسمت ندارید.'
            );
        $product = Product::where('app_id', $request->input('app_id'))
            ->with("gallery", "video")
            ->where(['id' => $product_id])
            ->select(
                'id',
                'app_id',
                'title',
                'image',
                'small_desc',
                'desc',
                'rule',
                'recovery',
                'sort',
                'star',
                DB::raw("CASE WHEN image != '' THEN (concat ( '" . url('') . "/files/product/', image) ) ELSE '' END as image"),
                DB::raw("CASE WHEN image != '' THEN (concat ( '" . url('') . "/files/product/thumb/', image) ) ELSE '' END as image_thumb")
            )
            ->first();
        return $this->respond($product);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        //
    }

    ///////////////////public function///////////////////////


}
