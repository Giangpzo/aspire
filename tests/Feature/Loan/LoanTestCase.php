<?php

namespace Tests\Feature\Loan;

use App\Modules\Auth\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\TModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class LoanTestCase extends TestCase
{
    protected $admin;
    protected $customer;
    protected $anotherCustomer;

    private string $adminPassword = 'admin';
    private string $customerPassword = 'customer';
    private string $anotherCustomerPassword = 'another_customer';

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:install');

        $this->admin = $this->createUser($this->adminPassword, User::TYPE_ADMIN);
        $this->customer = $this->createUser($this->customerPassword, User::TYPE_CUSTOMER);
        $this->anotherCustomer = $this->createUser($this->anotherCustomerPassword, User::TYPE_CUSTOMER);
    }

    /**
     * Create user
     *
     * @param $password
     * @param int $type
     * @return Collection|TModel|Model
     */
    private function createUser($password, $type = User::TYPE_CUSTOMER)
    {
        return User::factory()->create([
            'type' => $type,
            'password' => $password
        ]);
    }

    /**
     * Login
     *
     * @param $email
     * @param $password
     * @return string
     */
    private function login($email, $password): string
    {
        $response = $this->post('/api/v1/auth/login', [
            'email' => $email,
            'password' => $password
        ]);

        return data_get($response->json()['data'], 'token');
    }

    /**
     * Acting as Admin
     *
     * @return LoanTestCase
     */
    protected function actingAsAdmin()
    {
        $token = $this->login($this->admin->email, $this->adminPassword);
        return $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ]);
    }

    /**
     * Acting as Customer
     *
     * @return LoanTestCase
     */
    protected function actingAsCustomer()
    {
        $token = $this->login($this->customer->email, $this->customerPassword);
        return $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ]);
    }

    /**
     * Acting as Customer
     *
     * @return LoanTestCase
     */
    protected function actingAsAnotherCustomer()
    {
        $token = $this->login($this->anotherCustomer->email, $this->anotherCustomerPassword);
        return $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ]);
    }

    protected function sameValue(SupportCollection $collection1, SupportCollection $collection2)
    {
        $diffItems = $collection1->diff($collection2);

        return $diffItems->isEmpty();
    }

    private function createPersonalAccessClients()
    {
        $clientRepository = new ClientRepository();
        $client = $clientRepository->createPersonalAccessClient(
            null, 'Test Personal Access Client', 'http://localhost'
        );

        DB::table('oauth_personal_access_clients')->insert([
            'client_id' => $client->id,
            'created_at' => new \DateTime,
            'updated_at' => new \DateTime,
        ]);
    }
}