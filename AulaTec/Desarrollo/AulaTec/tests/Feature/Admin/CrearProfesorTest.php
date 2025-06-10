<?php

namespace Tests\Feature;
// php artisan test tests/Feature/Admin/CrearProfesorTest.php
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;

class CrearProfesorTest extends TestCase
{
    use RefreshDatabase;

    protected $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear un usuario profesor
        $this->teacher = User::create([
            'nombre' => 'Profesor',
            'apellido' => 'Test',
            'email' => 'profesor@test.com',
            'password' => bcrypt('password'),
            'rol' => 'profesor',
            'numero_matricula' => 123456,
        ]);
    }

    #[Test]
    public function profesor_puede_acceder_a_crear_profesor()
    {
        Auth::login($this->teacher);

        $response = $this->get(route('admin.crear-profesor.create'));

        $response->assertStatus(200);
        $response->assertViewIs('modules.admin.crear_profesor.create');
        $response->assertViewHas('titulo', 'Crear Nuevo Profesor');
    }

    #[Test]
    public function usuario_no_autenticado_es_redirigido_en_create()
    {
        $response = $this->get(route('admin.crear-profesor.create'));

        $response->assertStatus(302);
    }

    #[Test]
    public function profesor_puede_crear_nuevo_profesor()
    {
        Auth::login($this->teacher);

        $profesorData = [
            'nombre' => 'Nuevo',
            'apellido' => 'Profesor',
            'email' => 'nuevo.profesor@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->withoutMiddleware()->post(route('admin.crear-profesor.store'), $profesorData);

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.crear-profesor.create'));
        $response->assertSessionHas('success', '¡Profesor registrado exitosamente!');

        // Verificar que el profesor fue creado en la base de datos
        $this->assertDatabaseHas('users', [
            'nombre' => 'Nuevo',
            'apellido' => 'Profesor',
            'email' => 'nuevo.profesor@test.com',
            'rol' => 'profesor',
        ]);
    }

    #[Test]
    public function store_requiere_autenticacion()
    {
        $profesorData = [
            'nombre' => 'Nuevo',
            'apellido' => 'Profesor',
            'email' => 'nuevo.profesor@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->withoutMiddleware()->post(route('admin.crear-profesor.store'), $profesorData);

        // Usuario no autenticado debería recibir 302, 403 o 401
        $this->assertContains($response->getStatusCode(), [302, 403, 401]);

        // Verificar que NO se creó el profesor
        $this->assertDatabaseMissing('users', [
            'email' => 'nuevo.profesor@test.com',
        ]);
    }

    #[Test]
    public function store_valida_campos_requeridos()
    {
        Auth::login($this->teacher);

        $response = $this->withoutMiddleware()->post(route('admin.crear-profesor.store'), []);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['nombre', 'apellido', 'email', 'password']);
    }

    #[Test]
    public function store_valida_email_unico()
    {
        Auth::login($this->teacher);

        // Crear otro profesor con email específico
        User::create([
            'nombre' => 'Existente',
            'apellido' => 'Profesor',
            'email' => 'existente@test.com',
            'password' => bcrypt('password'),
            'rol' => 'profesor',
            'numero_matricula' => 999999,
        ]);

        $profesorData = [
            'nombre' => 'Nuevo',
            'apellido' => 'Profesor',
            'email' => 'existente@test.com', // Email duplicado
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->withoutMiddleware()->post(route('admin.crear-profesor.store'), $profesorData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email']);
    }

    #[Test]
    public function store_valida_confirmacion_password()
    {
        Auth::login($this->teacher);

        $profesorData = [
            'nombre' => 'Nuevo',
            'apellido' => 'Profesor',
            'email' => 'nuevo.profesor@test.com',
            'password' => 'password123',
            'password_confirmation' => 'diferente123',
        ];

        $response = $this->withoutMiddleware()->post(route('admin.crear-profesor.store'), $profesorData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['password']);
    }

    #[Test]
    public function store_valida_longitud_minima_password()
    {
        Auth::login($this->teacher);

        $profesorData = [
            'nombre' => 'Nuevo',
            'apellido' => 'Profesor',
            'email' => 'nuevo.profesor@test.com',
            'password' => '123', // Muy corto
            'password_confirmation' => '123',
        ];

        $response = $this->withoutMiddleware()->post(route('admin.crear-profesor.store'), $profesorData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['password']);
    }

    #[Test]
    public function password_es_encriptado_correctamente()
    {
        Auth::login($this->teacher);

        $profesorData = [
            'nombre' => 'Nuevo',
            'apellido' => 'Profesor',
            'email' => 'nuevo.profesor@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->withoutMiddleware()->post(route('admin.crear-profesor.store'), $profesorData);

        $user = User::where('email', 'nuevo.profesor@test.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertNotEquals('password123', $user->password); // No debe estar en texto plano
    }

    #[Test]
    public function profesor_puede_acceder_a_editar_perfil()
    {
        Auth::login($this->teacher);

        $response = $this->get(route('admin.crear-profesor.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('modules.admin.crear_profesor.edit');
        $response->assertViewHas('titulo', 'Editar Profesor');
        $response->assertViewHas('usuario');
    }

    #[Test]
    public function edit_contiene_datos_del_profesor_autenticado()
    {
        Auth::login($this->teacher);

        $response = $this->get(route('admin.crear-profesor.edit'));

        $response->assertStatus(200);

        $response->assertViewHas('usuario', function ($usuario) {
            return $usuario->id === $this->teacher->id
                && $usuario->nombre === 'Profesor'
                && $usuario->apellido === 'Test'
                && $usuario->email === 'profesor@test.com';
        });
    }

    #[Test]
    public function usuario_no_autenticado_es_redirigido_en_edit()
    {
        $response = $this->get(route('admin.crear-profesor.edit'));

        $response->assertStatus(302);
    }

    #[Test]
    public function store_maneja_errores_de_base_de_datos()
    {
        Auth::login($this->teacher);

        // Simular un escenario que cause error (email muy largo)
        $profesorData = [
            'nombre' => 'Nuevo',
            'apellido' => 'Profesor',
            'email' => str_repeat('a', 300) . '@test.com', // Email muy largo
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->withoutMiddleware()->post(route('admin.crear-profesor.store'), $profesorData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email']);
    }

    #[Test]
    public function rol_se_asigna_automaticamente_como_profesor()
    {
        Auth::login($this->teacher);

        $profesorData = [
            'nombre' => 'Nuevo',
            'apellido' => 'Profesor',
            'email' => 'nuevo.profesor@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->withoutMiddleware()->post(route('admin.crear-profesor.store'), $profesorData);

        $this->assertDatabaseHas('users', [
            'email' => 'nuevo.profesor@test.com',
            'rol' => 'profesor',
        ]);
    }

    #[Test]
    public function campos_nombre_y_apellido_son_guardados_correctamente()
    {
        Auth::login($this->teacher);

        $profesorData = [
            'nombre' => 'María José',
            'apellido' => 'García López',
            'email' => 'maria.garcia@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->withoutMiddleware()->post(route('admin.crear-profesor.store'), $profesorData);

        $this->assertDatabaseHas('users', [
            'nombre' => 'María José',
            'apellido' => 'García López',
            'email' => 'maria.garcia@test.com',
        ]);
    }

    #[Test]
    public function multiples_profesores_pueden_crear_otros_profesores()
    {
        // Crear otro profesor
        $otherTeacher = User::create([
            'nombre' => 'Otro',
            'apellido' => 'Profesor',
            'email' => 'otro.profesor@test.com',
            'password' => bcrypt('password'),
            'rol' => 'profesor',
            'numero_matricula' => 555555,
        ]);

        // Test con primer profesor
        Auth::login($this->teacher);
        $response = $this->get(route('admin.crear-profesor.create'));
        $response->assertStatus(200);

        // Test con segundo profesor
        Auth::logout();
        Auth::login($otherTeacher);
        $response = $this->get(route('admin.crear-profesor.create'));
        $response->assertStatus(200);
    }

    #[Test]
    public function formulario_create_tiene_titulo_correcto()
    {
        Auth::login($this->teacher);

        $response = $this->get(route('admin.crear-profesor.create'));

        $response->assertStatus(200);
        $response->assertViewHas('titulo', 'Crear Nuevo Profesor');
        $response->assertSee('Crear Nuevo Profesor');
    }

    #[Test]
    public function formulario_edit_tiene_titulo_correcto()
    {
        Auth::login($this->teacher);

        $response = $this->get(route('admin.crear-profesor.edit'));

        $response->assertStatus(200);
        $response->assertViewHas('titulo', 'Editar Profesor');
        $response->assertSee('Editar Profesor');
    }

    #[Test]
    public function test_flujo_completo_crear_profesor()
    {
        // Test del flujo completo: crear y verificar
        Auth::login($this->teacher);

        // 1. Acceder al formulario
        $response = $this->get(route('admin.crear-profesor.create'));
        $response->assertStatus(200);

        // 2. Enviar datos válidos
        $profesorData = [
            'nombre' => 'Flujo',
            'apellido' => 'Completo',
            'email' => 'flujo.completo@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->withoutMiddleware()->post(route('admin.crear-profesor.store'), $profesorData);

        // 3. Verificar redirección exitosa
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.crear-profesor.create'));
        $response->assertSessionHas('success');

        // 4. Verificar que se creó en la base de datos
        $newUser = User::where('email', 'flujo.completo@test.com')->first();
        $this->assertNotNull($newUser);
        $this->assertEquals('Flujo', $newUser->nombre);
        $this->assertEquals('Completo', $newUser->apellido);
        $this->assertEquals('profesor', $newUser->rol);
        $this->assertTrue(Hash::check('password123', $newUser->password));
    }
}
