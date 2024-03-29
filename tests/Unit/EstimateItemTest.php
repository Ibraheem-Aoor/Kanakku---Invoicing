<?php

use Kanakku\Models\User;
use Kanakku\Models\Estimate;
use Kanakku\Models\Item;
use Kanakku\Models\EstimateItem;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);

    $user = User::where('role', 'super admin')->first();
    $this->withHeaders([
        'company' => $user->company_id,
    ]);
    Sanctum::actingAs(
        $user,
        ['*']
    );
});

test('estimate item belongs to estimate', function () {
    $estimateItem = EstimateItem::factory()->forEstimate()->create();

    $this->assertTrue($estimateItem->estimate()->exists());
});

test('estimate item belongs to item', function () {
    $estimateItem = EstimateItem::factory()->create([
        'item_id' => Item::factory(),
        'estimate_id' => Estimate::factory()
    ]);

    $this->assertTrue($estimateItem->item()->exists());
});


test('estimate item has many taxes', function () {
    $estimateItem = EstimateItem::factory()->hasTaxes(5)->create([
        'estimate_id' => Estimate::factory()
    ]);

    $this->assertCount(5, $estimateItem->taxes);

    $this->assertTrue($estimateItem->taxes()->exists());
});
