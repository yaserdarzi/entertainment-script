<?php

namespace App;

use App\Inside\Constants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductEpisode extends Model
{
    protected $table = Constants::PRODUCT_EPISODE_DB;
    protected $fillable = [
        'app_id', 'product_id', 'supplier_id', 'capacity', 'capacity_power_up',
        'capacity_filled', 'capacity_remaining', 'price_adult',
        'price_adult_power_up', 'price_child', 'price_child_power_up',
        'price_baby', 'price_baby_power_up', 'type_percent', 'percent',
        'title', 'date', 'start_hours', 'end_hours', 'status'
    ];

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id')
            ->select(
                'id',
                'title',
                DB::raw("CASE WHEN image != '' THEN (concat ( '" . url('') . "/files/product/thumb/', image) ) ELSE '' END as image_thumb")
            )
            ->where('deleted_at', null);
    }

}
