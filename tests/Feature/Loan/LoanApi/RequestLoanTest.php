<?php

namespace Tests\Feature\Loan\LoanApi;

use Illuminate\Http\Response;
use Tests\Feature\Loan\LoanTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RequestLoanTest extends LoanTestCase
{
    use RefreshDatabase;

    private string $url = '/api/v1/loans/request-loan';

    public function test_unauthorized_user_cannot_access_this_route()
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])->post($this->url);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_validate_empty_amount(): void
    {
        $response = $this->actingAsCustomer()->post($this->url, ['term' => 3]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertSee('errors');
        $response->assertSee('amount');
    }

    public function test_validate_incorrect_amount(): void
    {
        $response = $this->actingAsCustomer()->post($this->url, ['term' => 3, 'amount' => 'this is text']);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertSee('errors');
        $response->assertSee('amount');
    }

    public function test_validate_empty_term(): void
    {
        $response = $this->actingAsCustomer()->post($this->url, ['amount' => 15000]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertSee('errors');
        $response->assertSee('term');
    }

    public function test_validate_incorrect_term(): void
    {
        $response = $this->actingAsCustomer()->post($this->url, ['amount' => 15000, 'term' => 'this is text']);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertSee('errors');
        $response->assertSee('term');
    }

    public function test_validate_empty_all_params(): void
    {
        $response = $this->actingAsCustomer()->post($this->url);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertSee('errors');
    }

    public function test_customer_request_success(): void
    {
        $response = $this->actingAsCustomer()->post($this->url, ['amount' => 15000, 'term' => 3]);

        $response->assertOk();
        $response->assertSee('requested loan success');
        $this->assertEquals(15000, data_get($response->json(), 'data.amount'));
        $this->assertEquals(3, data_get($response->json(), 'data.term'));
    }

    public function test_admin_cannot_request_loan(): void
    {
        $response = $this->actingAsAdmin()->post($this->url, ['amount' => 15000, 'term' => 3]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
