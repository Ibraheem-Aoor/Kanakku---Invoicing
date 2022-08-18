<?php

namespace Database\Factories;

use Kanakku\Models\ExpenseCategory;
use Kanakku\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ExpenseCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'company_id' => User::where('role', 'super admin')->first()->company_id,
            'description' => $this->faker->text
        ];
    }
}