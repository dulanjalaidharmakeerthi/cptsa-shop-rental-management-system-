<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\BusStand;
use App\Models\Admin;
use App\Models\ShopChargeHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    /**
     * Show the form for creating a new shop.
     */
    public function create()
    {
        $busStands = BusStand::orderBy('name')->get();
        $preselect = request()->query('bus_stand_id');
        return view('shops.create', compact('busStands', 'preselect'));
    }

    /**
     * Store a newly created shop in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'shop_name' => 'required|string|max:150',
            'shop_number' => 'required|string|max:20',
            'shop_owner_name' => 'required|string|max:150',
            'shop_owner_nic' => 'nullable|string|max:50',
            'shop_owner_phone' => 'nullable|string|max:40',
            'shop_owner_address' => 'nullable|string|max:255',
            'shop_address' => 'nullable|string|max:255',
            'agreement_duration' => 'nullable|string|max:50',
            'bus_stand_id' => 'nullable|integer|exists:bus_stands,id',
            'agreement_start' => 'nullable|date',
            'agreement_end' => 'nullable|date|after_or_equal:agreement_start',
            'monthly_rent' => 'required|numeric',
            'rate' => 'nullable|numeric',
            'shop_value' => 'nullable|numeric', // Added to match manage
            'advance_payment' => 'nullable|numeric',
            'opening_balance' => 'nullable|numeric',
            'total_paid' => 'nullable|numeric',
            'total_due' => 'nullable|numeric',
            'balance_amount' => 'nullable|numeric',
            'location' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'remarks' => 'nullable|string',
            'record_year' => 'required|digits:4',
        ]);

        // Default numeric values
        $numericDefaults = [
            'advance_payment', 'opening_balance', 'rate', 'shop_value',
            'total_paid', 'total_due', 'balance_amount',
        ];

        foreach ($numericDefaults as $k) {
            $data[$k] = $data[$k] ?? 0.00;
        }

        if ($request->hasFile('image')) {
            $fileName = 'shop_' . time() . '_' . $request->file('image')->getClientOriginalName();
            $data['image_path'] = $request->file('image')->storeAs('images', $fileName, 'public');
        }

        $shop = Shop::create($data);
        return redirect()->route('shops.agreement', $shop->id);
    }

    /**
     * Show printable agreement for a shop.
     */
    public function agreement($id)
    {
        $shop = Shop::with('busStand')->findOrFail($id);
        return view('shops.agreement', compact('shop'));
    }

    /**
     * Show shop management page.
     */
    public function manage()
    {
        $busStands = BusStand::orderBy('name')->get();
        return view('shops.management', compact('busStands'));
    }

    /**
     * Search shop by owner name, NIC, or Shop Number (AJAX).
     */
    public function search(Request $request)
    {
$q = trim($request->query('owner', $request->query('q', '')));
    if ($q === '') {
        return response()->json(['message' => 'Missing search query'], 400);
    }

    $qNormalized = strtolower($q);

        $shop = Shop::whereRaw('LOWER(shop_owner_name) like ?', ['%' . $qNormalized . '%'])
            ->orWhereRaw('LOWER(shop_owner_nic) like ?', ['%' . $qNormalized . '%'])
            ->orWhereRaw('LOWER(shop_number) like ?', ['%' . $qNormalized . '%'])
            ->orderBy('id', 'desc')
            ->first();

        if (!$shop) {
            return response()->json(['message' => 'No shop found'], 404);
        }

        $shop->setRelation('chargeHistories', $shop->chargeHistories()->get());

        return response()->json($shop);
    }

    /**
     * Store or update shop management data.
     */
    public function manageStore(Request $request)
    {
        $data = $request->validate([
            'shop_id' => 'nullable|integer|exists:shops,id',
            'bus_stand_id' => 'nullable|integer|exists:bus_stands,id',
            'shop_number' => 'required|string|max:20',
            'shop_owner_name' => 'required|string|max:150',
            'shop_value' => 'nullable|numeric',
            'monthly_rent' => 'nullable|numeric',
            'rate' => 'nullable|numeric',
            'year_charge' => 'nullable|numeric',
            'saving' => 'nullable|numeric',
            'due_payment' => 'nullable|numeric',
            'due_interest' => 'nullable|numeric',
            'year_interest' => 'nullable|numeric',
            'year_total_taken' => 'nullable|numeric',
            'deduct_payment' => 'nullable|numeric',
            'deduct_interest' => 'nullable|numeric',
            'agreement_start' => 'nullable|date',
            'agreement_end' => 'nullable|date|after_or_equal:agreement_start',
            'record_year' => 'nullable|digits:4',
        ]);

        $shop = null;
        if (!empty($data['shop_id'])) {
            $shop = Shop::find($data['shop_id']);
        }
        if (!$shop && !empty($data['shop_number'])) {
            $shop = Shop::where('shop_number', $data['shop_number'])->first();
        }

        // List of all numeric fields to be sanitized
        $numericFields = [
            'shop_value', 'monthly_rent', 'rate', 'year_charge', 'saving', 
            'due_payment', 'due_interest', 'year_interest', 
            'year_total_taken', 'deduct_payment', 'deduct_interest'
        ];

        foreach ($numericFields as $field) {
            if (!array_key_exists($field, $data)) {
                $data[$field] = $shop ? (float) ($shop->{$field} ?? 0) : 0.00;
            } else {
                $data[$field] = ($data[$field] !== '') ? (float) $data[$field] : 0.00;
            }
        }

        $data['record_year'] = $data['record_year'] ?? now()->year;

        if ($shop) {
            $changedBy = auth()->user()?->name;
            if ($changedBy === null && session()->has('admin_id')) {
                $changedBy = Admin::whereKey(session('admin_id'))->value('name');
            }

            DB::transaction(function () use ($shop, $data, $changedBy) {
                $oldChargeDetails = $this->formatChargeDetails([
                    'shop_value' => $shop->shop_value,
                    'rate' => $shop->rate,
                    'monthly_rent' => $shop->monthly_rent,
                    'year_charge' => $shop->year_charge,
                ]);
                $newChargeDetails = $this->formatChargeDetails($data);

                $oldOwnerData = [
                    'shop_owner_name' => $shop->shop_owner_name,
                    'shop_owner_nic' => $shop->shop_owner_nic,
                    'shop_owner_phone' => $shop->shop_owner_phone,
                    'shop_owner_address' => $shop->shop_owner_address,
                    'shop_address' => $shop->shop_address,
                    'agreement_start' => $shop->agreement_start,
                    'agreement_end' => $shop->agreement_end,
                ];

                if ($shop->shop_owner_name !== $data['shop_owner_name']) {
                    $shop->recordOwnerHistory($oldOwnerData, 'Owner reassigned in management screen');
                }

                $shop->update([
                    'shop_owner_name' => $data['shop_owner_name'],
                    'bus_stand_id' => $data['bus_stand_id'] ?? null,
                    'shop_value' => $data['shop_value'],
                    'monthly_rent' => $data['monthly_rent'],
                    'rate' => $data['rate'],
                    'year_charge' => $data['year_charge'],
                    'saving' => $data['saving'],
                    'due_payment' => $data['due_payment'],
                    'due_interest' => $data['due_interest'],
                    'year_interest' => $data['year_interest'],
                    'year_total_taken' => $data['year_total_taken'],
                    'deduct_payment' => $data['deduct_payment'],
                    'deduct_interest' => $data['deduct_interest'],
                    'agreement_start' => $data['agreement_start'] ?? null,
                    'agreement_end' => $data['agreement_end'] ?? null,
                    'record_year' => $data['record_year'],
                ]);

                if ($oldChargeDetails !== $newChargeDetails) {
                    ShopChargeHistory::create([
                        'shop_id' => $shop->id,
                        'old_values' => $oldChargeDetails,
                        'new_values' => $newChargeDetails,
                        'changed_by' => $changedBy,
                    ]);
                }
            });
        } else {
            $shop = Shop::create([
                'shop_number' => $data['shop_number'],
                'shop_owner_name' => $data['shop_owner_name'],
                'bus_stand_id' => $data['bus_stand_id'] ?? null,
                'shop_value' => $data['shop_value'],
                'monthly_rent' => $data['monthly_rent'],
                'rate' => $data['rate'],
                'year_charge' => $data['year_charge'],
                'saving' => $data['saving'],
                'due_payment' => $data['due_payment'],
                'due_interest' => $data['due_interest'],
                'year_interest' => $data['year_interest'],
                'year_total_taken' => $data['year_total_taken'],
                'deduct_payment' => $data['deduct_payment'],
                'deduct_interest' => $data['deduct_interest'],
                'agreement_start' => $data['agreement_start'] ?? null,
                'agreement_end' => $data['agreement_end'] ?? null,
                'record_year' => $data['record_year'],
            ]);
        }

        $managementRoute = $request->routeIs('admin.shop_management.update')
            ? 'admin.shop_management.index'
            : 'shops.manage';

        return redirect()->route($managementRoute)->with('success', 'Shop: ' . $shop->shop_number . ' updated successfully.');
    }

    private function formatChargeDetails(array $values): array
    {
        $rate = number_format((float) ($values['rate'] ?? 0), 2, '.', '');
        $monthlyRent = number_format((float) ($values['monthly_rent'] ?? 0), 2, '.', '');

        return [
            'rate' => $rate,
            'monthly_interest' => number_format((float) $monthlyRent * (float) $rate / 100, 2, '.', ''),
            'monthly_rent' => $monthlyRent,
        ];
    }
}