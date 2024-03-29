<?php

use Kanakku\Models\User;
use Kanakku\Models\Unit;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use Kanakku\Http\Requests\UnitRequest;
use Kanakku\Http\Controllers\V1\Item\UnitsController;
use function Pest\Laravel\{postJson, putJson, getJson, deleteJson};

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);

    $user = User::find(1);
    $this->withHeaders([
        'company' => $user->company_id,
    ]);
    Sanctum::actingAs(
        $user,
        ['*']
    );
});

test('get units', function () {
    $response = getJson('api/v1/units?page=1');

    $response->assertOk();
});

test('create unit', function () {
    $data = [
        'name' => 'unit name',
        'company_id' => User::find(1)->company_id
    ];

    $response = postJson('api/v1/units', $data);

    $response->assertOk();

    $this->assertDatabaseHas('units', $data);
});

test('store validates using a form request', function () {
    $this->assertActionUsesFormRequest(
        UnitsController::class,
        'store',
        UnitRequest::class
    );
});

test('get unit', function () {
    $unit = Unit::factory()->create();

    $response = getJson("api/v1/units/{$unit->id}");

    $response->assertOk();

    $this->assertDatabaseHas('units', [
        'id' => $unit->id,
        'name' => $unit['name'],
    ]);
});

test('update unit', function () {
    $unit = Unit::factory()->create();

    $update_unit = [
        'name' => 'new name',
    ];

    $response = putJson("api/v1/units/{$unit->id}", $update_unit);

    $response->assertOk();

    $this->assertDatabaseHas('units', [
        'id' => $unit->id,
        'name' => $update_unit['name'],
    ]);
});

test('update validates using a form request', function () {
    $this->assertActionUsesFormRequest(
        UnitsController::class,
        'update',
        UnitRequest::class
    );
});

test('delete unit', function () {
    $unit = Unit::factory()->create();

    $response = deleteJson("api/v1/units/{$unit->id}");

    $response->assertOk();

    $this->assertDeleted($unit);
});
