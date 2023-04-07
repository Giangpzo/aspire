<?php

namespace Tests\Feature\Loan\LoanApi;

use App\Modules\Loan\Models\Loan;
use Illuminate\Http\Response;
use Tests\Feature\Loan\LoanTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowTest extends LoanTestCase
{
    use RefreshDatabase;

    private string $url = '/api/v1/loans';

    public function test_unauthorized_user_cannot_access_this_route()
    {
        $loan = Loan::factory()->create();

        $response = $this->withHeaders(['Accept' => 'application/json'])->get($this->url . '/' . $loan->id);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_admin_can_request_this_route(): void
    {
        $loan = Loan::factory()->create();

        $response = $this->actingAsAdmin()->get($this->url . '/' . $loan->id);

        $response->assertOk();
        $response->assertSee('success');
    }

    public function test_customer_can_view_his_loan(): void
    {
        $loan = Loan::factory()->create(['customer_id' => $this->customer->id]);

        $response = $this->actingAsCustomer()->get($this->url . '/' . $loan->id);

        $response->assertOk();
        $response->assertSee('success');
        $this->assertEquals($loan->id, data_get($response->json(), 'data.id'));
    }

    public function test_customer_cannot_show_other_customer_loan(): void
    {
        $loan = Loan::factory()->create(['customer_id' => $this->anotherCustomer->id]);

        $response = $this->actingAsCustomer()->get($this->url . '/' . $loan->id);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertSee('The loan you are finding is not exist or not belong to you!');
    }
}
