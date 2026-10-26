<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Shop;
use App\Models\PaymentSlip;
use Carbon\Carbon;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'shop_id', 'amount', 'interest', 'receipt_number', 'paid_at', 'saving_adjustment', 'month', 'year', 'monthly_payment', 'due_interest', 'payable_months'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'interest' => 'decimal:2',
        'saving_adjustment' => 'decimal:2',
        'paid_at' => 'datetime',
        'month' => 'integer',
        'year' => 'integer',
        'monthly_payment' => 'boolean',
        'due_interest' => 'boolean',
    ];

    protected $appends = ['payable_months_display'];

    public function getPayableMonthsDisplayAttribute(): string
    {
        if (!empty($this->attributes['payable_months'])) {
            return $this->attributes['payable_months'];
        }

        if (!empty($this->attributes['year']) && !empty($this->attributes['month'])) {
            return Carbon::create((int) $this->attributes['year'], (int) $this->attributes['month'], 1)->format('M Y');
        }

        return '-';
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function slips()
    {
        return $this->hasMany(PaymentSlip::class, 'payment_id');
    }
}
