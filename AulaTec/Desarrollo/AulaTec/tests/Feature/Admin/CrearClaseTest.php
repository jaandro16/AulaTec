<?php
// php artisan test tests/Feature/Admin/CrearClaseTest.php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

// Test para el controlador de creación de clases
// Verifica la gestión administrativa de clases

class CrearClaseTest extends TestCase
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

    /** @test */
    public function profesor_puede_acceder_a_crear_clase()
    {
        // Autenticar como profesor
        Auth::login($this->teacher);

        $response = $this->get(route('admin.crear-clase.create'));

        $response->assertStatus(200);
        $response->assertViewIs('modules.admin.crear_clase.create');
        $response->assertViewHas('titulo', 'Crear Nueva Clase');
        $response->assertViewHas('profesor');
    }

    /** @test */
    public function usuario_no_autenticado_es_redirigido()
    {
        // No autenticar usuario
        $response = $this->get(route('admin.crear-clase.create'));

        // La aplicación redirige (302) usuarios no autenticados
        $response->assertStatus(302);
    }

    /** @test */
    public function vista_contiene_datos_del_profesor_autenticado()
    {
        Auth::login($this->teacher);

        $response = $this->get(route('admin.crear-clase.create'));

        $response->assertStatus(200);

        // Verificar que la vista tiene los datos del profesor
        $response->assertViewHas('profesor', function ($profesor) {
            return $profesor->id === $this->teacher->id
                && $profesor->nombre === 'Profesor'
                && $profesor->apellido === 'Test'
                && $profesor->email === 'profesor@test.com';
        });
    }

    /** @test */
    public function vista_tiene_titulo_correcto()
    {
        Auth::login($this->teacher);

        $response = $this->get(route('admin.crear-clase.create'));

        $response->assertStatus(200);
        $response->assertViewHas('titulo', 'Crear Nueva Clase');

        // Verificar que el título aparece en la vista
        $response->assertSee('Crear Nueva Clase');
    }

    /** @test */
    public function ruta_requiere_autenticacion_de_profesor()
    {
        // Test con diferentes escenarios de autenticación

        // 1. Sin autenticación - debe redirigir
        $response = $this->get(route('admin.crear-clase.create'));
        $this->assertEquals(302, $response->getStatusCode());

        // 2. Con profesor autenticado - debe funcionar
        Auth::login($this->teacher);
        $response = $this->get(route('admin.crear-clase.create'));
        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function multiples_profesores_pueden_acceder()
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
        $response = $this->get(route('admin.crear-clase.create'));
        $response->assertStatus(200);
        $response->assertViewHas('profesor', $this->teacher);

        // Logout y test con segundo profesor
        Auth::logout();
        Auth::login($otherTeacher);
        $response = $this->get(route('admin.crear-clase.create'));
        $response->assertStatus(200);
        $response->assertViewHas('profesor', $otherTeacher);
    }

    /** @test */
    public function response_contiene_headers_correctos()
    {
        Auth::login($this->teacher);

        $response = $this->get(route('admin.crear-clase.create'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/html; charset=UTF-8');
    }

    /** @test */
    public function acceso_sin_session_es_redirigido()
    {
        // Asegurar que no hay sesión activa
        Auth::logout();
        $this->assertFalse(Auth::check());

        $response = $this->get(route('admin.crear-clase.create'));

        // La aplicación redirige usuarios no autenticados
        $response->assertStatus(302);
    }

    /** @test */
    public function datos_del_profesor_son_correctos_en_vista()
    {
        Auth::login($this->teacher);

        $response = $this->get(route('admin.crear-clase.create'));

        $response->assertStatus(200);

        // Verificar que todos los datos del profesor están presentes
        $viewData = $response->viewData('profesor');

        $this->assertEquals($this->teacher->id, $viewData->id);
        $this->assertEquals($this->teacher->nombre, $viewData->nombre);
        $this->assertEquals($this->teacher->apellido, $viewData->apellido);
        $this->assertEquals($this->teacher->email, $viewData->email);
        $this->assertEquals($this->teacher->rol, $viewData->rol);
        $this->assertEquals($this->teacher->numero_matricula, $viewData->numero_matricula);
    }

    /** @test */
    public function test_con_datos_minimos_funciona()
    {
        // Test simple para verificar funcionalidad básica
        Auth::login($this->teacher);

        $response = $this->get(route('admin.crear-clase.create'));

        $response->assertSuccessful();
        $response->assertViewIs('modules.admin.crear_clase.create');
        $this->assertNotNull($response->viewData('titulo'));
        $this->assertNotNull($response->viewData('profesor'));
    }

    /** @test */
    public function profesor_autenticado_ve_vista_correcta()
    {
        Auth::login($this->teacher);

        $response = $this->get(route('admin.crear-clase.create'));

        $response->assertSuccessful();
        $response->assertViewIs('modules.admin.crear_clase.create');

        // Verificar contenido específico
        $response->assertSee('Crear Nueva Clase');
        $response->assertViewHas('titulo', 'Crear Nueva Clase');
        $response->assertViewHas('profesor');

        // Verificar que el profesor en la vista es el correcto
        $profesor = $response->viewData('profesor');
        $this->assertEquals($this->teacher->id, $profesor->id);
        $this->assertEquals('profesor', $profesor->rol);
    }

    /** @test */
    public function controlador_maneja_autenticacion_correctamente()
    {
        // Test del flujo completo de autenticación

        // 1. Usuario no autenticado - debe ser redirigido
        $this->assertFalse(Auth::check());
        $response = $this->get(route('admin.crear-clase.create'));
        $this->assertTrue($response->isRedirection());

        // 2. Usuario autenticado como profesor - debe acceder
        Auth::login($this->teacher);
        $this->assertTrue(Auth::check());
        $this->assertEquals('profesor', Auth::user()->rol);

        $response = $this->get(route('admin.crear-clase.create'));
        $this->assertTrue($response->isSuccessful());

        // Verificar la vista correcta (sin usar 'view' que no existe)
        $response->assertViewIs('modules.admin.crear_clase.create');
        $response->assertViewHas('titulo', 'Crear Nueva Clase');
        $response->assertViewHas('profesor');
    }
    

    /** @test */
    public function metodo_checkTeacherRole_funciona_correctamente()
    {
        // Test indirecto del método privado checkTeacherRole()

        // Sin autenticación - debe redirigir (por middleware de Laravel)
        $response = $this->get(route('admin.crear-clase.create'));
        $this->assertEquals(302, $response->getStatusCode());

        // Con profesor autenticado - método debe permitir acceso
        Auth::login($this->teacher);
        $response = $this->get(route('admin.crear-clase.create'));
        $this->assertEquals(200, $response->getStatusCode());

        // Verificar que el método create() se ejecutó correctamente
        $this->assertEquals('Crear Nueva Clase', $response->viewData('titulo'));
        $this->assertEquals($this->teacher->id, $response->viewData('profesor')->id);
    }
}
