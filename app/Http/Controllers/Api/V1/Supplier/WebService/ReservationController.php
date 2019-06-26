<?php

namespace App\Http\Controllers\Api\V1\Supplier\WebService;

use App\Exceptions\ApiException;
use App\Http\Controllers\ApiController;
use App\Inside\Constants;
use App\Inside\Helpers;
use App\Product;
use App\ProductEpisode;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\CalendarUtils;

class ReservationController extends ApiController
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
        if (!$request->input('start_date'))
            throw new ApiException(
                ApiException::EXCEPTION_NOT_FOUND_404,
                'کاربر گرامی ، وارد کردن تاریخ شروع اجباری می باشد.'
            );
        $capacity = intval($this->help->normalizePhoneNumber($request->input('capacity')));
        $capacity_child = intval($this->help->normalizePhoneNumber($request->input('capacity_child')));
        $capacity_baby = intval($this->help->normalizePhoneNumber($request->input('capacity_baby')));
        $startExplode = explode('/', $request->input('start_date'));
        $start_date = \Morilog\Jalali\CalendarUtils::toGregorian($startExplode[0], $startExplode[1], $startExplode[2]);
        $startDay = date_create(date('Y-m-d', strtotime($start_date[0] . '-' . $start_date[1] . '-' . $start_date[2])));
        $supplier_id = $request->input('supplier_id');
        $date = $startDay->format('Y-m-d');
        $productEpisode = ProductEpisode::where('app_id', $request->input('app_id'))
            ->where('supplier_id', $supplier_id)
            ->where([
                'status' => Constants::STATUS_ACTIVE,
                'date' => $date,
            ])->get();
        $episode = [];
        if (sizeof($productEpisode))
            foreach ($productEpisode as $key => $value) {
                $value->is_buy = true;
                $is_full = false;
                if ($value->capacity_remaining < ($capacity + $capacity_child)) {
                    $value->is_buy = false;
                    $is_full = true;
                }
                $price_all = intval(
                    intval($value->price_adult * $capacity) +
                    intval($value->price_child * $capacity_child) +
                    intval($value->price_baby * $capacity_baby)
                );
                $price_percent = $price_all;
                if ($value->type_percent == Constants::TYPE_PERCENT_PERCENT) {
                    if ($value->percent != 0) {
                        $price_percent = ($value->percent / 100) * $price_all;
                        $price_percent = $price_all - $price_percent;
                    }
                } elseif ($value->type_percent == Constants::TYPE_PERCENT_PRICE)
                    $price_percent = $price_all - $value->percent;
                $episode[$key] = [
                    'id' => $value->id,
                    'date' => CalendarUtils::strftime('Y-m-d', strtotime($value->date)),
                    'day' => CalendarUtils::strftime('%A', strtotime($value->date)),
                    'start_hours' => $value->start_hours,
                    'end_hours' => $value->end_hours,
                    'title' => $value->title,
                    'price_adult' => $value->price_adult,
                    'count_adult' => $capacity,
                    'price_child' => $value->price_child,
                    'count_child' => $capacity_child,
                    'price_baby' => $value->price_baby,
                    'count_baby' => $capacity_baby,
                    'price_all' => $price_all,
                    'count_all' => intval($capacity + $capacity_child + $capacity_baby),
                    'price_percent' => $price_percent,
                    'capacity_remaining' => $value->capacity_remaining,
                    'is_full' => $is_full,
                ];
            }
        return $this->respond($episode);
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
    public function show(Request $request, $id)
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
    public function destroy($hotel_id, $id, Request $request)
    {
        //
    }

    ///////////////////public function///////////////////////


}
