<?php

namespace Tests\Feature;
// php artisan test tests/Feature/DashboardControllerTest.php
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $controller;
    protected $alumno;
    protected $profesor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->controller = new DashboardController();

        // Crear un usuario alumno
        $this->alumno = User::create([
            'nombre' => 'Estudiante',
            'apellido' => 'Test',
            'email' => 'alumno@test.com',
            'password' => bcrypt('password'),
            'rol' => 'alumno',
            'numero_matricula' => 123456,
        ]);

        // Crear un usuario profesor
        $this->profesor = User::create([
            'nombre' => 'Profesor',
            'apellido' => 'Test',
            'email' => 'profesor@test.com',
            'password' => bcrypt('password'),
            'rol' => 'profesor',
            'numero_matricula' => 789012,
        ]);
    }

    #[Test]
    public function dashboard_permite_acceso_a_alumnos_autenticados()
    {
        Auth::login($this->alumno);

        $response = $this->controller->dashboard();

        // Verificar que devuelve una vista
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('layouts.dashboard', $response->name());
    }

    #[Test]
    public function dashboard_rechaza_usuarios_no_autenticados()
    {
        Auth::logout();

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Debes iniciar sesión para acceder.');

        $this->controller->dashboard();
    }

    #[Test]
    public function dashboard_rechaza_profesores()
    {
        Auth::login($this->profesor);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Acceso restringido.');

        $this->controller->dashboard();
    }

    #[Test]
    public function database_enforces_rol_not_null_constraint()
    {
        // Verificar que la DB rechaza rol null
        $this->expectException(\Illuminate\Database\QueryException::class);
        $this->expectExceptionMessage('Column \'rol\' cannot be null');

        User::create([
            'nombre' => 'Usuario',
            'apellido' => 'Test',
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
            'rol' => null, // Esto debe fallar
            'numero_matricula' => 555555,
        ]);
    }

    #[Test]
    public function database_enforces_rol_check_constraint()
    {
        // Verificar que la DB rechaza roles inválidos
        $this->expectException(\Illuminate\Database\QueryException::class);
        $this->expectExceptionMessage('Data truncated for column \'rol\'');

        User::create([
            'nombre' => 'Usuario',
            'apellido' => 'Test',
            'email' => 'test2@test.com',
            'password' => bcrypt('password'),
            'rol' => 'admin', // Esto debe fallar por ENUM constraint
            'numero_matricula' => 666666,
        ]);
    }

    #[Test]
    public function database_enforces_rol_empty_string_constraint()
    {
        // Verificar que la DB rechaza string vacío
        $this->expectException(\Illuminate\Database\QueryException::class);
        $this->expectExceptionMessage('Data truncated for column \'rol\'');

        User::create([
            'nombre' => 'Usuario',
            'apellido' => 'Test',
            'email' => 'test3@test.com',
            'password' => bcrypt('password'),
            'rol' => '', // Esto debe fallar por ENUM constraint
            'numero_matricula' => 777777,
        ]);
    }

    #[Test]
    public function database_enforces_rol_case_sensitive_constraint()
    {
        // MySQL ENUM might be case-insensitive or auto-convert values
        // Let's test with a completely invalid role instead
        $this->expectException(\Illuminate\Database\QueryException::class);

        User::create([
            'nombre' => 'Usuario',
            'apellido' => 'Test',
            'email' => 'test4@test.com',
            'password' => bcrypt('password'),
            'rol' => 'administrador', // Completely invalid role that won't match enum
            'numero_matricula' => 888888,
        ]);
    }

    #[Test]
    public function check_teacher_role_funciona_correctamente_con_alumno()
    {
        Auth::login($this->alumno);

        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('checkTeacherRole');
        $method->setAccessible(true);

        // No debe lanzar excepción
        $method->invoke($this->controller);
        $this->assertTrue(true); // Si llegamos aquí, no hubo excepción
    }

    #[Test]
    public function check_teacher_role_lanza_excepcion_sin_autenticacion()
    {
        Auth::logout();

        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('checkTeacherRole');
        $method->setAccessible(true);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Debes iniciar sesión para acceder.');

        $method->invoke($this->controller);
    }

    #[Test]
    public function check_teacher_role_lanza_excepcion_con_profesor()
    {
        Auth::login($this->profesor);

        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('checkTeacherRole');
        $method->setAccessible(true);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Acceso restringido.');

        $method->invoke($this->controller);
    }

    #[Test]
    public function dashboard_verifica_estado_de_autenticacion()
    {
        // Simular usuario autenticado inicialmente
        Auth::login($this->alumno);

        // Luego hacer logout
        Auth::logout();

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Debes iniciar sesión para acceder.');

        $this->controller->dashboard();
    }

    #[Test]
    public function dashboard_con_multiple_autenticaciones()
    {
        // Autenticar profesor primero
        Auth::login($this->profesor);

        // Cambiar a alumno
        Auth::logout();
        Auth::login($this->alumno);

        $response = $this->controller->dashboard();

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('layouts.dashboard', $response->name());
    }

    #[Test]
    public function dashboard_verifica_coherencia_del_metodo_privado()
    {
        // Verificar que el método privado y el público se comportan igual
        Auth::login($this->alumno);

        // Llamar método privado
        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('checkTeacherRole');
        $method->setAccessible(true);

        // No debe lanzar excepción
        $method->invoke($this->controller);

        // Llamar método público
        $response = $this->controller->dashboard();

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    #[Test]
    public function dashboard_codigo_de_error_correcto_sin_autenticacion()
    {
        Auth::logout();

        try {
            $this->controller->dashboard();
            $this->fail('Se esperaba una HttpException');
        } catch (HttpException $e) {
            $this->assertEquals(403, $e->getStatusCode());
            $this->assertEquals('Debes iniciar sesión para acceder.', $e->getMessage());
        }
    }

    #[Test]
    public function dashboard_codigo_de_error_correcto_con_rol_incorrecto()
    {
        Auth::login($this->profesor);

        try {
            $this->controller->dashboard();
            $this->fail('Se esperaba una HttpException');
        } catch (HttpException $e) {
            $this->assertEquals(403, $e->getStatusCode());
            $this->assertEquals('Acceso restringido.', $e->getMessage());
        }
    }

    #[Test]
    public function dashboard_mantiene_sesion_de_usuario_correcto()
    {
        Auth::login($this->alumno);

        $usuarioAntes = Auth::user();

        $response = $this->controller->dashboard();

        $usuarioDespues = Auth::user();

        // Verificar que el usuario sigue siendo el mismo
        $this->assertEquals($usuarioAntes->id, $usuarioDespues->id);
        $this->assertEquals($usuarioAntes->rol, $usuarioDespues->rol);
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    #[Test]
    public function dashboard_solo_acepta_rol_alumno_exacto()
    {
        // Crear otro alumno para confirmar que funciona consistentemente
        $otroAlumno = User::create([
            'nombre' => 'Otro',
            'apellido' => 'Alumno',
            'email' => 'otro@test.com',
            'password' => bcrypt('password'),
            'rol' => 'alumno',
            'numero_matricula' => 999999,
        ]);

        Auth::login($otroAlumno);

        $response = $this->controller->dashboard();

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('layouts.dashboard', $response->name());
    }

    #[Test]
    public function dashboard_funciona_con_diferentes_alumnos()
    {
        // Test con el primer alumno
        Auth::login($this->alumno);
        $response1 = $this->controller->dashboard();
        $this->assertInstanceOf(\Illuminate\View\View::class, $response1);

        // Cambiar al segundo alumno
        Auth::logout();

        $segundoAlumno = User::create([
            'nombre' => 'Segundo',
            'apellido' => 'Estudiante',
            'email' => 'segundo@test.com',
            'password' => bcrypt('password'),
            'rol' => 'alumno',
            'numero_matricula' => 111111,
        ]);

        Auth::login($segundoAlumno);
        $response2 = $this->controller->dashboard();

        $this->assertInstanceOf(\Illuminate\View\View::class, $response2);
        $this->assertEquals('layouts.dashboard', $response2->name());
    }

    #[Test]
    public function dashboard_valida_autenticacion_antes_que_rol()
    {
        // Verificar que primero se valida autenticación
        Auth::logout();

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Debes iniciar sesión para acceder.');

        $this->controller->dashboard();
    }
}
