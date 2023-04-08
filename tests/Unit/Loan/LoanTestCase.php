<?php

namespace Tests\Unit\Loan;

use App\Modules\Auth\Models\User;
use App\Modules\Loan\Models\Loan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\TModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection as SupportCollection;
use Tests\TestCase;

class LoanTestCase extends TestCase
{
    use RefreshDatabase;

    protected string $password = 'password';

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:install');
    }

    /**
     * Create user
     *
     * @param array $attributes
     * @return Collection|TModel|Model
     */
    protected function createUser($attributes = [])
    {
        $data = array_merge([
            'password' => $this->password
        ], $attributes);

        return User::factory()->create($data);
    }

    /**
     * Create Loan
     *
     * @param array $attributes
     * @return Collection|TModel|Model
     */
    protected function createLoan($attributes = [])
    {
        $customer = $this->createUser(['type' => User::TYPE_CUSTOMER]);

        $data = array_merge([
            'customer_id' => $customer->id,
            'status' => Loan::STATUS_PENDING
        ], $attributes);

        return Loan::factory()->create($data);
    }

    /**
     * Login
     *
     * @param $email
     * @param $password
     * @return string
     */
    protected function login($email, $password): string
    {
        $response = $this->post('/api/v1/auth/login', [
            'email' => $email,
            'password' => $password
        ]);

        return data_get($response->json()['data'], 'token');
    }

    /**
     * Check if two collection have same items
     *
     * @param SupportCollection $collection1
     * @param SupportCollection $collection2
     * @return bool
     */
    protected function sameValue(SupportCollection $collection1, SupportCollection $collection2)
    {
        $diffItems = $collection1->diff($collection2);

        return $diffItems->isEmpty();
    }
}