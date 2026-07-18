<?php

use App\Models\User;
use App\UserStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

describe('register', function (): void {
    it('creates a user and returns a JWT token', function (): void {
        $response = postJson('/api/v1/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['user', 'access_token', 'token_type', 'expires_in'],
            ])
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.token_type', 'bearer');

        expect(User::where('email', 'john@example.com')->exists())->toBeTrue();
    });

    it('fails when email is already taken', function (): void {
        User::factory()->create(['email' => 'john@example.com']);

        postJson('/api/v1/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertUnprocessable();
    });

    it('fails with invalid payload', function (): void {
        postJson('/api/v1/auth/register', [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    });
});

describe('login', function (): void {
    it('returns a JWT token for valid credentials', function (): void {
        User::factory()->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('secret123'),
            'status' => UserStatus::Active,
        ]);

        postJson('/api/v1/auth/login', [
            'email' => 'jane@example.com',
            'password' => 'secret123',
        ])->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => ['user', 'access_token', 'token_type', 'expires_in'],
            ]);
    });

    it('rejects invalid credentials', function (): void {
        User::factory()->create(['email' => 'jane@example.com']);

        postJson('/api/v1/auth/login', [
            'email' => 'jane@example.com',
            'password' => 'wrongpassword',
        ])->assertUnauthorized()
            ->assertJsonPath('success', false);
    });

    it('rejects suspended users', function (): void {
        User::factory()->create([
            'email' => 'suspended@example.com',
            'password' => bcrypt('secret123'),
            'status' => UserStatus::Suspended,
        ]);

        postJson('/api/v1/auth/login', [
            'email' => 'suspended@example.com',
            'password' => 'secret123',
        ])->assertUnauthorized();
    });
});

describe('me', function (): void {
    it('returns the authenticated user', function (): void {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        getJson('/api/v1/auth/me', ['Authorization' => "Bearer {$token}"])
            ->assertOk()
            ->assertJsonPath('data.user.email', $user->email);
    });

    it('rejects unauthenticated requests', function (): void {
        getJson('/api/v1/auth/me')->assertUnauthorized();
    });
});

describe('logout', function (): void {
    it('invalidates the token', function (): void {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        postJson('/api/v1/auth/logout', [], ['Authorization' => "Bearer {$token}"])
            ->assertOk()
            ->assertJsonPath('success', true);

        getJson('/api/v1/auth/me', ['Authorization' => "Bearer {$token}"])
            ->assertUnauthorized();
    });
});

describe('refresh', function (): void {
    it('issues a new token', function (): void {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        postJson('/api/v1/auth/refresh', [], ['Authorization' => "Bearer {$token}"])
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['access_token', 'token_type', 'expires_in'],
            ]);
    });
});
