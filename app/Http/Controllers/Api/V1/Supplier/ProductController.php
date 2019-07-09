<?php

namespace App\Http\Controllers\Api\V1\Supplier;

use App\Exceptions\ApiException;
use App\Http\Controllers\ApiController;
use App\Inside\Constants;
use App\Inside\Helpers;
use App\Product;
use App\ProductEpisode;
use App\ProductSupplier;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;

class ProductController extends ApiController
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
    public function index(Request $request)
    {
        $product = Product::join(Constants::PRODUCT_SUPPLIER_DB, Constants::PRODUCT_DB . '.id', '=', Constants::PRODUCT_SUPPLIER_DB . '.product_id')
            ->where([
                Constants::PRODUCT_DB . '.app_id' => $request->input('app_id'),
                Constants::PRODUCT_SUPPLIER_DB . '.app_id' => $request->input('app_id'),
                'supplier_id' => $request->input('supplier_id'),
            ])
            ->select(
                Constants::PRODUCT_DB . '.id',
                'title',
                DB::raw("CASE WHEN image != '' THEN (concat ( '" . url('') . "/files/product/thumb/', image) ) ELSE '' END as image_thumb")
            )->get();
        return $this->respond($product);
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
        if ($request->input('role') != Constants::ROLE_ADMIN)
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی شما دسترسی به این قسمت ندارید.'
            );
        if (!$request->input('title'))
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی ، وارد کردن نام محصول اجباری می باشد.'
            );
        if (!$request->input('star'))
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی ، وارد کردن ستاره اجباری می باشد.'
            );
        if (!$request->file('image'))
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی ، وارد کردن تصویر اجباری می باشد.'
            );
        if (!in_array($request->file('image')->getClientMimeType(), Constants::PHOTO_TYPE))
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی ، وارد کردن تصویر اجباری می باشد.'
            );
        if (!$request->input('small_desc'))
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی ، وارد کردن توضیحات کوتاه محصول اجباری می باشد.'
            );
        \Storage::disk('upload')->makeDirectory('/product/', 0777, true, true);
        \Storage::disk('upload')->makeDirectory('/product/thumb/', 0777, true, true);
        $image = md5(\File::get($request->file("image"))) . '.' . $request->file("image")->getClientOriginalExtension();
        $exists = \Storage::disk('upload')->has('/product/' . $image);
        if ($exists == null) {
            \Storage::disk('upload')->put('/product/' . $image, \File::get($request->file("image")->getRealPath()));
        }
        //generate thumbnail
        $image_resize = Image::make($request->file("image")->getRealPath());
        //get width and height of image
        $data = getimagesize($request->file("image"));
        $imageWidth = $data[0];
        $imageHeight = $data[1];
        $newDimen = $this->help->getScaledDimension($imageWidth, $imageHeight, 400, 400, false);
        $image_resize->resize($newDimen[0], $newDimen[1]);
        $thumb = public_path('/files/product/thumb/' . $image);
        $image_resize->save($thumb);
        $product = Product::create([
            'app_id' => $request->input('app_id'),
            'title' => $request->input('title'),
            'star' => $this->help->normalizePhoneNumber($request->input('star')),
            'image' => $image,
            'rule' => $request->input('rule'),
            'recovery' => $request->input('recovery'),
            'small_desc' => $request->input('small_desc'),
            'desc' => $request->input('desc'),
        ]);
        ProductSupplier::create([
            'app_id' => $request->input('app_id'),
            'supplier_id' => $request->input('supplier_id'),
            'product_id' => $product->id,
        ]);
        return $this->respond(["status" => "success"]);
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
                '*',
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
        if (!ProductSupplier::where(['supplier_id' => $request->input('supplier_id'), 'product_id' => $id])->exists())
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی شما دسترسی به این قسمت ندارید.'
            );
        if ($request->input('role') != Constants::ROLE_ADMIN)
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی شما دسترسی به این قسمت ندارید.'
            );
        $productInfo = Product::where('app_id', $request->input('app_id'))
            ->where(['id' => $id])->first();
        if (!$productInfo)
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'plz check your id'
            );
        if (!$request->input('title'))
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی ، وارد کردن نام محصول اجباری می باشد.'
            );
        if (!$request->input('star'))
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی ، وارد کردن ستاره اجباری می باشد.'
            );
        if (!$request->input('small_desc'))
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی ، وارد کردن توضیحات کوتاه محصول اجباری می باشد.'
            );
        $image = $productInfo->image;
        if ($request->file('image')) {
            if (!in_array($request->file('image')->getClientMimeType(), Constants::PHOTO_TYPE))
                throw new ApiException(
                    ApiException::EXCEPTION_NOT_FOUND_404,
                    'کاربر گرامی ، وارد کردن تصویر اجباری می باشد.'
                );
            \Storage::disk('upload')->makeDirectory('/product/', 0777, true, true);
            \Storage::disk('upload')->makeDirectory('/product/thumb/', 0777, true, true);
            $image = md5(\File::get($request->file("image"))) . '.' . $request->file("image")->getClientOriginalExtension();
            $exists = \Storage::disk('upload')->has('/product/' . $image);
            if ($exists == null) {
                \Storage::disk('upload')->put('/product/' . $image, \File::get($request->file("image")->getRealPath()));
            }
            //generate thumbnail
            $image_resize = Image::make($request->file("image")->getRealPath());
            //get width and height of image
            $data = getimagesize($request->file("image"));
            $imageWidth = $data[0];
            $imageHeight = $data[1];
            $newDimen = $this->help->getScaledDimension($imageWidth, $imageHeight, 400, 400, false);
            $image_resize->resize($newDimen[0], $newDimen[1]);
            $thumb = public_path('/files/product/thumb/' . $image);
            $image_resize->save($thumb);
        }
        Product::where('id', $id)->update([
            'title' => $request->input('title'),
            'star' => $this->help->normalizePhoneNumber($request->input('star')),
            'image' => $image,
            'rule' => $request->input('rule'),
            'recovery' => $request->input('recovery'),
            'small_desc' => $request->input('small_desc'),
            'desc' => $request->input('desc'),
        ]);
        return $this->respond(["status" => "success"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        if ($request->input('role') != Constants::ROLE_ADMIN)
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی شما دسترسی به این قسمت ندارید.'
            );
        if (!Product::where('app_id', $request->input('app_id'))->where(['id' => $id])->exists())
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'plz check your id'
            );
        if (ProductEpisode::where('product_id', $id)->exists())
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی محصول مورد نظر دارای سانس می باشد.'
            );
        product::where('id', $id)->delete();
        return $this->respond(["status" => "success"]);
    }

    ///////////////////public function///////////////////////


}
