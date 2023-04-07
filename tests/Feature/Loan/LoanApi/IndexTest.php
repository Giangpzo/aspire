<?php

namespace Tests\Feature\Loan\LoanApi;

use App\Modules\Loan\Models\Loan;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Tests\Feature\Loan\LoanTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IndexTest extends LoanTestCase
{
    use RefreshDatabase;

    private string $url = '/api/v1/loans';

    public function test_unauthorized_user_cannot_access_this_route()
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])->get($this->url);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_admin_can_request_index(): void
    {
        $response = $this->actingAsAdmin()->get($this->url);

        $response->assertOk();
    }

    public function test_admin_can_view_empty_loans(): void
    {
        $response = $this->actingAsAdmin()->get($this->url);

        $response->assertOk();
        $this->assertCount(0, data_get($response->json(), 'data'));
        $this->assertEquals(0, data_get($response->json(), 'meta.pagination.total'));
    }

    public function test_admin_can_view_all_loans(): void
    {
        $loanCount = 3;

        $loans = Loan::factory()->count($loanCount)->create();
        $response = $this->actingAsAdmin()->get($this->url . '?' . Arr::query(['per_page' => $loanCount]));

        $data = data_get($response->json(), 'data');
        $response->assertOk();
        $this->assertCount($loans->count(), $data);
        $this->assertEquals($loans->count(), data_get($response->json(), 'meta.pagination.total'));
        $this->assertTrue($this->sameValue($loans->pluck('id'), collect($data)->pluck('id')));
    }

    public function test_customer_can_request_index(): void
    {
        $response = $this->actingAsCustomer()->get($this->url);

        $response->assertOk();
    }

    public function test_customer_can_view_empty_loans(): void
    {
        $response = $this->actingAsCustomer()->get($this->url);

        $response->assertOk();
        $this->assertCount(0, data_get($response->json(), 'data'));
        $this->assertEquals(0, data_get($response->json(), 'meta.pagination.total'));
    }

    public function test_customer_can_view_owned_loans(): void
    {
        $customerLoanCount = 3;
        $anotherLoanCount = 2;

        $customerLoans = Loan::factory()->count($customerLoanCount)->create(['customer_id' => $this->customer->id]);
        $anotherCustomerLoans = Loan::factory()->count($anotherLoanCount)->create(['customer_id' => $this->anotherCustomer->id]);
        $response = $this->actingAsCustomer()->get($this->url . '?' . Arr::query(['per_page' => $customerLoanCount + $anotherLoanCount]));

        $data = data_get($response->json(), 'data');
        $response->assertOk();
        $this->assertCount($customerLoans->count(), $data);
        $this->assertEquals($customerLoans->count(), data_get($response->json(), 'meta.pagination.total'));
        $this->assertTrue($this->sameValue($customerLoans->pluck('id'), collect($data)->pluck('id')));
    }
}
