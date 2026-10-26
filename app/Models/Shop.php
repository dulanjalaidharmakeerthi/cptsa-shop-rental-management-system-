<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\BusStand;
use App\Models\ShopOwnerHistory;

class Shop extends Model
{
    use HasFactory;

    protected $table = 'shops';

    public function getAttachmentUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        // If it's already a full URL, return as-is
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        $relativePath = ltrim($path, '/');

        // Handle paths stored in storage/app/public (from storeAs with 'public' disk)
        if (str_starts_with($relativePath, 'images/')) {
            return rtrim(url('/'), '/') . '/storage/' . $relativePath;
        }

        // Handle old storage symlink paths (for backward compatibility)
        if (str_starts_with($relativePath, 'storage/')) {
            $relativePath = ltrim(substr($relativePath, 8), '/');
            return rtrim(url('/'), '/') . '/storage/' . $relativePath;
        }

        // Default fallback for other paths
        return rtrim(url('/'), '/') . '/storage/' . $relativePath;
    }

    protected $fillable = [
        'shop_name',
        'shop_number',
        'shop_owner_name',
        'shop_owner_nic',
        'agreement_duration',
        'agreement_start',
        'agreement_end',
        'bus_stand_id',
        'shop_value', // <--- ADDED THIS
        'monthly_rent',
        'rate',
        'advance_payment',
        'opening_balance',
        'rent_january','rent_february','rent_march','rent_april','rent_may','rent_june','rent_july','rent_august','rent_september','rent_october','rent_november','rent_december',
        'total_paid','total_due','balance_amount',
        'remarks','record_year',
        'location','image_path','image1_path','image2_path','image3_path','document_path',
        'shop_owner_phone','shop_owner_address','shop_address',
        // Management page extra fields
        'saving','due_payment','due_interest','year_charge','year_interest','year_total_taken','deduct_payment','deduct_interest','last_rent_charged','last_interest_charged',
    ];

    protected $casts = [
        'shop_value' => 'decimal:2', // <--- ADDED THIS
        'monthly_rent' => 'decimal:2',
        'advance_payment' => 'decimal:2',
        'rate' => 'decimal:2',
        'opening_balance' => 'decimal:2',
        'saving' => 'decimal:2',
        'due_payment' => 'decimal:2',
        'due_interest' => 'decimal:2',
        'year_charge' => 'decimal:2',
        'year_interest' => 'decimal:2',
        'year_total_taken' => 'decimal:2',
        'deduct_payment' => 'decimal:2',
        'deduct_interest' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'total_due' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'agreement_start' => 'date',
        'agreement_end' => 'date',
        'last_rent_charged' => 'date',
        'last_interest_charged' => 'date',
    ];

    /**
     * Relation to bus stand.
     */
    public function busStand()
    {
        return $this->belongsTo(BusStand::class, 'bus_stand_id');
    }

    /**
     * Payments for this shop.
     */
    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class, 'shop_id');
    }

    public function ownerHistories()
    {
        return $this->hasMany(ShopOwnerHistory::class, 'shop_id')->orderBy('changed_at', 'desc');
    }

    public function chargeHistories()
    {
        return $this->hasMany(ShopChargeHistory::class, 'shop_id')->orderBy('created_at', 'desc');
    }

    public function recordOwnerHistory(array $oldData, ?string $reason = null)
    {
        // Only create history if old owner name exists
        if (empty($oldData['shop_owner_name'])) {
            return;
        }

        $this->ownerHistories()->create([
            'shop_owner_name' => $oldData['shop_owner_name'] ?? null,
            'shop_owner_nic' => $oldData['shop_owner_nic'] ?? null,
            'shop_owner_phone' => $oldData['shop_owner_phone'] ?? null,
            'shop_owner_address' => $oldData['shop_owner_address'] ?? null,
            'shop_address' => $oldData['shop_address'] ?? null,
            'agreement_start' => $oldData['agreement_start'] ?? null,
            'agreement_end' => $oldData['agreement_end'] ?? null,
            'reason' => $reason,
            'changed_at' => now(),
        ]);
    }
}
