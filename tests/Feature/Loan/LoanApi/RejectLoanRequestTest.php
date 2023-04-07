<?php

namespace Tests\Feature\Loan\LoanApi;

use App\Modules\Auth\Models\User;
use App\Modules\Loan\Models\Loan;
use Illuminate\Http\Response;
use Tests\Feature\Loan\LoanTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\ClientRepository;
use Tests\Helpers\Helpers;
use Tests\TestCase;

class RejectLoanRequestTest extends LoanTestCase
{
    use RefreshDatabase;

    private string $url = '/api/v1/loans';

    private function calculateActualUrl($loan)
    {
        return $this->url . '/' . $loan->id . '/reject';
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
        $loan = Loan::factory()->create();
        $response = $this->withHeaders(['Accept' => 'application/json'])->post($this->calculateActualUrl($loan));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_validate_notes_params_too_long(): void
    {
        $loan = $this->createLoan();

        $notes = Helpers::generate_string_by_length(256);
        $url = $this->calculateActualUrl($loan);
        $response = $this->actingAsAdmin()->post($url, ['notes' => $notes]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertSee('errors');
        $response->assertSee('notes');
    }

    public function test_customer_cannot_reject_another_one_loan_request(): void
    {
        $loan = $this->createLoan();
        $url = $this->calculateActualUrl($loan);
        $notes = Helpers::generate_string_by_length(255);

        $response = $this->actingAsAnotherCustomer()->post($url, ['notes' => $notes]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertSee('The loan you are finding is not exist or not belong to you!');
    }

    public function test_customer_cannot_reject_his_loan_request(): void
    {
        $loan = $this->createLoan();
        $url = $this->calculateActualUrl($loan);
        $notes = Helpers::generate_string_by_length(255);

        $response = $this->actingAsCustomer()->post($url, ['notes' => $notes]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_admin_cannot_reject_approved_loan_request(): void
    {
        $loan = $this->createLoan(['status' => Loan::STATUS_APPROVED]);
        $url = $this->calculateActualUrl($loan);
        $notes = Helpers::generate_string_by_length(255);

        $response = $this->actingAsAdmin()->post($url, ['notes' => $notes]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_admin_cannot_reject_rejected_loan_request(): void
    {
        $loan = $this->createLoan(['status' => Loan::STATUS_REJECTED]);
        $url = $this->calculateActualUrl($loan);
        $notes = Helpers::generate_string_by_length(255);

        $response = $this->actingAsAdmin()->post($url, ['notes' => $notes]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_admin_cannot_reject_paid_loan_request(): void
    {
        $loan = $this->createLoan(['status' => Loan::STATUS_PAID]);
        $url = $this->calculateActualUrl($loan);
        $notes = Helpers::generate_string_by_length(255);

        $response = $this->actingAsAdmin()->post($url, ['notes' => $notes]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_admin_reject_pending_loan_request(): void
    {
        $loan = $this->createLoan();
        $url = $this->calculateActualUrl($loan);
        $notes = Helpers::generate_string_by_length(255);

        $response = $this->actingAsAdmin()->post($url, ['notes' => $notes]);

        $response->assertOk();
        $response->assertSee('success');
    }
}
