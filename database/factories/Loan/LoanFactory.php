<?php

namespace Database\Factories\Loan;

use App\Modules\Loan\Models\Loan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class LoanFactory extends Factory
{
    protected $model = Loan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => fake()->numberBetween(1, 3),
            'amount' => fake()->randomNumber(5, true),
            'term' => fake()->numberBetween(1, 5),
            'status' => fake()->numberBetween(Loan::STATUS_PENDING, Loan::STATUS_REJECTED),
            'approver_id' => fake()->numberBetween(1, 3),
            'approver_notes' => fake()->sentence()
        ];
    }
}
