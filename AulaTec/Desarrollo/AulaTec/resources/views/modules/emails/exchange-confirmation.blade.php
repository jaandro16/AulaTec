{{-- Vista del email de confirmación de intercambio de reservas --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    {{-- CSS EMBEBIDO PARA COMPATIBILIDAD CON CLIENTES DE EMAIL --}}
    <style>
        /* ESTILOS BASE PARA EL CUERPO DEL EMAIL */
        body {
            font-family: Arial, sans-serif;        /* Fuente compatible con todos los clientes */
            line-height: 1.6;                      /* Altura de línea legible */
            background-color: #f5f5f5;             /* Fondo gris claro */
            margin: 0;
            padding: 20px;                         /* Espacio alrededor del contenido */
        }
        
        /* CONTENEDOR PRINCIPAL DEL EMAIL */
        .container {
            max-width: 600px;                      /* Ancho máximo estándar para emails */
            margin: 0 auto;                        /* Centrar horizontalmente */
            background: white;                     /* Fondo blanco para el contenido */
            border-radius: 8px;                    /* Bordes redondeados */
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); /* Sombra sutil */
            overflow: hidden;                      /* Contener elementos flotantes */
        }
        
        /* HEADER CON GRADIENTE Y MENSAJE DE ÉXITO */
        .header {
            text-align: center;
            padding: 24px;
            background: linear-gradient(to right, #9333ea, #06b6d4); /* Gradiente morado-cyan */
            color: white;
            margin-bottom: 24px;
        }
        
        /* ICONO DE CHECK CIRCULAR */
        .check-icon {
            background: white;                     /* Fondo blanco para contraste */
            width: 64px;
            height: 64px;
            border-radius: 50%;                    /* Forma circular */
            margin: 0 auto 16px;                   /* Centrado con margen inferior */
            display: flex;
            align-items: center;                   /* Centrar verticalmente */
            justify-content: center;               /* Centrar horizontalmente */
        }
        
        /* COLOR DEL SVG DENTRO DEL ICONO */
        .check-icon svg {
            color: #16a34a;                       /* Verde para éxito */
            stroke: #16a34a;
        }
        
        /* SECCIÓN DE DETALLES DE LA RESERVA */
        .details {
            background: #f9fafb;                   /* Fondo gris muy claro */
            margin: 24px;                          /* Margen alrededor */
            padding: 16px;                         /* Espaciado interno */
            border-radius: 8px;                    /* Bordes redondeados */
        }
        
        /* FILA INDIVIDUAL DE DETALLE (ETIQUETA + VALOR) */
        .detail-row {
            display: flex;                         /* Layout flexbox */
            justify-content: space-between;        /* Separar etiqueta y valor */
            align-items: center;                   /* Alinear verticalmente */
            margin-bottom: 8px;                    /* Espacio entre filas */
            padding-bottom: 8px;                   /* Padding inferior */
            border-bottom: 1px solid #e5e7eb;     /* Línea separadora */
            width: 100%;
        }
        
        /* ELIMINAR BORDE DE LA ÚLTIMA FILA */
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        /* ETIQUETA DEL DETALLE (LADO IZQUIERDO) */
        .detail-label {
            color: #6b7280;                        /* Color gris para etiquetas */
            flex-shrink: 0;                        /* No reducir tamaño */
            margin-right: 16px;                    /* Espacio hacia el valor */
        }
        
        /* VALOR DEL DETALLE (LADO DERECHO) */
        .detail-value {
            font-weight: 500;                      /* Texto semi-bold */
            text-align: right;                     /* Alineado a la derecha */
            margin-left: auto;                     /* Empujar hacia la derecha */
        }
        
        /* SECCIÓN DEL CÓDIGO QR */
        .qr-section {
            text-align: center;
            margin: 24px;
        }
        
        /* CONTENEDOR DEL CÓDIGO QR */
        .qr-container {
            background: white;
            padding: 16px;                         /* Espaciado alrededor del QR */
            border-radius: 8px;
            display: inline-block;                 /* Solo el tamaño necesario */
            box-shadow: 0 1px 3px rgba(0,0,0,0.1); /* Sombra sutil */
        }
        
        /* TAMAÑO DEL CÓDIGO QR */
        .qr-code {
            width: 160px;
            height: 160px;
        }
        
        /* CAJA DE INFORMACIÓN ADICIONAL */
        .info-box {
            background: #eff6ff;                   /* Fondo azul claro */
            border: 1px solid #bfdbfe;             /* Borde azul */
            border-radius: 8px;
            padding: 16px;
            margin: 24px;
            color: #1e40af;                        /* Texto azul oscuro */
        }
        
        /* LISTA DENTRO DE LA CAJA DE INFORMACIÓN */
        .info-box ul {
            margin: 8px 0 0 20px;                  /* Márgenes para la lista */
            padding: 0;
        }
        
        /* ESTILOS DE TÍTULOS */
        h1 { 
            font-size: 24px;
            margin: 0 0 8px 0;
        }
        h2 {
            font-size: 18px;
            margin: 0 0 16px 0;
        }
    </style>
</head>
<body>
    {{-- CONTENEDOR PRINCIPAL DEL EMAIL --}}
    <div class="container">
        
        {{-- ======= HEADER CON MENSAJE DE ÉXITO ======= --}}
        <div class="header">
            {{-- Icono de check dentro de círculo blanco --}}
            <div class="check-icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" class="check-icon-svg" stroke-width="2">
                    {{-- Path del icono de check --}}
                    <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            {{-- Título principal del email --}}
            <h1>¡Intercambio Completado!</h1>
            {{-- Subtítulo explicativo --}}
            <p style="margin:0;opacity:0.9">Tu reserva ha sido intercambiada correctamente</p>
        </div>

        {{-- ======= DETALLES DE LA NUEVA RESERVA ======= --}}
        <div class="details">
            <h2>Detalles de la Nueva Reserva</h2>
            
            {{-- Nombre de la clase/materia --}}
            <div class="detail-row">
                <span class="detail-label">Clase: </span>
                <span class="detail-value">{{ $reservation->classSession->subject->name }}</span>
            </div>
            
            {{-- Profesor a cargo de la clase --}}
            <div class="detail-row">
                <span class="detail-label">Profesor: </span>
                <span class="detail-value">{{ $reservation->classSession->teacher->nombre }} {{ $reservation->classSession->teacher->apellido }}</span>
            </div>
            
            {{-- Fecha de la clase formateada en español --}}
            <div class="detail-row">
                <span class="detail-label">Fecha: </span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($reservation->classSession->date)->format('d \d\e F \d\e Y') }}</span>
            </div>
            
            {{-- Hora de inicio formateada en 24h --}}
            <div class="detail-row">
                <span class="detail-label">Hora: </span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($reservation->classSession->timeSlot->start_time)->format('H:i') }}</span>
            </div>
            
            {{-- Duración calculada en minutos --}}
            <div class="detail-row">
                <span class="detail-label">Duración: </span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($reservation->classSession->timeSlot->start_time)->diffInMinutes($reservation->classSession->timeSlot->end_time) }} minutos</span>
            </div>
            
            {{-- Aula donde se imparte la clase --}}
            <div class="detail-row">
                <span class="detail-label">Aula: </span>
                <span class="detail-value">{{ $reservation->classSession->classroom->name }}</span>
            </div>
            
            {{-- Número de asiento asignado (destacado en morado) --}}
            <div class="detail-row">
                <span class="detail-label">Asiento: </span>
                <span class="detail-value" style="color:#9333ea">{{ $reservation->asiento }}</span>
            </div>
        </div>

        {{-- ======= INFORMACIÓN ADICIONAL IMPORTANTE ======= --}}
        <div class="info-box">
            <p style="margin:0 0 8px 0;font-weight:500">Información importante:</p>
            <ul>
                {{-- Lista de recordatorios para el estudiante --}}
                <li>Llega 10 minutos antes del inicio de la clase</li>
                <li>Recuerda traer tu identificación estudiantil</li>
                <li>No olvides cancelar tu reserva si no puedes asistir</li>
            </ul>
        </div>
    </div>
</body>
</html>