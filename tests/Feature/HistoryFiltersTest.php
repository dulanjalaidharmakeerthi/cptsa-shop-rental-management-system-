<?php

namespace Tests\Feature;

use App\Models\BusStand;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoryFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_history_filters_by_shop_stand_owner_name_and_id_together(): void
    {
        $standA = BusStand::create(['name' => 'Central Stand']);
        $standB = BusStand::create(['name' => 'North Stand']);

        $match = $this->createShop($standA->id, 'C-101', 'Central Kiosk', 'Nimal Perera', '991234567V');
        $this->createShop($standA->id, 'C-102', 'Central Cafe', 'Nimal Perera', '991234567V');
        $this->createShop($standB->id, 'N-101', 'North Kiosk', 'Nimal Perera', '991234567V');
        $this->createShop($standA->id, 'C-103', 'Central Bookshop', 'Other Owner', '881234567V');

        $response = $this->actingAs(User::factory()->create())->get(route('history', [
            'shop_number' => 'C-101',
            'bus_stand_id' => $standA->id,
            'owner_name' => 'Nimal',
            'owner_nic' => '991234567',
        ]));

        $response->assertOk();
        $response->assertViewHas('shops', function ($shops) use ($match) {
            $this->assertSame([$match->id], $shops->pluck('id')->all());
            return true;
        });
        $response->assertViewHas('busStands', function ($busStands) use ($standA, $standB) {
            $this->assertEqualsCanonicalizing([$standA->id, $standB->id], $busStands->pluck('id')->all());
            return true;
        });
    }

    public function test_history_can_search_shop_by_name_and_owner_by_id_number(): void
    {
        $stand = BusStand::create(['name' => 'Main Stand']);
        $match = $this->createShop($stand->id, 'M-201', 'Market Arcade', 'Saman Silva', '200012345678');
        $this->createShop($stand->id, 'M-202', 'Market Annex', 'Other Owner', '199912345678');

        $response = $this->actingAs(User::factory()->create())->get(route('history', [
            'shop_number' => 'Arcade',
            'owner_nic' => '200012345678',
        ]));

        $response->assertOk();
        $response->assertViewHas('shops', function ($shops) use ($match) {
            $this->assertSame([$match->id], $shops->pluck('id')->all());
            return true;
        });
    }

    private function createShop(int $busStandId, string $number, string $name, string $owner, string $nic): Shop
    {
        return Shop::create([
            'shop_number' => $number,
            'shop_name' => $name,
            'shop_owner_name' => $owner,
            'shop_owner_nic' => $nic,
            'bus_stand_id' => $busStandId,
            'monthly_rent' => 1000,
            'record_year' => 2026,
        ]);
    }
}
