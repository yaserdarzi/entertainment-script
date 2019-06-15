<?php

namespace App;

use App\Inside\Constants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $table = Constants::PRODUCT_DB;
    protected $fillable = [
        'app_id', 'title', 'image', 'small_desc', 'desc',
        'rule', 'recovery', 'sort', 'star', 'info'
    ];
    protected $dates = ['deleted_at'];
}