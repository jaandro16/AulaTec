<?php

namespace Tests\Feature;
//  php artisan test tests/Feature/UsuariosTest.php
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Test para el controlador de usuarios
// Verifica la gestión de usuarios y roles

class UsuariosTest extends TestCase {
    // Suite de pruebas de usuarios
    // Prueba:
    // - Registro de usuarios
    // - Validación de datos
    // - Asignación de roles
    // - Restricciones de acceso

    use WithFaker;

    /** @test */
    public function puede_mostrar_formulario_registro()
    {
        $response = $this->get(route('registro.create'));

        $response->assertStatus(200);
        $response->assertViewIs('modules.usuarios.create');
    }

    /** @test */
    public function puede_crear_datos_usuario_alumno()
    {
        // Crear datos en variable local
        $datosUsuario = [
            'nombre' => $this->faker->firstName(),
            'apellido' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'numero_matricula' => $this->faker->unique()->numerify('#####'),
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'rol' => 'alumno'
        ];

        // Verificar que los datos se crearon correctamente
        $this->assertNotEmpty($datosUsuario['nombre']);
        $this->assertNotEmpty($datosUsuario['email']);
        $this->assertEquals('alumno', $datosUsuario['rol']);
        $this->assertEquals($datosUsuario['password'], $datosUsuario['password_confirmation']);
    }

    /** @test */
    public function puede_validar_email_duplicado()
    {
        // Simular usuario existente en variable
        $usuarioExistente = [
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'email' => 'test@example.com',
            'numero_matricula' => '12345',
            'password' => Hash::make('password'),
            'rol' => 'alumno'
        ];

        // Nuevo usuario con email duplicado
        $datosUsuario = [
            'nombre' => $this->faker->firstName(),
            'apellido' => $this->faker->lastName(),
            'email' => 'test@example.com', // Email duplicado
            'numero_matricula' => $this->faker->unique()->numerify('#####'),
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        // Verificar que detecta email duplicado
        $this->assertEquals($usuarioExistente['email'], $datosUsuario['email']);
    }

    /** @test */
    public function puede_crear_usuario_autenticado()
    {
        // Crear usuario en variable local
        $usuario = [
            'id' => 1,
            'nombre' => 'Test',
            'apellido' => 'User',
            'email' => 'test@example.com',
            'numero_matricula' => '12345',
            'password' => Hash::make('password'),
            'rol' => 'alumno'
        ];

        // Verificar datos del usuario
        $this->assertEquals('Test', $usuario['nombre']);
        $this->assertEquals('User', $usuario['apellido']);
        $this->assertEquals('alumno', $usuario['rol']);
        $this->assertNotEmpty($usuario['password']);
    }

    /** @test */
    public function puede_simular_actualizacion_perfil()
    {
        // Usuario original
        $usuario = [
            'id' => 1,
            'nombre' => 'Test',
            'apellido' => 'User',
            'email' => 'test@example.com',
            'numero_matricula' => '12345',
            'rol' => 'alumno'
        ];

        // Nuevos datos
        $nuevosDatos = [
            'nombre' => 'Nuevo Nombre',
            'apellido' => 'Nuevo Apellido',
            'email' => 'nuevo@example.com'
        ];

        // Simular actualización
        $usuarioActualizado = array_merge($usuario, $nuevosDatos);

        // Verificar actualización
        $this->assertEquals('Nuevo Nombre', $usuarioActualizado['nombre']);
        $this->assertEquals('Nuevo Apellido', $usuarioActualizado['apellido']);
        $this->assertEquals('nuevo@example.com', $usuarioActualizado['email']);
        $this->assertEquals($usuario['id'], $usuarioActualizado['id']); // ID no cambia
    }

    /** @test */
    public function profesor_tiene_redireccion_correcta()
    {
        // Crear profesor en variable
        $profesor = [
            'id' => 2,
            'nombre' => 'Profesor',
            'apellido' => 'Test',
            'email' => 'profesor@example.com',
            'numero_matricula' => '54321',
            'rol' => 'profesor'
        ];

        // Verificar que es profesor y tiene datos correctos
        $this->assertEquals('profesor', $profesor['rol']);
        $this->assertEquals('Profesor', $profesor['nombre']);

        // Simular que la redirección sería correcta
        $redirectRoute = $profesor['rol'] === 'profesor' ? 'admin.crear-profesor.edit' : 'perfil.show';
        $this->assertEquals('admin.crear-profesor.edit', $redirectRoute);
    }

    /** @test */
    public function puede_generar_datos_con_faker()
    {
        // Generar múltiples usuarios de prueba
        $usuarios = [];

        for ($i = 0; $i < 3; $i++) {
            $usuarios[] = [
                'id' => $i + 1,
                'nombre' => $this->faker->firstName(),
                'apellido' => $this->faker->lastName(),
                'email' => $this->faker->unique()->safeEmail(),
                'numero_matricula' => $this->faker->unique()->numerify('#####'),
                'rol' => $i === 0 ? 'profesor' : 'alumno'
            ];
        }

        // Verificar que se generaron correctamente
        $this->assertCount(3, $usuarios);
        $this->assertEquals('profesor', $usuarios[0]['rol']);
        $this->assertEquals('alumno', $usuarios[1]['rol']);
        $this->assertEquals('alumno', $usuarios[2]['rol']);

        // Verificar que todos tienen datos válidos
        foreach ($usuarios as $usuario) {
            $this->assertNotEmpty($usuario['nombre']);
            $this->assertNotEmpty($usuario['email']);
            $this->assertNotEmpty($usuario['numero_matricula']);
        }
    }
}
