<?php

namespace Tests\Unit\Loan\Jobs;

use App\Modules\Auth\Models\User;
use App\Modules\Loan\DataProvider\LoanDataProvider;
use App\Modules\Loan\Jobs\RepaymentGeneratingJob;
use App\Modules\Loan\Models\Loan;
use App\Modules\Loan\Models\ScheduledRepayment;
use Tests\Unit\Loan\LoanTestCase;

class RepaymentGeneratingJobTest extends LoanTestCase
{
    # php artisan test --filter 'Tests\\Unit\\Loan\\Jobs\\RepaymentGeneratingJobTest'

    public function test_generate_1_repayments()
    {
        $loan = $this->createLoan(['amount' => 15, 'term' => 1]);
        $job = new RepaymentGeneratingJob($loan);

        $job->handle();
        $loan->refresh();

        foreach ($loan->repayments as $repayment) {
            $this->assertEquals($repayment->amount, 15 / 1);
            $this->assertEquals($repayment->loan_id, $loan->id);
            $this->assertEquals($repayment->status, ScheduledRepayment::STATUS_UNPAID);
        }
    }

    public function test_generate_3_repayments()
    {
        $loan = $this->createLoan(['amount' => 15, 'term' => 3]);
        $job = new RepaymentGeneratingJob($loan);

        $job->handle();
        $loan->refresh();

        foreach ($loan->repayments as $repayment) {
            $this->assertEquals($repayment->amount, 15 / 3);
            $this->assertEquals($repayment->loan_id, $loan->id);
            $this->assertEquals($repayment->status, ScheduledRepayment::STATUS_UNPAID);
        }
    }
}