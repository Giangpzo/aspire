<?php

namespace Tests\Feature\Loan\RepaymentApi;

use App\Modules\Loan\Models\Loan;
use App\Modules\Loan\Models\ScheduledRepayment;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Tests\Feature\Loan\LoanTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RepayTest extends LoanTestCase
{
    use RefreshDatabase;

    # php artisan test --filter 'Tests\\Feature\\Loan\\RepaymentApi\\RepayTest::test_unauthorized_user_cannot_access_this_route'

    private string $url = '/api/v1/loans';

    private function calculateActualUrl($loan, $repayment)
    {
        return $this->url . '/' . $loan->id . '/scheduled-repayment/' . $repayment->id . '/repay';
    }

    private function createLoan($attributes = [])
    {
        $data = array_merge([
            'customer_id' => $this->customer->id,
            'status' => Loan::STATUS_APPROVED
        ], $attributes);

        return Loan::factory()->create($data);
    }

    private function createRepayment($loan, $attributes = [])
    {
        $data = array_merge([
            'loan_id' => $loan->id
        ], $attributes);

        return ScheduledRepayment::factory()->create($data);
    }

    public function test_unauthorized_user_cannot_access_this_route()
    {
        $loan = $this->createLoan();
        $repayment = $this->createRepayment($loan);
        $url = $this->calculateActualUrl($loan, $repayment);

        $response = $this->withHeaders(['Accept' => 'application/json'])->post($url);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_admin_cannot_repay(): void
    {
        $loan = $this->createLoan();
        $repayment = $this->createRepayment($loan);
        $url = $this->calculateActualUrl($loan, $repayment);

        $response = $this->actingAsAdmin()->post($url);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_customer_cannot_repay_another_one_repayment(): void
    {
        $loan = $this->createLoan();
        $repayment = $this->createRepayment($loan);
        $url = $this->calculateActualUrl($loan, $repayment);

        $response = $this->actingAsAnotherCustomer()->post($url);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertSee('The loan you are finding is not exist or not belong to you!');
    }

    public function test_validate_amount_cannot_be_string(): void
    {
        $loan = $this->createLoan();
        $repayment = $this->createRepayment($loan);
        $url = $this->calculateActualUrl($loan, $repayment);

        $response = $this->actingAsCustomer()->post($url, ['amount' => 'this is a text']);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_validate_amount_cannot_less_than_scheduled_amount(): void
    {
        $loan = $this->createLoan();
        $repayment = $this->createRepayment($loan, ['amount' => 4000]);
        $url = $this->calculateActualUrl($loan, $repayment);

        $response = $this->actingAsCustomer()->post($url, ['amount' => 3700]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_validate_cannot_repay_paid_repayment(): void
    {
        $loan = $this->createLoan();
        $repayment = $this->createRepayment($loan, ['amount' => 4000, 'status' => ScheduledRepayment::STATUS_PAID]);
        $url = $this->calculateActualUrl($loan, $repayment);

        $response = $this->actingAsCustomer()->post($url, ['amount' => 4001]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_customer_repay_owned_repayment(): void
    {
        $loan = $this->createLoan();
        $repayment = $this->createRepayment($loan, ['amount' => 4000, 'status' => ScheduledRepayment::STATUS_UNPAID]);
        $url = $this->calculateActualUrl($loan, $repayment);

        $response = $this->actingAsCustomer()->post($url, ['amount' => 4001]);

        $data = data_get($response->json(), 'data');
        $response->assertOk();
        $this->assertEquals(4001, data_get($data, 'actual_amount'));
        $this->assertEquals(ScheduledRepayment::STATUS_PAID, data_get($data, 'status'));

        $todayDate = today()->format('Y-m-d');
        $actualRepaymentDate = new \DateTime(data_get($data, 'actual_repayment_date'));
        $this->assertEquals($todayDate, $actualRepaymentDate->format('Y-m-d'));
    }

    public function test_customer_repay_all_repayments(): void
    {
        // generate 3 records of repayments automatically
        // repayment: amount = 15000/3=5000
        $loan = $this->createLoan(['amount' => 15000, 'term' => 3, 'status' => Loan::STATUS_APPROVED]);
        $customerHttp = $this->actingAsCustomer();

        foreach ($loan->repayments as $repayment) {
            $url = $this->calculateActualUrl($loan, $repayment);

            $response = $customerHttp->post($url, ['amount' => 5001]);
        }

        // if all repayments was paid, loan's status will be changed to paid
        $this->assertEquals(Loan::STATUS_PAID, $loan->refresh()->status);
    }
}
