<?php

namespace App;

use App\Inside\Constants;
use Illuminate\Database\Eloquent\Model;

class ProductSupplier extends Model
{
    protected $table = Constants::PRODUCT_SUPPLIER_DB;
    protected $casts = [
        'info' => 'object',
    ];
    protected $fillable = [
        'app_id', 'supplier_id', 'product_id', 'info'
    ];
}
