<?php

namespace App;

use App\Inside\Constants;
use Illuminate\Database\Eloquent\Model;

class ProductGallery extends Model
{
    protected $table = Constants::PRODUCT_GALLERY_DB;
    protected $fillable = [
        'app_id', 'product_id', 'path', 'mime_type', 'created_at'
    ];
    public $timestamps = false;
}
