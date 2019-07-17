<?php

namespace App;

use App\Inside\Constants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use SoftDeletes;
    protected $table = Constants::PRODUCT_DB;
    protected $casts = [
        'info' => 'object',
    ];
    protected $fillable = [
        'app_id', 'title', 'image', 'small_desc', 'desc',
        'rule', 'recovery', 'sort', 'star', 'info'
    ];
    protected $dates = ['deleted_at'];

    public function gallery()
    {
        return $this->hasMany(ProductGallery::class, 'product_id', 'id')
            ->select(
                'product_id',
                'mime_type',
                DB::raw("CASE WHEN path != '' THEN (concat ( '" . url('') . "/files/product/',product_id,'/', path) ) ELSE '' END as path"),
                DB::raw("CASE WHEN path != '' THEN (concat ( '" . url('') . "/files/product/',product_id,'/thumb/', path) ) ELSE '' END as path_thumb")
            );
    }

    public function video()
    {
        return $this->hasMany(ProductGallery::class, 'product_id', 'id')
            ->select(
                'product_id',
                'mime_type',
                DB::raw("CASE WHEN path != '' THEN (concat ( '" . url('') . "/files/product/',product_id,'/', path) ) ELSE '' END as path")
            );
    }

}