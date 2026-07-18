<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private function actingWithToken(User $user): string
    {
        return auth('api')->login($user);
    }

    public function test_list_users_requires_authentication(): void
    {
        $this->getJson('/api/v1/users')->assertUnauthorized();
    }

    public function test_authenticated_user_can_list_users(): void
    {
        $user = User::factory()->create();
        User::factory()->count(4)->create();
        $token = $this->actingWithToken($user);

        $this->getJson('/api/v1/users', ['Authorization' => "Bearer {$token}"])
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'users',
                    'meta' => ['current_page', 'last_page', 'per_page', 'total'],
                ],
            ])
            ->assertJsonPath('data.meta.total', 5);
    }

    public function test_user_can_get_their_profile(): void
    {
        $user = User::factory()->create();
        $token = $this->actingWithToken($user);

        $this->getJson('/api/v1/users/profile', ['Authorization' => "Bearer {$token}"])
            ->assertOk()
            ->assertJsonPath('data.user.email', $user->email);
    }

    public function test_user_can_get_another_user_by_id(): void
    {
        $requester = User::factory()->create();
        $target = User::factory()->create();
        $token = $this->actingWithToken($requester);

        $this->getJson("/api/v1/users/{$target->id}", ['Authorization' => "Bearer {$token}"])
            ->assertOk()
            ->assertJsonPath('data.user.id', $target->id);
    }

    public function test_show_returns_404_for_missing_user(): void
    {
        $user = User::factory()->create();
        $token = $this->actingWithToken($user);

        $this->getJson('/api/v1/users/9999', ['Authorization' => "Bearer {$token}"])
            ->assertNotFound()
            ->assertJsonPath('success', false);
    }

    public function test_user_can_update_their_profile(): void
    {
        $user = User::factory()->create();
        $token = $this->actingWithToken($user);

        $this->putJson('/api/v1/users/profile', [
            'name' => 'Updated Name',
            'bio' => 'My updated bio.',
        ], ['Authorization' => "Bearer {$token}"])
            ->assertOk()
            ->assertJsonPath('data.user.name', 'Updated Name')
            ->assertJsonPath('data.user.bio', 'My updated bio.');
    }

    public function test_profile_update_validates_input(): void
    {
        $user = User::factory()->create();
        $token = $this->actingWithToken($user);

        $this->putJson('/api/v1/users/profile', [
            'name' => str_repeat('a', 256),
        ], ['Authorization' => "Bearer {$token}"])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_user_can_update_another_user(): void
    {
        $requester = User::factory()->create();
        $target = User::factory()->create();
        $token = $this->actingWithToken($requester);

        $this->putJson("/api/v1/users/{$target->id}", [
            'name' => 'New Name',
        ], ['Authorization' => "Bearer {$token}"])
            ->assertOk()
            ->assertJsonPath('data.user.name', 'New Name');
    }

    public function test_user_can_soft_delete_a_user(): void
    {
        $requester = User::factory()->create();
        $target = User::factory()->create();
        $token = $this->actingWithToken($requester);

        $this->deleteJson("/api/v1/users/{$target->id}", [], ['Authorization' => "Bearer {$token}"])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertSoftDeleted('users', ['id' => $target->id]);
    }

    public function test_delete_returns_404_for_missing_user(): void
    {
        $user = User::factory()->create();
        $token = $this->actingWithToken($user);

        $this->deleteJson('/api/v1/users/9999', [], ['Authorization' => "Bearer {$token}"])
            ->assertNotFound();
    }
}
