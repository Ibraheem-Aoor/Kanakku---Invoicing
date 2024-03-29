<?php

use Kanakku\Models\User;
use Kanakku\Models\CustomField;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use Kanakku\Http\Requests\CustomFieldRequest;
use Kanakku\Http\Controllers\V1\CustomField\CustomFieldsController;
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

test('get custom fields', function () {
    $response = getJson('api/v1/custom-fields?page=1');

    $response->assertOk();
});

test('create custom field', function () {
    $data = CustomField::factory()->raw();

    postJson('api/v1/custom-fields', $data)
        ->assertStatus(200)
        ->assertJson([
            'success' => true
        ]);

    $this->assertDatabaseHas('custom_fields', [
        'name' => $data['name'],
        'label' => $data['label'],
        'type' => $data['type'],
        'model_type' => $data['model_type'],
        'is_required' => $data['is_required']
    ]);
});

test('store validates using a form request', function () {
    $this->assertActionUsesFormRequest(
        CustomFieldsController::class,
        'store',
        CustomFieldRequest::class
    );
});

test('update custom field', function () {
    $customField = CustomField::factory()->create();

    $newCustomField = CustomField::factory()->raw([
        'is_required' => false
    ]);

    putJson('api/v1/custom-fields/' . $customField->id, $newCustomField)
        ->assertStatus(200)
        ->assertJson([
            'success' => true
        ]);

    $this->assertDatabaseHas('custom_fields', [
        'id' => $customField->id,
        'name' => $newCustomField['name'],
        'label' => $newCustomField['label'],
        'type' => $newCustomField['type'],
        'model_type' => $newCustomField['model_type'],
    ]);
});

test('update validates using a form request', function () {
    $this->assertActionUsesFormRequest(
        CustomFieldsController::class,
        'update',
        CustomFieldRequest::class
    );
});

test('delete custom field', function () {
    $customField = CustomField::factory()->create();

    $response = deleteJson('api/v1/custom-fields/' . $customField->id);

    $response
        ->assertOk()
        ->assertJson([
            'success' => true
        ]);

    $this->assertDeleted($customField);
});
