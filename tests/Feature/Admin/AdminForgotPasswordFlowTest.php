<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AdminForgotPasswordFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_request_password_reset_link_for_admin_login(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create([
            'email' => 'admin-reset@example.com',
        ]);

        $response = $this->post(route('auth.password.email'), [
            'email' => $admin->email,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('status');

        Notification::assertSentTo($admin, ResetPassword::class);
    }

    public function test_guest_can_reset_password_using_valid_token(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin-token@example.com',
        ]);

        $token = Password::broker()->createToken($admin);

        $response = $this->post(route('auth.password.update'), [
            'token' => $token,
            'email' => $admin->email,
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ]);

        $response->assertRedirect(route('auth.login'));
        $this->assertTrue(Hash::check('new-secure-password', $admin->fresh()->password));
    }
}
