<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentDetail extends Model
{
    use HasFactory;

    protected $table = 'payment_details';

    protected $fillable = [
        'payment_id', 'shop_id', 'shop_number', 'owner_name', 'amount', 'interest', 'saving_adjustment', 'receipt_number', 'paid_at', 'payable_months'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'interest' => 'decimal:2',
        'saving_adjustment' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }
}
