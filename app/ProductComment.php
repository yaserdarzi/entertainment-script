<?php

namespace App;

use App\Inside\Constants;
use Illuminate\Database\Eloquent\Model;

class ProductComment extends Model
{
    protected $casts = [
        'info' => 'object',
    ];
    protected $table = Constants::PRODUCT_COMMENT_DB;
    protected $fillable = [
        'app_id', 'product_id', 'name', 'comment', 'path',
        'mime_type', 'info'
    ];
}