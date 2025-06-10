{{-- Template para generar PDF de confirmación de reserva --}}
{{-- Se usa para crear documentos descargables cuando se completa una reserva nueva --}}

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        {{-- ======= CONFIGURACIÓN DE PÁGINA ======= --}}
        {{-- Eliminar márgenes por defecto del PDF --}}
        @page {
            margin: 0cm;
        }
        
        {{-- ======= ESTILOS GLOBALES ======= --}}
        {{-- Reset básico y fuente para todo el documento --}}
        body {
            margin: 0;
            font-family: sans-serif;
            font-size: 14px;
        }

        {{-- Contenedor principal que ocupa toda la altura --}}
        .container {
            width: 100%;
            height: 100vh;
            padding: 0;
        }

        {{-- ======= HEADER CON GRADIENTE ======= --}}
        {{-- Barra superior con título e información de confirmación --}}
        .header {
            background: rgb(147,51,234);                                           /* Color púrpura de respaldo */
            background: linear-gradient(to right, rgb(147,51,234), rgb(6,182,212)); /* Gradiente púrpura a cian */
            color: white;
            padding: 30px 20px;
            text-align: center;
            position: relative;
        }

        {{-- ======= TABLA PRINCIPAL DE INFORMACIÓN ======= --}}
        {{-- Tabla con los detalles de la reserva confirmada --}}
        .main-table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;                                              /* Eliminar espacios entre celdas */
        }

        {{-- Encabezados de la tabla --}}
        .main-table th {
            background-color: #f3e8ff;                                             /* Fondo púrpura claro */
            padding: 10px;
            text-align: left;
            font-size: 16px;
            color: #6b21a8;                                                         /* Texto púrpura oscuro */
        }

        {{-- Celdas de datos --}}
        .main-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;                                      /* Línea separadora gris */
        }

        {{-- Primera columna (etiquetas) --}}
        .main-table td:first-child {
            color: #6b7280;                                                         /* Texto gris para etiquetas */
            width: 30%;                                                             /* Ancho fijo para consistencia */
        }

        {{-- Segunda columna (valores) --}}
        .main-table td:last-child {
            font-weight: 500;                                                       /* Negrita para los valores */
        }

        {{-- ======= SECCIÓN DE CÓDIGO QR ======= --}}
        {{-- Área centrada para mostrar el QR de asistencia --}}
        .qr-section {
            margin: 40px auto;
            text-align: center;
        }

        {{-- Etiqueta descriptiva del QR --}}
        .qr-label {
            font-weight: bold;
            margin-bottom: 10px;
            color: #6b21a8;                                                         /* Púrpura para consistencia */
        }

        {{-- Contenedor del código QR con sombra --}}
        .qr-code {
            width: 150px;
            height: 150px;
            display: inline-block;
            padding: 10px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);                                 /* Sombra sutil */
        }

        {{-- ======= UTILIDADES DE COLOR ======= --}}
        {{-- Clase para texto púrpura (usado en número de asiento) --}}
        .purple-text {
            color: #9333ea;
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- ======= ENCABEZADO DEL DOCUMENTO ======= --}}
        {{-- Título principal con mensaje de confirmación --}}
        <div class="header">
            <h1>¡Reserva Confirmada!</h1>
            <p>Tu reserva ha sido procesada correctamente</p>
        </div>

        {{-- ======= TABLA DE DETALLES DE LA RESERVA ======= --}}
        {{-- Información completa de la reserva realizada --}}
        <table class="main-table">
            {{-- Encabezado de la tabla --}}
            <tr>
                <th colspan="2">Detalles de la Reserva</th>
            </tr>
            
            {{-- Nombre de la asignatura --}}
            <tr>
                <td>Clase</td>
                <td>{{ $reservation->classSession->subject->name }}</td>
            </tr>
            
            {{-- Profesor que imparte la clase --}}
            <tr>
                <td>Profesor</td>
                <td>{{ $reservation->classSession->teacher->nombre }} {{ $reservation->classSession->teacher->apellido }}</td>
            </tr>
            
            {{-- Fecha de la clase formateada en español --}}
            <tr>
                <td>Fecha</td>
                <td>{{ \Carbon\Carbon::parse($reservation->classSession->date)->format('d \d\e F \d\e Y') }}</td>
            </tr>
            
            {{-- Hora de inicio de la clase --}}
            <tr>
                <td>Hora</td>
                <td>{{ \Carbon\Carbon::parse($reservation->classSession->timeSlot->start_time)->format('H:i') }}</td>
            </tr>
            
            {{-- Duración calculada entre hora inicio y fin --}}
            <tr>
                <td>Duración</td>
                <td>{{ \Carbon\Carbon::parse($reservation->classSession->timeSlot->start_time)->diffInMinutes($reservation->classSession->timeSlot->end_time) }} minutos</td>
            </tr>
            
            {{-- Aula donde se imparte la clase --}}
            <tr>
                <td>Aula</td>
                <td>{{ $reservation->classSession->classroom->name }}</td>
            </tr>
            
            {{-- Número de asiento asignado (destacado en púrpura) --}}
            <tr>
                <td>Asiento</td>
                <td class="purple-text">{{ $reservation->asiento }}</td>
            </tr>
        </table>

        {{-- ======= SECCIÓN DEL CÓDIGO QR ======= --}}
        {{-- Código QR para verificar asistencia en la clase --}}
        <div class="qr-section">
            {{-- Título descriptivo --}}
            <div class="qr-label">Código QR de Asistencia</div>
            {{-- Imagen del código QR --}}
            <div class="qr-code">
                <img src="{{ $qrCode }}" alt="Código QR" style="width:150px; height:150px;">
            </div>
            {{-- Instrucciones de uso --}}
            <p style="color:#6b7280;font-size:14px;margin-top:8px">Muestra este código al ingresar a la clase</p>
        </div>
    </div>
</body>
</html>