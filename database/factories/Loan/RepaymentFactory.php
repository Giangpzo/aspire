<?php

namespace Database\Factories\Loan;

use App\Modules\Loan\Models\ScheduledRepayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class RepaymentFactory extends Factory
{
    protected $model = ScheduledRepayment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'loan_id' => fake()->numberBetween(1, 3),
            'repayment_date' => fake()->date('Y-m-d'),
            'amount' => fake()->numberBetween(4000, 5000),
            'status' => ScheduledRepayment::STATUS_UNPAID,
            'actual_repayment_date' => null,
            'actual_amount' => null
        ];
    }
}
