<?php

namespace Database\Factories;

use Kanakku\Models\Expense;
use Kanakku\Models\User;
use Kanakku\Models\ExpenseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Expense::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'expense_date' => $this->faker->date('Y-m-d', 'now'),
            'expense_category_id' => ExpenseCategory::factory(),
            'company_id' => User::where('role', 'super admin')->first()->company_id,
            'amount' => $this->faker->randomDigitNotNull,
            'notes' => $this->faker->text,
            'attachment_receipt' => null
        ];
    }
}
