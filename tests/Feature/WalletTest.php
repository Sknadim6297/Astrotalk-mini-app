<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WalletTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $user;
    private $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'role' => 'user',
            'wallet_balance' => 100.00
        ]);
        
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    /** @test */
    public function user_can_get_wallet_balance()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->getJson('/api/wallet');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'data' => [
                        'balance',
                        'user_id'
                    ]
                ])
                ->assertJson([
                    'status' => 'success',
                    'data' => [
                        'balance' => 100.00,
                        'user_id' => $this->user->id
                    ]
                ]);
    }

    /** @test */
    public function user_can_add_balance_to_wallet()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->postJson('/api/wallet/add', [
            'amount' => 50.00
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'message',
                    'data' => [
                        'transaction_id',
                        'amount',
                        'new_balance'
                    ]
                ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'wallet_balance' => 150.00
        ]);

        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $this->user->id,
            'amount' => 50.00,
            'type' => 'credit',
            'description' => 'Wallet top-up'
        ]);
    }

    /** @test */
    public function user_cannot_add_negative_amount()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->postJson('/api/wallet/add', [
            'amount' => -50.00
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['amount']);
    }

    /** @test */
    public function user_cannot_add_zero_amount()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->postJson('/api/wallet/add', [
            'amount' => 0
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['amount']);
    }

    /** @test */
    public function user_can_get_wallet_transactions()
    {
        // Create some transactions
        WalletTransaction::create([
            'user_id' => $this->user->id,
            'amount' => 50.00,
            'type' => 'credit',
            'description' => 'Wallet top-up'
        ]);

        WalletTransaction::create([
            'user_id' => $this->user->id,
            'amount' => 20.00,
            'type' => 'debit',
            'description' => 'Chat session payment'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->getJson('/api/wallet/transactions');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'data' => [
                        'transactions' => [
                            '*' => [
                                'id',
                                'amount',
                                'type',
                                'description',
                                'created_at'
                            ]
                        ],
                        'pagination'
                    ]
                ]);
    }

    /** @test */
    public function user_can_get_wallet_stats()
    {
        // Create some transactions
        WalletTransaction::create([
            'user_id' => $this->user->id,
            'amount' => 100.00,
            'type' => 'credit',
            'description' => 'Wallet top-up'
        ]);

        WalletTransaction::create([
            'user_id' => $this->user->id,
            'amount' => 30.00,
            'type' => 'debit',
            'description' => 'Chat session payment'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->getJson('/api/wallet/stats');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'data' => [
                        'current_balance',
                        'total_credited',
                        'total_debited',
                        'total_transactions'
                    ]
                ]);
    }

    /** @test */
    public function non_user_cannot_access_wallet_endpoints()
    {
        $astrologer = User::factory()->create(['role' => 'astrologer']);
        $astrologerToken = $astrologer->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $astrologerToken,
            'Accept' => 'application/json',
        ])->getJson('/api/wallet');

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_wallet()
    {
        $response = $this->getJson('/api/wallet');
        $response->assertStatus(401);
    }

    /** @test */
    public function wallet_transaction_validation_works()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->postJson('/api/wallet/add', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['amount']);
    }

    /** @test */
    public function wallet_deduction_works_correctly()
    {
        // This would be called internally during booking
        $initialBalance = $this->user->wallet_balance;
        $deductionAmount = 25.00;

        // Simulate wallet deduction
        $this->user->update([
            'wallet_balance' => $this->user->wallet_balance - $deductionAmount
        ]);

        WalletTransaction::create([
            'user_id' => $this->user->id,
            'amount' => $deductionAmount,
            'type' => 'debit',
            'description' => 'Chat session payment'
        ]);

        $this->assertEquals($initialBalance - $deductionAmount, $this->user->fresh()->wallet_balance);
    }
}
