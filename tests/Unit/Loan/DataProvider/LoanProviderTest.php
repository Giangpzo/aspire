<?php

namespace Tests\Unit\Loan\DataProvider;

use App\Modules\Auth\Models\User;
use App\Modules\Loan\DataProvider\LoanDataProvider;
use App\Modules\Loan\Models\Loan;
use App\Modules\Loan\Models\ScheduledRepayment;
use Tests\Unit\Loan\LoanTestCase;

class LoanProviderTest extends LoanTestCase
{
    # php artisan test --filter 'Tests\\Unit\\Loan\\DataProvider\\LoanProviderTest::test_request_loan_function'

    private LoanDataProvider $provider;

    public function setUp(): void
    {
        parent::setUp();

        $this->provider = app(LoanDataProvider::class);
    }

    public function test_request_loan_function()
    {
        $customer = $this->createUser(['type' => User::TYPE_CUSTOMER]);
        $this->login($customer->email, $this->password);
        $amount = 15000;
        $term = 3;

        $loan = $this->provider->requestLoan($amount, $term);

        $this->assertEquals($loan->status, Loan::STATUS_PENDING);
        $this->assertEquals($loan->customer_id, $customer->id);
    }

    public function test_approve_loan_function()
    {
        $admin = $this->createUser(['type' => User::TYPE_ADMIN]);
        $note = 'test approve';
        $this->login($admin->email, $this->password);
        $loan = $this->createLoan();

        $this->provider->approveLoan($loan, $note);
        $loan->refresh();

        $this->assertEquals($loan->status, Loan::STATUS_APPROVED);
        $this->assertEquals($loan->approver_id, $admin->id);
        $this->assertEquals($loan->approver_notes, $note);
    }

    public function test_reject_loan_function()
    {
        $admin = $this->createUser(['type' => User::TYPE_ADMIN]);
        $note = 'test reject';
        $this->login($admin->email, $this->password);
        $loan = $this->createLoan();

        $this->provider->rejectLoan($loan, $note);
        $loan->refresh();

        $this->assertEquals($loan->status, Loan::STATUS_REJECTED);
        $this->assertEquals($loan->approver_id, $admin->id);
        $this->assertEquals($loan->approver_notes, $note);
    }

    public function test_should_update_status_to_paid_success()
    {
        $loan = $this->createLoan();
        $repayment = ScheduledRepayment::factory()->create([
            'loan_id' => $loan->id,
            'status' => ScheduledRepayment::STATUS_PAID
        ]);

        $this->provider->shouldUpdateStatusToPaid($loan);
        $loan->refresh();

        $this->assertEquals($loan->status, Loan::STATUS_PAID);
    }

    public function test_should_update_status_to_paid_not_success()
    {
        $loan = $this->createLoan();
        $repayment = ScheduledRepayment::factory()->create([
            'loan_id' => $loan->id,
            'status' => ScheduledRepayment::STATUS_UNPAID
        ]);

        $this->provider->shouldUpdateStatusToPaid($loan);
        $loan->refresh();

        $this->assertNotEquals($loan->status, Loan::STATUS_PAID);
    }
}