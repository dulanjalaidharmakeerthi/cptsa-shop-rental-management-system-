<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentDetail;
use App\Models\Payment;

class PaymentDetailController extends Controller
{
    /**
     * Store a newly created payment detail.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'shop_id' => ['nullable','integer'],
            'shop_number' => ['nullable','string'],
            'owner_name' => ['nullable','string'],
            'amount' => ['required','numeric'],
            'interest' => ['nullable','numeric'],
            'receipt_number' => ['required','string'],
            'paid_at' => ['nullable','date'],
        ]);

        // Optionally create a Payment record so existing reports continue to work
        $payment = Payment::create([
            'shop_id' => $data['shop_id'] ?? null,
            'amount' => $data['amount'],
            'interest' => $data['interest'] ?? 0,
            'paid_at' => $data['paid_at'] ?? now(),
        ]);

        $detail = PaymentDetail::create([
            'payment_id' => $payment->id,
            'shop_id' => $data['shop_id'] ?? null,
            'shop_number' => $data['shop_number'] ?? null,
            'owner_name' => $data['owner_name'] ?? null,
            'amount' => $data['amount'],
            'interest' => $data['interest'] ?? 0,
            'receipt_number' => $data['receipt_number'],
            'paid_at' => $data['paid_at'] ?? now(),
        ]);

        return response()->json(['success' => true, 'data' => $detail], 201);
    }
}
