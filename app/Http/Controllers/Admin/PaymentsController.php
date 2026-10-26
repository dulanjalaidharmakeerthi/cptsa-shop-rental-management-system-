<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentDetail;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    /**
     * Display a listing of payments, optionally filtered by shop number.
     */
    public function index(Request $request)
    {
        $shopNumber = trim($request->query('shop_number', ''));

        $payments = Payment::with('shop')
            ->when($shopNumber !== '', function ($query) use ($shopNumber) {
                $query->whereHas('shop', function ($q) use ($shopNumber) {
                    $q->where('shop_number', 'like', "%{$shopNumber}%");
                });
            })
            ->orderBy('paid_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        $view = request()->routeIs('accountant.payments.*')
            ? 'accountant.payments.index'
            : 'admin.payments.index';

        return view($view, compact('payments', 'shopNumber'));
    }

    /**
     * Show the form for editing the specified payment.
     */
    public function edit($id)
    {
        $payment = Payment::with('shop')->findOrFail($id);
        $view = request()->routeIs('accountant.payments.*')
            ? 'accountant.payments.edit'
            : 'admin.payments.edit';

        return view($view, compact('payment'));
    }

    /**
     * Update the specified payment in storage.
     */
    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $validated = $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'amount' => 'nullable|numeric|min:0',
            'interest' => 'nullable|numeric|min:0',
            'receipt_number' => 'required|string|max:255',
            'paid_at' => 'required|date',
        ]);

        // apply changes to payment
        $payment->update($validated);

        // attempt to keep payment detail in sync, if exists
        $detail = PaymentDetail::where('payment_id', $payment->id)->first();
        if ($detail) {
            $detail->update([
                'shop_id' => $validated['shop_id'],
                'amount' => $validated['amount'] ?? 0,
                'interest' => $validated['interest'] ?? 0,
                'receipt_number' => $validated['receipt_number'],
                'paid_at' => $validated['paid_at'],
            ]);
        }

        $indexRoute = request()->routeIs('accountant.payments.*')
            ? 'accountant.payments.index'
            : 'admin.payments.index';

        return redirect()->route($indexRoute)->with('success', 'Payment updated successfully.');
    }
}
