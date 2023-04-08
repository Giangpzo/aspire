<?php

namespace Tests\Unit\Loan\DataProvider;

use App\Modules\Auth\Models\User;
use App\Modules\Loan\DataProvider\LoanDataProvider;
use App\Modules\Loan\DataProvider\ScheduledRepaymentProvider;
use App\Modules\Loan\Models\Loan;
use App\Modules\Loan\Models\ScheduledRepayment;
use Tests\Unit\Loan\LoanTestCase;

class ScheduledRepaymentProviderTest extends LoanTestCase
{
    # php artisan test --filter 'Tests\\Unit\\Loan\\DataProvider\\ScheduledRepaymentProviderTest'

    private ScheduledRepaymentProvider $provider;

    public function setUp(): void
    {
        parent::setUp();

        $this->provider = app(ScheduledRepaymentProvider::class);
    }

    public function test_customer_get_repayments()
    {
        $loan = $this->createLoan();
        $repayments = ScheduledRepayment::factory(3)->create([
            'loan_id' => $loan->id,
            'status' => ScheduledRepayment::STATUS_PAID
        ]);

        $gotRepayments = $this->provider->getRepayments($loan);

        $this->assertEquals(3, $gotRepayments->count());
        $this->assertTrue($this->sameValue($repayments->pluck('id'), $gotRepayments->pluck('id')));
    }

    public function test_customer_get_empty_repayments()
    {
        $loan = $this->createLoan();

        $gotRepayments = $this->provider->getRepayments($loan);

        $this->assertEquals(0, $gotRepayments->count());
    }

    public function test_repay()
    {
        $loan = $this->createLoan(['status' => Loan::STATUS_APPROVED]);
        $repayment = $loan->repayments->first();

        $this->provider->repay($repayment, 5000);
        $repayment->refresh();

        $this->assertEquals($repayment->status, ScheduledRepayment::STATUS_PAID);
        $this->assertEquals($repayment->actual_amount, 5000);
    }
}