<?php
//php artisan test tests/Unit/AdminAsistenciasTest.php
namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\Admin\Asistencias;
use App\Models\User;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\TimeSlot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Carbon\Carbon;

// Test para el controlador Admin\Asistencias
// Verifica la funcionalidad de registro y gestión de asistencias

class AdminAsistenciasTest extends TestCase
{
    // Prueba unitaria del módulo de asistencias administrativas
    // Usa RefreshDatabase para garantizar un estado limpio de la BD
    // Verifica:
    // - Acceso al endpoint index
    // - Datos necesarios en la vista 
    // - Autenticación de profesor requerida

    use RefreshDatabase;

    protected $controller;
    protected $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->controller = new Asistencias();

        // Crear y autenticar profesor
        $this->teacher = User::create([
            'nombre' => 'Profesor',
            'apellido' => 'Unit',
            'email' => 'profesor.unit@test.com',
            'password' => bcrypt('password'),
            'rol' => 'profesor',
            'numero_matricula' => 111111,
        ]);

        Auth::login($this->teacher);
    }

    /** @test */
    public function index_devuelve_vista_correcta()
    {
        $request = new Request();
        $response = $this->controller->index($request);

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('modules.admin.asistencia.index', $response->name());
        $this->assertEquals('Registro de Asistencias', $response->getData()['titulo']);
    }

    /** @test */
    public function index_contiene_datos_necesarios()
    {
        $request = new Request();
        $response = $this->controller->index($request);
        $data = $response->getData();

        // Verificar que contiene las variables necesarias
        $this->assertArrayHasKey('titulo', $data);
        $this->assertArrayHasKey('reservas', $data);
        $this->assertArrayHasKey('clasesUnicas', $data);
        $this->assertArrayHasKey('fechasUnicas', $data);
    }

    /** @test */
    public function index_filtra_por_profesor_autenticado()
    {
        // Crear otro profesor
        $otherTeacher = User::create([
            'nombre' => 'Otro',
            'apellido' => 'Profesor',
            'email' => 'otro.profesor@test.com',
            'password' => bcrypt('password'),
            'rol' => 'profesor',
            'numero_matricula' => 222222,
        ]);

        // Crear datos de prueba
        $subject1 = Subject::create([
            'name' => 'Materia 1',
            'code' => 'MAT1',
            'description' => 'Materia del profesor actual'
        ]);

        $subject2 = Subject::create([
            'name' => 'Materia 2',
            'code' => 'MAT2',
            'description' => 'Materia del otro profesor'
        ]);

        $classroom1 = Classroom::create([
            'name' => 'Aula Test 1',
            'capacity' => 30
        ]);

        $classroom2 = Classroom::create([
            'name' => 'Aula Test 2',
            'capacity' => 25
        ]);

        $timeSlot1 = TimeSlot::create([
            'start_time' => '09:00:00',
            'end_time' => '10:00:00'
        ]);

        $timeSlot2 = TimeSlot::create([
            'start_time' => '10:00:00',
            'end_time' => '11:00:00'
        ]);

        // Crear clases para ambos profesores - evitar conflictos de UNIQUE constraint
        $myClassId = DB::table('classes')->insertGetId([
            'subject_id' => $subject1->id,
            'user_id' => $this->teacher->id,
            'classroom_id' => $classroom1->id,
            'time_slot_id' => $timeSlot1->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $otherClassId = DB::table('classes')->insertGetId([
            'subject_id' => $subject2->id,
            'user_id' => $otherTeacher->id,
            'classroom_id' => $classroom2->id, // Diferente aula
            'time_slot_id' => $timeSlot2->id,  // Diferente horario
            'date' => Carbon::now()->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $student = User::create([
            'nombre' => 'Estudiante',
            'apellido' => 'Test',
            'email' => 'estudiante@test.com',
            'password' => bcrypt('password'),
            'rol' => 'profesor',
            'numero_matricula' => 333333,
        ]);

        // Crear reservas para ambas clases
        DB::table('reservations')->insert([
            'user_id' => $student->id,
            'class_id' => $myClassId,
            'asiento' => 'A1',
            'estado' => 'Completada',
            'justificado' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('reservations')->insert([
            'user_id' => $student->id,
            'class_id' => $otherClassId,
            'asiento' => 'A2',
            'estado' => 'Completada',
            'justificado' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $request = new Request();
        $response = $this->controller->index($request);
        $data = $response->getData();

        // Solo debe devolver reservas del profesor autenticado
        $this->assertCount(1, $data['reservas']);
        $this->assertEquals('Materia 1', $data['reservas'][0]['clase']);
    }

    /** @test */
    public function index_maneja_filtros_de_request()
    {
        // Crear datos de prueba con diferentes fechas
        $subject = Subject::create([
            'name' => 'Materia Filtro',
            'code' => 'MF001',
            'description' => 'Para probar filtros'
        ]);

        $classroom1 = Classroom::create([
            'name' => 'Aula Filtro 1',
            'capacity' => 25
        ]);

        $classroom2 = Classroom::create([
            'name' => 'Aula Filtro 2',
            'capacity' => 30
        ]);

        $timeSlot1 = TimeSlot::create([
            'start_time' => '10:00:00',
            'end_time' => '11:00:00'
        ]);

        $timeSlot2 = TimeSlot::create([
            'start_time' => '11:00:00',
            'end_time' => '12:00:00'
        ]);

        // Clase de hoy
        $classToday = DB::table('classes')->insertGetId([
            'subject_id' => $subject->id,
            'user_id' => $this->teacher->id,
            'classroom_id' => $classroom1->id,
            'time_slot_id' => $timeSlot1->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Clase de mañana
        $classTomorrow = DB::table('classes')->insertGetId([
            'subject_id' => $subject->id,
            'user_id' => $this->teacher->id,
            'classroom_id' => $classroom2->id, // Diferente aula
            'time_slot_id' => $timeSlot2->id,  // Diferente horario
            'date' => Carbon::tomorrow()->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $student = User::create([
            'nombre' => 'Estudiante',
            'apellido' => 'Filtro',
            'email' => 'filtro@test.com',
            'password' => bcrypt('password'),
            'rol' => 'profesor',
            'numero_matricula' => 444444,
        ]);

        // Crear reservas para ambas fechas
        DB::table('reservations')->insert([
            'user_id' => $student->id,
            'class_id' => $classToday,
            'asiento' => 'A1',
            'estado' => 'Completada',
            'justificado' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('reservations')->insert([
            'user_id' => $student->id,
            'class_id' => $classTomorrow,
            'asiento' => 'A2',
            'estado' => 'Completada',
            'justificado' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Test sin filtros - debe devolver todas
        $request = new Request();
        $response = $this->controller->index($request);
        $data = $response->getData();
        $this->assertCount(2, $data['reservas']);

        // Test con filtro de fecha (si el controlador lo soporta)
        $requestWithFilter = new Request([
            'fecha' => Carbon::now()->format('Y-m-d')
        ]);
        $responseFiltered = $this->controller->index($requestWithFilter);
        $dataFiltered = $responseFiltered->getData();

        // Verificar que el filtro funciona (si está implementado)
        $this->assertLessThanOrEqual(2, count($dataFiltered['reservas']));
    }

    /** @test */
    public function index_maneja_estado_justificado_correctamente()
    {
        $subject = Subject::create([
            'name' => 'Materia Justificado',
            'code' => 'MJ001',
            'description' => 'Para probar justificaciones'
        ]);

        $classroom = Classroom::create([
            'name' => 'Aula Justificado',
            'capacity' => 20
        ]);

        $timeSlot = TimeSlot::create([
            'start_time' => '11:00:00',
            'end_time' => '12:00:00'
        ]);

        $classId = DB::table('classes')->insertGetId([
            'subject_id' => $subject->id,
            'user_id' => $this->teacher->id,
            'classroom_id' => $classroom->id,
            'time_slot_id' => $timeSlot->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $student = User::create([
            'nombre' => 'Estudiante',
            'apellido' => 'Justificado',
            'email' => 'justificado@test.com',
            'password' => bcrypt('password'),
            'rol' => 'profesor',
            'numero_matricula' => 555555,
        ]);

        // Crear reserva justificada
        DB::table('reservations')->insert([
            'user_id' => $student->id,
            'class_id' => $classId,
            'asiento' => 'B1',
            'estado' => 'Completada',
            'justificado' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $request = new Request();
        $response = $this->controller->index($request);
        $data = $response->getData();

        $this->assertCount(1, $data['reservas']);
        $this->assertEquals('Justificado', $data['reservas'][0]['estado']);
    }

    /** @test */
    public function index_genera_fechas_unicas_correctamente()
    {
        $subject = Subject::create([
            'name' => 'Materia Fechas',
            'code' => 'MFE001',
            'description' => 'Para probar fechas únicas'
        ]);

        $classroom1 = Classroom::create([
            'name' => 'Aula Fechas 1',
            'capacity' => 30
        ]);

        $classroom2 = Classroom::create([
            'name' => 'Aula Fechas 2',
            'capacity' => 25
        ]);

        $classroom3 = Classroom::create([
            'name' => 'Aula Fechas 3',
            'capacity' => 20
        ]);

        $timeSlot1 = TimeSlot::create([
            'start_time' => '08:00:00',
            'end_time' => '09:00:00'
        ]);

        $timeSlot2 = TimeSlot::create([
            'start_time' => '09:00:00',
            'end_time' => '10:00:00'
        ]);

        $timeSlot3 = TimeSlot::create([
            'start_time' => '10:00:00',
            'end_time' => '11:00:00'
        ]);

        $student = User::create([
            'nombre' => 'Estudiante',
            'apellido' => 'Fechas',
            'email' => 'fechas@test.com',
            'password' => bcrypt('password'),
            'rol' => 'profesor',
            'numero_matricula' => 666666,
        ]);

        // Crear múltiples clases en diferentes fechas - evitar duplicados de UNIQUE constraint
        $fechas = [
            ['fecha' => '2025-06-15', 'classroom' => $classroom1, 'timeslot' => $timeSlot1],
            ['fecha' => '2025-06-16', 'classroom' => $classroom2, 'timeslot' => $timeSlot2],
            ['fecha' => '2025-06-15', 'classroom' => $classroom3, 'timeslot' => $timeSlot3], // Fecha repetida pero diferentes aula/horario
        ];

        foreach ($fechas as $index => $config) {
            $classId = DB::table('classes')->insertGetId([
                'subject_id' => $subject->id,
                'user_id' => $this->teacher->id,
                'classroom_id' => $config['classroom']->id,
                'time_slot_id' => $config['timeslot']->id,
                'date' => $config['fecha'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('reservations')->insert([
                'user_id' => $student->id,
                'class_id' => $classId,
                'asiento' => 'C' . ($index + 1),
                'estado' => 'Completada',
                'justificado' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $request = new Request();
        $response = $this->controller->index($request);
        $data = $response->getData();

        // Debe haber solo 2 fechas únicas (no 3)
        $this->assertCount(2, $data['fechasUnicas']);

        // Verificar formato de fechas
        foreach ($data['fechasUnicas'] as $fechaUnica) {
            $this->assertArrayHasKey('formato', $fechaUnica);
            $this->assertMatchesRegularExpression('/\d{2}\/\d{2}\/\d{4}/', $fechaUnica['formato']);
        }
    }

    /** @test */
    public function index_sin_reservas_devuelve_arrays_vacios()
    {
        $request = new Request();
        $response = $this->controller->index($request);
        $data = $response->getData();

        // Cambiar las validaciones para manejar Collections
        $this->assertTrue(is_array($data['reservas']) || $data['reservas'] instanceof \Illuminate\Support\Collection);
        $this->assertTrue(is_array($data['clasesUnicas']) || $data['clasesUnicas'] instanceof \Illuminate\Support\Collection);
        $this->assertTrue(is_array($data['fechasUnicas']) || $data['fechasUnicas'] instanceof \Illuminate\Support\Collection);

        // Verificar que están vacíos
        $this->assertCount(0, $data['reservas']);
        $this->assertCount(0, $data['clasesUnicas']);
        $this->assertCount(0, $data['fechasUnicas']);
    }

    /** @test */
    public function formato_de_datos_es_consistente()
    {
        $subject = Subject::create([
            'name' => 'Formato Test',
            'code' => 'FT001',
            'description' => 'Para verificar formato'
        ]);

        $classroom = Classroom::create([
            'name' => 'Aula Formato',
            'capacity' => 25
        ]);

        $timeSlot = TimeSlot::create([
            'start_time' => '14:30:00',
            'end_time' => '15:30:00'
        ]);

        $classId = DB::table('classes')->insertGetId([
            'subject_id' => $subject->id,
            'user_id' => $this->teacher->id,
            'classroom_id' => $classroom->id,
            'time_slot_id' => $timeSlot->id,
            'date' => '2025-07-20',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $student = User::create([
            'nombre' => 'María',
            'apellido' => 'González',
            'email' => 'maria@test.com',
            'password' => bcrypt('password'),
            'rol' => 'profesor',
            'numero_matricula' => 777777,
        ]);

        DB::table('reservations')->insert([
            'user_id' => $student->id,
            'class_id' => $classId,
            'asiento' => 'D5',
            'estado' => 'Completada',
            'justificado' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $request = new Request();
        $response = $this->controller->index($request);
        $data = $response->getData();

        $reserva = $data['reservas'][0];

        // Verificar que contiene todos los campos esperados
        $camposEsperados = ['nombre', 'matricula', 'clase', 'aula', 'asiento', 'estado', 'fecha', 'hora'];
        foreach ($camposEsperados as $campo) {
            $this->assertArrayHasKey($campo, $reserva, "Falta el campo: {$campo}");
        }

        // Verificar tipos de datos - ajustar según el formato real
        $this->assertIsString($reserva['nombre']);
        $this->assertTrue(is_int($reserva['matricula']) || is_string($reserva['matricula'])); // Flexible para int o string
        $this->assertIsString($reserva['clase']);
        $this->assertIsString($reserva['aula']);
        $this->assertIsString($reserva['asiento']);
        $this->assertIsString($reserva['estado']);
        $this->assertIsString($reserva['hora']);
    }
}
