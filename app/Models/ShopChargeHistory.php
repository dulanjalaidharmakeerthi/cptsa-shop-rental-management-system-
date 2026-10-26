<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopChargeHistory extends Model
{
    protected $fillable = [
        'shop_id',
        'old_values',
        'new_values',
        'changed_by',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];
}
