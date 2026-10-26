<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\BusStand;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Show the form for creating a new shop (Admin version)
     */
    public function create()
    {
        $busStands = BusStand::all();
        return view('admin.shops.create', ['busStands' => $busStands]);
    }

    /**
     * Store a newly created shop in storage (Admin version)
     */
    public function store(Request $request)
    {
        // Validate input (core required fields plus optional extras)
        $validated = $request->validate([
            'shop_number' => 'required|string|unique:shops,shop_number',
            'shop_name' => 'required|string',
            'shop_owner_name' => 'required|string',
            'shop_owner_nic' => 'required|string',
            'shop_owner_phone' => 'required|string',
            'monthly_rent' => 'required|numeric|min:0',
            'bus_stand_id' => 'required|exists:bus_stands,id',
            'agreement_start' => 'nullable|date',
            'agreement_end' => 'nullable|date|after:agreement_start',

            // additional optional columns from shops table
            'shop_owner_address' => 'nullable|string',
            'shop_address' => 'nullable|string',
            'shop_value' => 'nullable|numeric|min:0',
            'rate' => 'nullable|numeric|min:0',
            'advance_payment' => 'nullable|numeric|min:0',
            'opening_balance' => 'nullable|numeric|min:0',
            'saving' => 'nullable|numeric|min:0',
            'due_payment' => 'nullable|numeric|min:0',
            'due_interest' => 'nullable|numeric|min:0',
            'year_charge' => 'nullable|numeric|min:0',
            'year_interest' => 'nullable|numeric|min:0',
            'year_total_taken' => 'nullable|numeric|min:0',
            'deduct_payment' => 'nullable|numeric|min:0',
            'deduct_interest' => 'nullable|numeric|min:0',
            'agreement_duration' => 'nullable|string',
            'total_paid' => 'nullable|numeric|min:0',
            'total_due' => 'nullable|numeric|min:0',
            'balance_amount' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
            'record_year' => 'nullable|digits:4|integer',
            'location' => 'nullable|string',
            'image_path' => 'nullable|string',
            // (monthly rent fields removed - not present in DB)
        ]);

        // Set record_year to current year if not provided
        $validated['record_year'] = $validated['record_year'] ?? now()->year;

        // Normalize numeric fields that can arrive as explicit null
        $validated = $this->normalizeNumericDefaults($validated);

        // Create the shop
        $shop = Shop::create($validated);

        return redirect('/admin')
            ->with('success', 'Shop "' . $shop->shop_name . '" created successfully!');
    }

    /** Show manage-edit page with search */
    public function manageEdit()
    {
        $busStands = BusStand::all();
        return view('admin.shops.manage_edit', ['busStands' => $busStands]);
    }

    /** Search shops by number or owner (JSON) */
    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));
        if ($q === '') {
            return response()->json([]);
        }

        $shops = Shop::where('shop_number', 'like', "%{$q}%")
            ->orWhere('shop_owner_name', 'like', "%{$q}%")
            ->limit(30)
            ->get();

        return response()->json($shops);
    }

    /** Return a single shop record by id (JSON) */
    public function show($id)
    {
        $shop = Shop::findOrFail($id);
        return response()->json($shop);
    }

    /** Update shop (admin) */
    public function update(Request $request, $id)
    {
        $shop = Shop::findOrFail($id);

        $validated = $request->validate([
            'shop_number' => 'required|string|unique:shops,shop_number,' . $shop->id,
            'shop_name' => 'required|string',
            'shop_owner_name' => 'required|string',
            'shop_owner_nic' => 'nullable|string',
            'shop_owner_phone' => 'nullable|string',
            'monthly_rent' => 'nullable|numeric|min:0',
            'bus_stand_id' => 'nullable|exists:bus_stands,id',
            'agreement_start' => 'nullable|date',
            'agreement_end' => 'nullable|date|after:agreement_start',
            'remarks' => 'nullable|string',
            'owner_change_reason' => 'nullable|string|max:255',

            // extended columns
            'shop_owner_address' => 'nullable|string',
            'shop_address' => 'nullable|string',
            'shop_value' => 'nullable|numeric|min:0',
            'rate' => 'nullable|numeric|min:0',
            'advance_payment' => 'nullable|numeric|min:0',
            'opening_balance' => 'nullable|numeric|min:0',
            'saving' => 'nullable|numeric|min:0',
            'due_payment' => 'nullable|numeric|min:0',
            'due_interest' => 'nullable|numeric|min:0',
            'year_charge' => 'nullable|numeric|min:0',
            'year_interest' => 'nullable|numeric|min:0',
            'year_total_taken' => 'nullable|numeric|min:0',
            'deduct_payment' => 'nullable|numeric|min:0',
            'deduct_interest' => 'nullable|numeric|min:0',
            'agreement_duration' => 'nullable|string',
            'total_paid' => 'nullable|numeric|min:0',
            'total_due' => 'nullable|numeric|min:0',
            'balance_amount' => 'nullable|numeric|min:0',
            'record_year' => 'nullable|digits:4|integer',
            'location' => 'nullable|string',
            'image_path' => 'nullable|string',
            // (monthly rent fields removed - not present in DB)
        ]);

        $oldOwnerData = [
            'shop_owner_name' => $shop->shop_owner_name,
            'shop_owner_nic' => $shop->shop_owner_nic,
            'shop_owner_phone' => $shop->shop_owner_phone,
            'shop_owner_address' => $shop->shop_owner_address,
            'shop_address' => $shop->shop_address,
            'agreement_start' => $shop->agreement_start,
            'agreement_end' => $shop->agreement_end,
        ];

        $ownerChanged = (
            ($validated['shop_owner_name'] ?? $shop->shop_owner_name) !== $shop->shop_owner_name ||
            ($validated['shop_owner_nic'] ?? $shop->shop_owner_nic) !== $shop->shop_owner_nic ||
            ($validated['shop_owner_phone'] ?? $shop->shop_owner_phone) !== $shop->shop_owner_phone ||
            ($validated['shop_owner_address'] ?? $shop->shop_owner_address) !== $shop->shop_owner_address ||
            ($validated['shop_address'] ?? $shop->shop_address) !== $shop->shop_address ||
            ($validated['agreement_start'] ?? null) != optional($shop->agreement_start)->format('Y-m-d') ||
            ($validated['agreement_end'] ?? null) != optional($shop->agreement_end)->format('Y-m-d')
        );

        if ($ownerChanged) {
            $shop->recordOwnerHistory($oldOwnerData, $validated['owner_change_reason'] ?? 'Admin owner reassignment');
        }

        $validated = $this->normalizeNumericDefaults($validated);
        $shop->update($validated);

        return redirect()->back()->with('success', 'Shop updated successfully.');
    }

    private function normalizeNumericDefaults(array $data): array
    {
        $numericFields = [
            'shop_value',
            'monthly_rent',
            'rate',
            'advance_payment',
            'opening_balance',
            'saving',
            'due_payment',
            'due_interest',
            'year_charge',
            'year_interest',
            'year_total_taken',
            'deduct_payment',
            'deduct_interest',
            'total_paid',
            'total_due',
            'balance_amount',
        ];

        foreach ($numericFields as $field) {
            if (array_key_exists($field, $data) && $data[$field] === null) {
                $data[$field] = 0;
            }
        }

        return $data;
    }
}
