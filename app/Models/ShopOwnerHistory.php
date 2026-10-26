<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopOwnerHistory extends Model
{
    use HasFactory;

    protected $table = 'shop_owner_histories';

    protected $fillable = [
        'shop_id',
        'shop_owner_name',
        'shop_owner_nic',
        'shop_owner_phone',
        'shop_owner_address',
        'shop_address',
        'agreement_start',
        'agreement_end',
        'reason',
        'changed_at',
    ];

    protected $casts = [
        'agreement_start' => 'date',
        'agreement_end' => 'date',
        'changed_at' => 'datetime',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }
}
