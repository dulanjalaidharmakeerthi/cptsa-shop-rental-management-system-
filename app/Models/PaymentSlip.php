<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSlip extends Model
{
    use HasFactory;

    protected $table = 'payment_slips';

    protected $fillable = [
        'payment_id', 'file_path', 'original_name', 'mime_type', 'size'
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }
}
