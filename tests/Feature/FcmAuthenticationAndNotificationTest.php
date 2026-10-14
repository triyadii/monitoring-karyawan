<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FcmAuthenticationAndNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_requires_fcm_token(): void
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => Hash::make('password123'),
            'password_updated_at' => now(),
        ]);

        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['fcm_token']);
    }

    public function test_first_login_sets_fcm_token(): void
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => Hash::make('password123'),
            'fcm_token' => null,
            'password_updated_at' => now(),
        ]);

        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 'password123',
            'fcm_token' => 'device_token_phone_1',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'username', 'fcm_token'],
                'authorization' => ['token', 'type', 'expires_in'],
            ]);

        $this->assertEquals('device_token_phone_1', $user->fresh()->fcm_token);
    }

    public function test_login_with_matching_fcm_token_succeeds(): void
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => Hash::make('password123'),
            'fcm_token' => 'device_token_phone_1',
            'password_updated_at' => now(),
        ]);

        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 'password123',
            'fcm_token' => 'device_token_phone_1',
        ]);

        $response->assertStatus(200);
    }

    public function test_login_with_different_fcm_token_is_blocked_with_403(): void
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => Hash::make('password123'),
            'fcm_token' => 'device_token_phone_1',
            'password_updated_at' => now(),
        ]);

        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 'password123',
            'fcm_token' => 'device_token_phone_2',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'error' => 'Device mismatch',
            ]);

        // Token in database remains unchanged
        $this->assertEquals('device_token_phone_1', $user->fresh()->fcm_token);
    }

    public function test_reset_fcm_token_public_endpoint_clears_token_and_allows_login_on_new_phone(): void
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => Hash::make('password123'),
            'fcm_token' => 'device_token_phone_1',
            'password_updated_at' => now(),
        ]);

        // Attempting login on phone 2 fails first
        $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 'password123',
            'fcm_token' => 'device_token_phone_2',
        ])->assertStatus(403);

        // Reset FCM token
        $resetResponse = $this->postJson('/api/reset-fcm-token', [
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $resetResponse->assertStatus(200)
            ->assertJson([
                'message' => 'FCM token berhasil direset. Silakan login di perangkat baru.',
            ]);

        $this->assertNull($user->fresh()->fcm_token);

        // Now login on phone 2 succeeds and sets the new token
        $loginResponse = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 'password123',
            'fcm_token' => 'device_token_phone_2',
        ]);

        $loginResponse->assertStatus(200);
        $this->assertEquals('device_token_phone_2', $user->fresh()->fcm_token);
    }

    public function test_reset_fcm_token_with_invalid_credentials_fails(): void
    {
        User::factory()->create([
            'username' => 'testuser',
            'password' => Hash::make('password123'),
            'fcm_token' => 'device_token_phone_1',
        ]);

        $response = $this->postJson('/api/reset-fcm-token', [
            'username' => 'testuser',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
    }

    public function test_admin_can_reset_user_fcm_token(): void
    {
        $admin = User::factory()->create([
            'password_updated_at' => now(),
        ]);
        $targetUser = User::factory()->create([
            'fcm_token' => 'locked_device_token',
            'password_updated_at' => now(),
        ]);

        $token = auth('api')->login($admin);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/users/{$targetUser->id}/reset-fcm-token");

        $response->assertStatus(200);
        $this->assertNull($targetUser->fresh()->fcm_token);
    }

    public function test_firebase_config_endpoint_returns_valid_config(): void
    {
        $response = $this->getJson('/api/firebase-config');

        $response->assertStatus(200)
            ->assertJson([
                'projectId' => 'mdtnotification',
                'apiKey' => 'AIzaSyBdoAaFK4obS82aO9D-oqWn5dYgYLFJKRM',
                'messagingSenderId' => '330219223368',
            ]);
    }

    public function test_send_notification_validation(): void
    {
        $user = User::factory()->create([
            'password_updated_at' => now(),
        ]);
        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/notifications/send-to-token', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['fcm_token', 'title', 'body']);
    }
}
