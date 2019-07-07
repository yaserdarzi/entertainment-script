<?php

namespace App\Http\Controllers\Api\V1\Supplier;

use App\Exceptions\ApiException;
use App\Http\Controllers\ApiController;
use App\Inside\Constants;
use App\Inside\Helpers;
use App\Product;
use App\ProductVideo;
use App\ProductSupplier;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;

class ProductVideoController extends ApiController
{

    protected $help;

    public function __construct()
    {
        $this->help = new Helpers();
    }

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
        $productVideo = ProductVideo::
        where(['app_id' => $request->input('app_id'), 'product_id' => $product_id])
            ->select(
                'id',
                DB::raw("CASE WHEN path != '' THEN (concat ( '" . url('') . "/files/product/',product_id,'/', path) ) ELSE '' END as path"),
                'mime_type'
            )->get();
        return $this->respond($productVideo);
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
        if ($request->input('role') != Constants::ROLE_ADMIN)
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی شما دسترسی به این قسمت ندارید.'
            );
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
        if (!$request->file('path'))
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی ، وارد کردن مدیا اجباری می باشد.'
            );
        \Storage::disk('upload')->makeDirectory('/product/' . $product_id . '/', 0777, true, true);
        \Storage::disk('upload')->makeDirectory('/product/' . $product_id . '/thumb/', 0777, true, true);
        foreach ($request->file('path') as $value) {
            $mime_type = $value->getClientMimeType();
            $path = md5(\File::get($value)) . '.' . $value->getClientOriginalExtension();
            $exists = \Storage::disk('upload')->has('/product/' . $product_id . '/' . $path);
            if ($exists == null) {
                \Storage::disk('upload')->put('/product/' . $product_id . '/' . $path, \File::get($value->getRealPath()));
            }
            ProductVideo::create([
                'app_id' => $request->input('app_id'),
                'product_id' => $product_id,
                'path' => $path,
                'mime_type' => $mime_type,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        return $this->respond(["status" => "success"]);
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
        if (!ProductSupplier::where(['supplier_id' => $request->input('supplier_id'), 'product_id' => $product_id])->exists())
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی شما دسترسی به این قسمت ندارید.'
            );
        if ($request->input('role') != Constants::ROLE_ADMIN)
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی شما دسترسی به این قسمت ندارید.'
            );
        if (!Product::where('app_id', $request->input('app_id'))->where(['id' => $product_id])->exists())
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'plz check your product_id'
            );
        if (!ProductVideo::where('app_id', $request->input('app_id'))->where(['id' => $id])->exists())
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'plz check your id'
            );
        ProductVideo::where('id', $id)->delete();
        return $this->respond(["status" => "success"]);
    }

    ///////////////////public function///////////////////////


}
