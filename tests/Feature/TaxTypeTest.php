<?php

use Kanakku\Models\User;
use Kanakku\Models\TaxType;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use Kanakku\Http\Requests\TaxTypeRequest;
use Kanakku\Http\Controllers\V1\Settings\TaxTypesController;

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

test('get tax types', function () {
    $response = getJson('api/v1/tax-types');

    $response->assertOk();
});

test('create tax type', function () {
    $taxType = TaxType::factory()->raw();

    postJson('api/v1/tax-types', $taxType);

    $this->assertDatabaseHas('tax_types', $taxType);
});

test('store validates using a form request', function () {
    $this->assertActionUsesFormRequest(
        TaxTypesController::class,
        'store',
        TaxTypeRequest::class
    );
});

test('get tax type', function () {
    $taxType = TaxType::factory()->create();

    $response = getJson('api/v1/tax-types/' . $taxType->id);

    $response->assertOk()
        ->assertJson([
            'taxType' => $taxType->toArray()
        ]);
});

test('update tax type', function () {
    $taxType = TaxType::factory()->create();

    $taxType1 = TaxType::factory()->raw();

    $response = putJson('api/v1/tax-types/' . $taxType->id, $taxType1);

    $response->assertOk()
        ->assertJson([
            'taxType' => $taxType1
        ]);
});

test('update validates using a form request', function () {
    $this->assertActionUsesFormRequest(
        TaxTypesController::class,
        'update',
        TaxTypeRequest::class
    );
});

test('delete tax type', function () {
    $taxType = TaxType::factory()->create();

    $response = deleteJson('api/v1/tax-types/' . $taxType->id);

    $response->assertOk()
        ->assertJson([
            'success' => true
        ]);

    $this->assertDeleted($taxType);
});
