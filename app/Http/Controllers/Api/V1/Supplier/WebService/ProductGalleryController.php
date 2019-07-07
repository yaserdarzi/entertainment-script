<?php

namespace App\Http\Controllers\Api\V1\Supplier\WebService;

use App\Exceptions\ApiException;
use App\Http\Controllers\ApiController;
use App\Product;
use App\ProductGallery;
use App\ProductSupplier;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;

class ProductGalleryController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($product_id, Request $request)
    {
        if (!ProductSupplier::where(['supplier_id' => $request->input('supplier_id'), 'product_id' => $product_id])->exists())
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی شما دسترسی به این قسمت ندارید.'
            );
        if (!Product::where('app_id', $request->input('app_id'))->where(['id' => $product_id])->exists())
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'plz check your product_id'
            );
        $productGallery = ProductGallery::
        where(['app_id' => $request->input('app_id'), 'product_id' => $product_id])
            ->select(
                'id',
                DB::raw("CASE WHEN path != '' THEN (concat ( '" . url('') . "/files/product/',product_id,'/', path) ) ELSE '' END as path"),
                DB::raw("CASE WHEN path != '' THEN (concat ( '" . url('') . "/files/product/',product_id,'/thumb/', path) ) ELSE '' END as path_thumb"),
                'mime_type'
            )->get();
        return $this->respond($productGallery);
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
    public function store($product_id, Request $request)
    {
       //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($product_id, Request $request, $id)
    {
        //
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
    public function destroy($product_id, $id, Request $request)
    {
       //
    }

    ///////////////////public function///////////////////////


}
