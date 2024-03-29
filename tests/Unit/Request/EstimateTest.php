<?php

use Kanakku\Models\Estimate;
use Kanakku\Http\Requests\EstimatesRequest;
use Kanakku\Rules\UniqueNumber;

test('estimate request validation rules', function () {
    $request = new EstimatesRequest;

    $this->assertEquals([
            'estimate_date' => [
                'required'
            ],
            'expiry_date' => [
                'required'
            ],
            'user_id' => [
                'required'
            ],
            'discount' => [
                'required'
            ],
            'discount_val' => [
                'required'
            ],
            'sub_total' => [
                'required'
            ],
            'total' => [
                'required'
            ],
            'tax' => [
                'required'
            ],
            'estimate_template_id' => [
                'required'
            ],
            'items' => [
                'required',
                'array'
            ],
            'items.*.description' => [
                'max:255'
            ],
            'items.*' => [
                'required',
                'max:255'
            ],
            'items.*.name' => [
                'required'
            ],
            'items.*.quantity' => [
                'required'
            ],
            'items.*.price' => [
                'required'
            ],
            'estimate_number' => [
                'required',
                new UniqueNumber(Estimate::class)
            ],
        ],
        $request->rules()
    );
});

test('estimate request authorize', function () {
    $request = new EstimatesRequest;

    $this->assertTrue($request->authorize());
});
