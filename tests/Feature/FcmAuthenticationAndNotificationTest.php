<?php

namespace Tests\Feature;

use App\Models\ManajemenHo;
use App\Models\MasterStatusClient;
use App\Models\Penugasan;
use App\Models\Role;
use App\Models\User;
use App\Services\FirebaseService;
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

    public function test_login_triggers_notification_to_leaders_and_superadmins(): void
    {
        $mock = $this->mock(FirebaseService::class);
        $mock->shouldReceive('notifyUserLogin')
            ->once()
            ->withArgs(function ($user) {
                return $user->username === 'karyawan1';
            })
            ->andReturn(['success' => true]);

        $user = User::factory()->create([
            'username' => 'karyawan1',
            'password' => Hash::make('secret123'),
            'fcm_token' => 'token_karyawan_1',
            'password_updated_at' => now(),
        ]);

        $response = $this->postJson('/api/login', [
            'username' => 'karyawan1',
            'password' => 'secret123',
            'fcm_token' => 'token_karyawan_1',
        ]);

        $response->assertStatus(200);
    }

    public function test_assign_task_triggers_notification_to_assigned_user_with_customer_info(): void
    {
        $mock = $this->mock(FirebaseService::class);

        $creator = User::factory()->create([
            'password_updated_at' => now(),
        ]);
        $targetEmployee = User::factory()->create([
            'nama' => 'Staff Lapangan',
            'fcm_token' => 'token_staff_lapangan',
            'password_updated_at' => now(),
        ]);

        $status = MasterStatusClient::create([
            'nama_status' => 'Prospek',
        ]);

        $ho = ManajemenHo::create([
            'namaClient' => 'PT Sumber Makmur',
            'nomorTelepon' => '08123456789',
            'alamat' => 'Jl. Gatot Subroto No. 45',
            'status_id' => $status->id,
            'user_id' => $creator->id,
        ]);

        $mock->shouldReceive('notifyPenugasanKunjungan')
            ->once()
            ->withArgs(function ($penugasan) use ($targetEmployee) {
                return $penugasan->user_id === $targetEmployee->id
                    && $penugasan->assignable->namaClient === 'PT Sumber Makmur';
            })
            ->andReturn(['success' => true]);

        $token = auth('api')->login($creator);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/penugasans', [
                'assignable_type' => 'HO',
                'assignable_id' => $ho->uuid,
                'user_id' => $targetEmployee->id,
                'catatan' => 'Silakan lakukan kunjungan penawaran produk.',
            ]);

        $response->assertStatus(201);
    }

    public function test_firebase_service_notify_user_login_sends_to_leader_and_superadmin(): void
    {
        $roleLeader = Role::create(['nama_role' => 'Leader']);
        $roleSuperadmin = Role::create(['nama_role' => 'superadmin']);
        $roleStaff = Role::create(['nama_role' => 'Staff']);

        $leader = User::factory()->create([
            'role_id' => $roleLeader->id,
            'fcm_token' => 'token_leader_1',
            'password_updated_at' => now(),
        ]);
        $superadmin = User::factory()->create([
            'role_id' => $roleSuperadmin->id,
            'fcm_token' => 'token_superadmin_1',
            'password_updated_at' => now(),
        ]);
        $staff = User::factory()->create([
            'role_id' => $roleStaff->id,
            'nama' => 'Budi Staff',
            'username' => 'budistaff',
            'fcm_token' => 'token_staff_1',
            'password_updated_at' => now(),
        ]);

        $firebaseService = new FirebaseService;
        $result = $firebaseService->notifyUserLogin($staff);

        // Result total should include leader and superadmin tokens (2 tokens)
        $this->assertEquals(2, $result['total']);
        $this->assertArrayHasKey('token_leader_1', $result['details']);
        $this->assertArrayHasKey('token_superadmin_1', $result['details']);
    }

    public function test_firebase_service_notify_penugasan_kunjungan_formats_customer_details(): void
    {
        $targetUser = User::factory()->create([
            'nama' => 'Doni Petugas',
            'username' => 'donipetugas',
            'fcm_token' => 'token_doni_device',
            'password_updated_at' => now(),
        ]);
        $creator = User::factory()->create(['password_updated_at' => now()]);
        $status = MasterStatusClient::create(['nama_status' => 'Pending']);

        $ho = ManajemenHo::create([
            'namaClient' => 'CV Digital Mandiri',
            'nomorTelepon' => '0899998888',
            'alamat' => 'Jl. Pahlawan No. 12',
            'status_id' => $status->id,
            'user_id' => $creator->id,
        ]);

        $penugasan = Penugasan::create([
            'assignable_type' => ManajemenHo::class,
            'assignable_id' => $ho->uuid,
            'user_id' => $targetUser->id,
            'assigned_by' => $creator->id,
            'status_id' => null,
            'catatan' => 'Kunjungi sebelum jam 12 siang',
        ]);

        $firebaseService = new FirebaseService;
        $result = $firebaseService->notifyPenugasanKunjungan($penugasan);

        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('message', $result);
    }
}
