<?php

namespace Tests\Unit\Loan\Policies;

use App\Modules\Auth\Models\User;
use App\Modules\Loan\Models\Loan;
use App\Modules\Loan\Policies\LoanPolicy;
use Tests\Unit\Loan\LoanTestCase;

class LoanPolicyTest extends LoanTestCase
{
    # php artisan test --filter 'Tests\\Unit\\Loan\\Policies\\LoanPolicyTest'

    private LoanPolicy $policy;

    public function setUp(): void
    {
        parent::setUp();

        $this->policy = app(LoanPolicy::class);
    }

    public function test_admin_can_view_all_loans()
    {
        $admin = $this->createUser(['type' => User::TYPE_ADMIN]);
        $loan1 = $this->createLoan();
        $loan2 = $this->createLoan();

        $canViewLoan1 = $this->policy->view($admin, $loan1);
        $canViewLoan2 = $this->policy->view($admin, $loan2);

        $this->assertTrue($canViewLoan1);
        $this->assertTrue($canViewLoan2);
    }

    public function test_customer_can_view_his_loan()
    {
        $customer = $this->createUser(['type' => User::TYPE_CUSTOMER]);
        $loan = $this->createLoan(['customer_id' => $customer->id]);

        $canViewLoan = $this->policy->view($customer, $loan);

        $this->assertTrue($canViewLoan);
    }

    public function test_customer_cannot_view_another_one_loan()
    {
        $customer1 = $this->createUser(['type' => User::TYPE_CUSTOMER]);
        $customer2 = $this->createUser(['type' => User::TYPE_CUSTOMER]);
        $loan = $this->createLoan(['customer_id' => $customer1->id]);

        $canViewLoan = $this->policy->view($customer2, $loan);

        $this->assertFalse($canViewLoan);
    }

    public function test_customer_cannot_approve_loan()
    {
        $customer = $this->createUser(['type' => User::TYPE_CUSTOMER]);
        $loan = $this->createLoan();

        $can = $this->policy->approve($customer, $loan);

        $this->assertFalse($can);
    }

    public function test_admin_cannot_approve_not_pending_loan()
    {
        $admin = $this->createUser(['type' => User::TYPE_ADMIN]);
        $loan1 = $this->createLoan(['status' => Loan::STATUS_PAID]);
        $loan2 = $this->createLoan(['status' => Loan::STATUS_REJECTED]);
        $loan3 = $this->createLoan(['status' => Loan::STATUS_APPROVED]);

        $can = $this->policy->approve($admin, $loan1);
        $can2 = $this->policy->approve($admin, $loan2);
        $can3 = $this->policy->approve($admin, $loan3);

        $this->assertFalse($can);
        $this->assertFalse($can2);
        $this->assertFalse($can3);
    }

    public function test_admin_can_approve_pending_loan()
    {
        $admin = $this->createUser(['type' => User::TYPE_ADMIN]);
        $loan = $this->createLoan(['status' => Loan::STATUS_PENDING]);

        $can = $this->policy->approve($admin, $loan);

        $this->assertTrue($can);
    }

    public function test_customer_cannot_reject_loan()
    {
        $customer = $this->createUser(['type' => User::TYPE_CUSTOMER]);
        $loan = $this->createLoan();

        $can = $this->policy->reject($customer, $loan);

        $this->assertFalse($can);
    }

    public function test_admin_cannot_reject_not_pending_loan()
    {
        $admin = $this->createUser(['type' => User::TYPE_ADMIN]);
        $loan1 = $this->createLoan(['status' => Loan::STATUS_PAID]);
        $loan2 = $this->createLoan(['status' => Loan::STATUS_REJECTED]);
        $loan3 = $this->createLoan(['status' => Loan::STATUS_APPROVED]);

        $can = $this->policy->reject($admin, $loan1);
        $can2 = $this->policy->reject($admin, $loan2);
        $can3 = $this->policy->reject($admin, $loan3);

        $this->assertFalse($can);
        $this->assertFalse($can2);
        $this->assertFalse($can3);
    }

    public function test_admin_can_reject_pending_loan()
    {
        $admin = $this->createUser(['type' => User::TYPE_ADMIN]);
        $loan = $this->createLoan(['status' => Loan::STATUS_PENDING]);

        $can = $this->policy->reject($admin, $loan);

        $this->assertTrue($can);
    }

    public function test_admin_cannot_request_loan()
    {
        $admin = $this->createUser(['type' => User::TYPE_ADMIN]);

        $can = $this->policy->requestLoan($admin, Loan::class);

        $this->assertFalse($can);
    }

    public function test_customer_can_request_loan()
    {
        $customer = $this->createUser(['type' => User::TYPE_CUSTOMER]);

        $can = $this->policy->requestLoan($customer, Loan::class);

        $this->assertTrue($can);
    }
}