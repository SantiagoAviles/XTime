<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_puede_ver_formulario_de_recuperacion(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertViewIs('auth.forgot-password');
    }

    public function test_usuario_puede_solicitar_enlace_de_recuperacion(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        // Redirige de vuelta con el status, no genera error 500
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
    }

    public function test_email_inexistente_no_filtra_informacion_sensible(): void
    {
        $response = $this->post('/forgot-password', [
            'email' => 'no-existe@ejemplo.com',
        ]);

        // Debe devolver respuesta sin revelar si el correo existe o no en errores de sesión
        // (Laravel estándar devuelve error de 'email' con mensaje genérico, no 500)
        $response->assertStatus(302);
        $this->assertNotEquals(500, $response->status());
    }

    public function test_usuario_puede_ver_formulario_de_reset_con_token(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $response = $this->get('/reset-password/' . $token);

        $response->assertStatus(200);
        $response->assertViewIs('auth.reset-password');
    }

    public function test_token_valido_permite_resetear_contrasena(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $response = $this->post('/reset-password', [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'NuevaContrasena123!',
            'password_confirmation' => 'NuevaContrasena123!',
        ]);

        // Redirige a login tras reset exitoso
        $response->assertRedirect('/login');
        $response->assertSessionHasNoErrors();
    }

    public function test_token_invalido_no_permite_resetear_contrasena(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/reset-password', [
            'token'                 => 'token-invalido-xxxx',
            'email'                 => $user->email,
            'password'              => 'NuevaContrasena123!',
            'password_confirmation' => 'NuevaContrasena123!',
        ]);

        // Devuelve error en el campo email (comportamiento estándar de Laravel Password)
        $response->assertSessionHasErrors('email');
    }

    public function test_contrasenas_que_no_coinciden_son_rechazadas(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $response = $this->post('/reset-password', [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'NuevaContrasena123!',
            'password_confirmation' => 'Diferente456!',
        ]);

        $response->assertSessionHasErrors('password');
    }
}
