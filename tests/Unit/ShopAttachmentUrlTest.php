<?php

namespace Tests\Unit;

use App\Models\Shop;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ShopAttachmentUrlTest extends TestCase
{
    public function test_attachment_url_uses_public_storage_url_when_file_exists(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('shops/test-image.jpg', 'test-image');

        $shop = new Shop();
        $url = $shop->getAttachmentUrl('shops/test-image.jpg');

        $this->assertSame(rtrim(url('/'), '/') . '/storage/shops/test-image.jpg', $url);
    }
}
