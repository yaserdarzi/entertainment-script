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
use Morilog\Jalali\CalendarUtils;
use Morilog\Jalali\jDate;

class ProductEpisodeController extends ApiController
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
        if (!$request->input('date'))
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'plz check your date'
            );
        $arrayDate = explode('/', $request->input('date'));
        $date = \Morilog\Jalali\CalendarUtils::toGregorian($arrayDate[0], $arrayDate[1], $arrayDate[2]);
        $date = date('Y-m-d', strtotime($date[0] . '-' . $date[1] . '-' . $date[2]));
        $ProductEpisode = ProductEpisode::where('app_id', $request->input('app_id'))
            ->with('product')
            ->where(['product_id' => $product_id, 'supplier_id' => $request->input('supplier_id'), 'date' => $date])
            ->get()->map(function ($value) {
                $value->date_persian = CalendarUtils::strftime('Y-m-d', strtotime($value->date));
                return $value;
            });
        return $this->respond($ProductEpisode);
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
        if (!$request->input('capacity'))
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی ، وارد کردن ظرفیت اجباری می باشد.'
            );
        if (!$request->input('capacity_power_up'))
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی ، وارد کردن ظرفیت پاورآپ اجباری می باشد.'
            );
        if (!$request->input('price_adult'))
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی ، وارد کردن قیمت بزرگسالان اجباری می باشد.'
            );
        if (!$request->input('price_adult_power_up'))
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی ، وارد کردن قیمت بزرگسالان پاورآپ اجباری می باشد.'
            );
        switch ($request->input('type_percent')) {
            case Constants::TYPE_PERCENT_PRICE:
                $typePercent = Constants::TYPE_PERCENT_PRICE;
                break;
            case Constants::TYPE_PERCENT_PERCENT:
                $typePercent = Constants::TYPE_PERCENT_PERCENT;
                break;
            default:
                $typePercent = Constants::TYPE_PERCENT_PRICE;
        }
        if (!$request->input('start_date'))
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی ، وارد کردن تاریخ شروع اجباری می باشد.'
            );
        if (!$request->input('end_date'))
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی ، وارد کردن تاریخ پایان اجباری می باشد.'
            );
        $arrayStartDate = explode('/', $request->input('start_date'));
        $arrayEndDate = explode('/', $request->input('end_date'));
        $start_date = \Morilog\Jalali\CalendarUtils::toGregorian($arrayStartDate[0], $arrayStartDate[1], $arrayStartDate[2]);
        $end_date = \Morilog\Jalali\CalendarUtils::toGregorian($arrayEndDate[0], $arrayEndDate[1], $arrayEndDate[2]);
        $startDay = date_create(date('Y-m-d', strtotime($start_date[0] . '-' . $start_date[1] . '-' . $start_date[2])));
        $endDay = date_create(date('Y-m-d', strtotime($end_date[0] . '-' . $end_date[1] . '-' . $end_date[2])));
        $diff = date_diff($startDay, $endDay);
        for ($i = 0; $i <= $diff->days; $i++) {
            $date = strtotime(date('Y-m-d', strtotime($startDay->format('Y-m-d') . " +" . $i . " days")));
            ProductEpisode::create([
                'app_id' => $request->input('app_id'),
                'product_id' => $product_id,
                'supplier_id' => $request->input('supplier_id'),
                'capacity' => $this->help->normalizePhoneNumber($request->input('capacity')),
                'capacity_power_up' => $this->help->normalizePhoneNumber($request->input('capacity_power_up')),
                'capacity_remaining' => $this->help->normalizePhoneNumber($request->input('capacity')),
                'price_adult' => intval($this->help->priceNumberDigitsToNormal($request->input('price_adult'))),
                'price_adult_power_up' => intval($this->help->priceNumberDigitsToNormal($request->input('price_adult_power_up'))),
                'price_child' => intval($this->help->priceNumberDigitsToNormal($request->input('price_child'))),
                'price_child_power_up' => intval($this->help->priceNumberDigitsToNormal($request->input('price_child_power_up'))),
                'price_baby' => intval($this->help->priceNumberDigitsToNormal($request->input('price_baby'))),
                'price_baby_power_up' => intval($this->help->priceNumberDigitsToNormal($request->input('price_baby_power_up'))),
                'type_percent' => $typePercent,
                'percent' => $this->help->normalizePhoneNumber($request->input('percent')),
                'title' => $request->input('title'),
                'date' => date('Y-m-d', $date),
                'start_hours' => $request->input('start_hours'),
                'end_hours' => $request->input('end_hours'),
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
        if (!ProductEpisode::where('app_id', $request->input('app_id'))->where(['id' => $id, 'supplier_id' => $request->input('supplier_id'), 'product_id' => $product_id])->exists())
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'plz check your id'
            );
        $ProductEpisode = ProductEpisode::where('app_id', $request->input('app_id'))
            ->with('product')
            ->where(['product_id' => $product_id, 'supplier_id' => $request->input('supplier_id'), 'id' => $id])
            ->first();
        $ProductEpisode->date_persian = CalendarUtils::strftime('Y-m-d', strtotime($ProductEpisode->date));
        return $this->respond($ProductEpisode);
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
    public function update($product_id, Request $request, $id)
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
        if (!ProductEpisode::where('app_id', $request->input('app_id'))->where(['id' => $id, 'supplier_id' => $request->input('supplier_id'), 'product_id' => $product_id])->exists())
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'plz check your id'
            );
        if (!$productEpisode = ProductEpisode::where('app_id', $request->input('app_id'))->where(['id' => $id, 'supplier_id' => $request->input('supplier_id'), 'product_id' => $product_id])->where('status', Constants::STATUS_ACTIVE)->first())
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی ، امکان تغییر این سانس وجود ندارد.'
            );
        if (!$request->input('capacity'))
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی ، وارد کردن ظرفیت اجباری می باشد.'
            );
        if (!$request->input('price_adult'))
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی ، وارد کردن قیمت بزرگسالان اجباری می باشد.'
            );
        switch ($request->input('type_percent')) {
            case Constants::TYPE_PERCENT_PRICE:
                $typePercent = Constants::TYPE_PERCENT_PRICE;
                break;
            case Constants::TYPE_PERCENT_PERCENT:
                $typePercent = Constants::TYPE_PERCENT_PERCENT;
                break;
            default:
                $typePercent = Constants::TYPE_PERCENT_PRICE;
        }
        switch ($request->input('status')) {
            case Constants::STATUS_ACTIVE:
                $status = Constants::STATUS_ACTIVE;
                break;
            case Constants::STATUS_DEACTIVATE:
                $status = Constants::STATUS_DEACTIVATE;
                break;
            case Constants::STATUS_RETURN_BUY:
                $status = Constants::STATUS_RETURN_BUY;
                break;
            default:
                throw new ApiException(
                    ApiException::EXCEPTION_NOT_FOUND_404,
                    'کاربر گرامی ، وارد کردن وضعیت اجباری می باشد.'
                );
        }
        if (!$request->input('date'))
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی ، وارد کردن تاریخ اجباری می باشد.'
            );
        if ($request->input('capacity') < $productEpisode->capacity_filled)
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی ، امکان کم کردن ظرفیت وجود ندارد.'
            );
        $arrayDate = explode('/', $request->input('date'));
        if (sizeof($arrayDate) == 1)
            $arrayDate = explode('-', $request->input('date'));
        $date = \Morilog\Jalali\CalendarUtils::toGregorian($arrayDate[0], $arrayDate[1], $arrayDate[2]);
        $date = date_create(date('Y-m-d', strtotime($date[0] . '-' . $date[1] . '-' . $date[2])));
        ProductEpisode::where('id', $id)->update([
            'capacity' => $this->help->normalizePhoneNumber($request->input('capacity')),
            'capacity_remaining' => intval($this->help->normalizePhoneNumber($request->input('capacity')) - $productEpisode->capacity_filled),
            'price_adult' => intval($this->help->priceNumberDigitsToNormal($request->input('price_adult'))),
            'price_child' => intval($this->help->priceNumberDigitsToNormal($request->input('price_child'))),
            'price_baby' => intval($this->help->priceNumberDigitsToNormal($request->input('price_baby'))),
            'type_percent' => $typePercent,
            'percent' => $this->help->normalizePhoneNumber($request->input('percent')),
            'title' => $request->input('title'),
            'date' => $date->format('Y-m-d'),
            'start_hours' => $request->input('start_hours'),
            'end_hours' => $request->input('end_hours'),
            'status' => $status,
        ]);
        return $this->respond(["status" => "success"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($product_id, $id, Request $request)
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
        if (!ProductEpisode::where('app_id', $request->input('app_id'))->where(['id' => $id, 'supplier_id' => $request->input('supplier_id'), 'product_id' => $product_id])->exists())
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'plz check your id'
            );
        if (!$productEpisode = ProductEpisode::where('app_id', $request->input('app_id'))->where(['id' => $id, 'supplier_id' => $request->input('supplier_id'), 'product_id' => $product_id])->where('status', Constants::STATUS_ACTIVE)->first())
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی ، امکان تغییر این سانس وجود ندارد.'
            );
        if ($productEpisode->capacity_filled != 0)
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی ، امکان حذف این سانس وجود ندارد.'
            );
        ProductEpisode::where('id', $id)->delete();
        return $this->respond(["status" => "success"]);
    }

    ///////////////////public function///////////////////////


}
