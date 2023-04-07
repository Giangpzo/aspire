<?php

namespace Tests\Feature\Loan\RepaymentApi;

use App\Modules\Loan\Models\Loan;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Tests\Feature\Loan\LoanTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IndexTest extends LoanTestCase
{
    use RefreshDatabase;

    private string $url = '/api/v1/loans';

    private function calculateActualUrl($loan)
    {
        return $this->url . '/' . $loan->id . '/scheduled-repayment';
    }

    private function createLoan($attributes = [])
    {
        $data = array_merge([
            'customer_id' => $this->customer->id,
            'status' => Loan::STATUS_PENDING
        ], $attributes);

        return Loan::factory()->create($data);
    }

    public function test_unauthorized_user_cannot_access_this_route()
    {
        $loan = $this->createLoan();
        $response = $this->withHeaders(['Accept' => 'application/json'])->get($this->calculateActualUrl($loan));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_customer_cannot_view_another_one_repayments(): void
    {
        $loan = $this->createLoan(['customer_id' => $this->anotherCustomer->id]);
        $url = $this->calculateActualUrl($loan);

        $response = $this->actingAsCustomer()->get($url);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_customer_only_view_owned_repayment(): void
    {
        $loan = $this->createLoan();
        $url = $this->calculateActualUrl($loan);

        $response = $this->actingAsCustomer()->get($url);

        $response->assertOk();
        // because loan has just created without approving, so repayments temporary empty!
        $this->assertCount(0, data_get($response->json(), 'data'));
    }

    public function test_admin_can_view_every_repayment(): void
    {
        $loan = $this->createLoan();
        $url = $this->calculateActualUrl($loan);

        $response = $this->actingAsAdmin()->get($url);

        $response->assertOk();
        // because loan has just created without approving, so repayments temporary empty! --> tested in next case
        $this->assertCount(0, data_get($response->json(), 'data'));
    }

    public function test_admin_view_repayments_of_approved_loan(): void
    {
        $loan = $this->createLoan(['amount' => 15000, 'term' => 3]); // 3 repayments
        $adminHttp = $this->actingAsAdmin();

        $adminHttp->post($this->url . '/' . $loan->id . '/approve', ['notes' => 'test with sync queue']);

        $listRepaymentsUrl = $this->calculateActualUrl($loan);
        $response = $adminHttp->get($listRepaymentsUrl);

        $response->dump('data');

        $response->assertOk();
        $this->assertCount(3, data_get($response->json(), 'data'));
    }
}
