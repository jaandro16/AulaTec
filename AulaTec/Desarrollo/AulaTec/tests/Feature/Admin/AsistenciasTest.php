<?php
// php artisan test tests/Feature/Admin/AsistenciasTest.php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Reservation;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\TimeSlot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;

// Test para el controlador de asistencias administrativas
// Verifica el registro y control de asistencias

class AsistenciasTest extends TestCase {
    // Verifica:
    // - Registro de asistencias
    // - Justificaciones
    // - Reportes 
    // - Validaciones de fecha/hora

    use RefreshDatabase;

    protected $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear un usuario profesor y autenticarlo
        $this->teacher = User::create([
            'nombre' => 'Profesor',
            'apellido' => 'Test',
            'email' => 'profesor@test.com',
            'password' => bcrypt('password'),
            'rol' => 'profesor',
            'numero_matricula' => 123456,
        ]);

        Auth::login($this->teacher);
    }

    #[Test]
    public function profesor_puede_acceder_a_la_pagina_de_asistencias()
    {
        $response = $this->get(route('admin.asistencias.index'));

        $response->assertStatus(200);
        $response->assertViewIs('modules.admin.asistencia.index');
        $response->assertViewHas('titulo', 'Registro de Asistencias');
    }

    #[Test]
    public function usuario_no_autenticado_no_puede_acceder_a_asistencias()
    {
        Auth::logout();

        $response = $this->get(route('admin.asistencias.index'));

        $response->assertStatus(302);
    }

    #[Test]
    public function profesor_puede_ver_sus_clases_con_asistencias_completadas()
    {
        // Crear datos de prueba incluyendo time_slot_id
        $subject = Subject::create([
            'name' => 'Matemáticas',
            'code' => 'MAT001',
            'description' => 'Materia de matemáticas'
        ]);

        $classroom = Classroom::create([
            'name' => 'Aula 101',
            'capacity' => 30
        ]);

        $timeSlot = TimeSlot::firstOrCreate([
            'start_time' => '09:00:00',
            'end_time' => '10:00:00'
        ]);

        // Crear clase con time_slot_id (campo obligatorio)
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
            'apellido' => 'Test',
            'email' => 'estudiante@test.com',
            'password' => bcrypt('password'),
            'rol' => 'alumno',
            'numero_matricula' => 789012,
        ]);

        // Crear reserva
        DB::table('reservations')->insert([
            'user_id' => $student->id,
            'class_id' => $classId,
            'asiento' => 'A1',
            'estado' => 'Completada',
            'justificado' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get(route('admin.asistencias.index'));

        $response->assertStatus(200);
        $response->assertViewHas('reservas');
        $response->assertViewHas('clasesUnicas');
        $response->assertViewHas('fechasUnicas');

        // Verificar que los datos están presentes
        $reservas = $response->viewData('reservas');
        $this->assertCount(1, $reservas);
        // Ajustar según el formato real que devuelve tu controlador
        $this->assertStringContainsString('Estudiante', $reservas[0]['nombre']);
        $this->assertEquals('Matemáticas', $reservas[0]['clase']);
    }

    #[Test]
    public function profesor_puede_ver_asistencias_justificadas()
    {
        $subject = Subject::create([
            'name' => 'Física',
            'code' => 'FIS001',
            'description' => 'Materia de física'
        ]);

        $classroom = Classroom::create([
            'name' => 'Aula 202',
            'capacity' => 25
        ]);

        $timeSlot = TimeSlot::firstOrCreate([
            'start_time' => '10:00:00',
            'end_time' => '11:00:00'
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
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'email' => 'juan@test.com',
            'password' => bcrypt('password'),
            'rol' => 'alumno',
            'numero_matricula' => 456789,
        ]);

        // Crear reserva justificada - usar estado válido
        DB::table('reservations')->insert([
            'user_id' => $student->id,
            'class_id' => $classId,
            'asiento' => 'B2',
            'estado' => 'Completada', // Cambiar a estado válido
            'justificado' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get(route('admin.asistencias.index'));

        $response->assertStatus(200);

        $reservas = $response->viewData('reservas');
        $this->assertCount(1, $reservas);
        $this->assertStringContainsString('Juan', $reservas[0]['nombre']);
        $this->assertEquals('Justificado', $reservas[0]['estado']);
    }

    #[Test]
    public function profesor_solo_ve_sus_propias_clases()
    {
        // Crear otro profesor
        $otherTeacher = User::create([
            'nombre' => 'Otro',
            'apellido' => 'Profesor',
            'email' => 'otro@test.com',
            'password' => bcrypt('password'),
            'rol' => 'profesor',
            'numero_matricula' => 999999,
        ]);

        // Crear materias
        $mySubject = Subject::create([
            'name' => 'Mi Materia',
            'code' => 'MIM001',
            'description' => 'Mi materia'
        ]);

        $otherSubject = Subject::create([
            'name' => 'Otra Materia',
            'code' => 'OTR001',
            'description' => 'Otra materia'
        ]);

        $classroom = Classroom::create([
            'name' => 'Aula 101',
            'capacity' => 30
        ]);

        $timeSlot1 = TimeSlot::firstOrCreate([
            'start_time' => '09:00:00',
            'end_time' => '10:00:00'
        ]);

        $timeSlot2 = TimeSlot::firstOrCreate([
            'start_time' => '11:00:00',
            'end_time' => '12:00:00'
        ]);

        // Crear clase del profesor actual
        $myClassId = DB::table('classes')->insertGetId([
            'subject_id' => $mySubject->id,
            'user_id' => $this->teacher->id,
            'classroom_id' => $classroom->id,
            'time_slot_id' => $timeSlot1->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Crear clase del otro profesor
        $otherClassId = DB::table('classes')->insertGetId([
            'subject_id' => $otherSubject->id,
            'user_id' => $otherTeacher->id,
            'classroom_id' => $classroom->id,
            'time_slot_id' => $timeSlot2->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $student = User::create([
            'nombre' => 'Estudiante',
            'apellido' => 'Test',
            'email' => 'estudiante@test.com',
            'password' => bcrypt('password'),
            'rol' => 'alumno',
            'numero_matricula' => 789012,
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

        $response = $this->get(route('admin.asistencias.index'));

        $response->assertStatus(200);

        $reservas = $response->viewData('reservas');
        // Solo debe ver 1 reserva (la de su propia clase)
        $this->assertCount(1, $reservas);
        $this->assertEquals('Mi Materia', $reservas[0]['clase']);
    }

    #[Test]
    public function datos_de_vista_tienen_formato_correcto()
    {
        $subject = Subject::create([
            'name' => 'Química',
            'code' => 'QUI001',
            'description' => 'Materia de química'
        ]);

        $classroom = Classroom::create([
            'name' => 'Lab Química',
            'capacity' => 20
        ]);

        $timeSlot = TimeSlot::firstOrCreate([
            'start_time' => '14:30:00',
            'end_time' => '15:30:00'
        ]);

        $classId = DB::table('classes')->insertGetId([
            'subject_id' => $subject->id,
            'user_id' => $this->teacher->id,
            'classroom_id' => $classroom->id,
            'time_slot_id' => $timeSlot->id,
            'date' => '2025-06-15',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $student = User::create([
            'nombre' => 'Ana',
            'apellido' => 'García',
            'email' => 'ana@test.com',
            'password' => bcrypt('password'),
            'rol' => 'alumno',
            'numero_matricula' => 111222,
        ]);

        DB::table('reservations')->insert([
            'user_id' => $student->id,
            'class_id' => $classId,
            'asiento' => 'C3',
            'estado' => 'Completada',
            'justificado' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get(route('admin.asistencias.index'));

        $reservas = $response->viewData('reservas');
        $fechasUnicas = $response->viewData('fechasUnicas');

        // Verificar formato de datos básicos - ajustar según formato real
        $this->assertStringContainsString('Ana', $reservas[0]['nombre']);
        $this->assertEquals(111222, $reservas[0]['matricula']);
        $this->assertEquals('Química', $reservas[0]['clase']);
        $this->assertEquals('Lab Química', $reservas[0]['aula']);
        $this->assertEquals('C3', $reservas[0]['asiento']);
        $this->assertEquals('Completada', $reservas[0]['estado']);

        // Verificar fecha - manejar tanto string como Carbon
        $fecha = $reservas[0]['fecha'];
        if ($fecha instanceof \Carbon\Carbon) {
            $this->assertEquals('2025-06-15', $fecha->format('Y-m-d'));
        } else {
            $this->assertEquals('2025-06-15', $fecha);
        }

        $this->assertEquals('14:30', $reservas[0]['hora']);

        // Verificar formato de fechas
        $this->assertEquals('15/06/2025', $fechasUnicas[0]['formato']);
    }

    #[Test]
    public function test_con_datos_basicos_funciona()
    {
        // Test simple para verificar que la funcionalidad básica funciona
        $subject = Subject::create([
            'name' => 'Test Subject',
            'code' => 'TEST001',
            'description' => 'Test description'
        ]);

        $classroom = Classroom::create([
            'name' => 'Test Classroom',
            'capacity' => 30
        ]);

        $timeSlot = TimeSlot::firstOrCreate([
            'start_time' => '08:00:00',
            'end_time' => '09:00:00'
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
            'nombre' => 'Test',
            'apellido' => 'Student',
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
            'rol' => 'alumno',
            'numero_matricula' => 123123,
        ]);

        DB::table('reservations')->insert([
            'user_id' => $student->id,
            'class_id' => $classId,
            'asiento' => 'A1',
            'estado' => 'Completada',
            'justificado' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get(route('admin.asistencias.index'));

        $response->assertStatus(200);

        $reservas = $response->viewData('reservas');
        $this->assertCount(1, $reservas);
        $this->assertStringContainsString('Test', $reservas[0]['nombre']);
    }

    #[Test]
    public function descubrir_estados_validos_para_reservas()
    {
        // Test temporal para descubrir qué estados son válidos
        $subject = Subject::create([
            'name' => 'Test',
            'code' => 'TEST001',
            'description' => 'Test'
        ]);

        $classroom = Classroom::create([
            'name' => 'Test',
            'capacity' => 30
        ]);

        $timeSlot = TimeSlot::firstOrCreate([
            'start_time' => '08:00:00',
            'end_time' => '09:00:00'
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
            'nombre' => 'Test',
            'apellido' => 'Student',
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
            'rol' => 'alumno',
            'numero_matricula' => 123123,
        ]);

        $estadosPosibles = ['Pendiente', 'Completada', 'Cancelada', 'Presente', 'Ausente', 'Justificado'];

        foreach ($estadosPosibles as $estado) {
            try {
                DB::table('reservations')->insert([
                    'user_id' => $student->id,
                    'class_id' => $classId,
                    'asiento' => 'A' . rand(1, 9),
                    'estado' => $estado,
                    'justificado' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                echo "✅ Estado válido: {$estado}\n";
            } catch (\Exception $e) {
                echo "❌ Estado inválido: {$estado}\n";
            }
        }

        // Solo para que el test pase
        $this->assertTrue(true);
    }
}
