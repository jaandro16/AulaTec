@extends('layouts.admin')
@section('titulo', 'Escanear Código QR')

@section('contenido')
<div class="p-3 sm:p-4 lg:p-6 space-y-4 sm:space-y-6">
    {{-- Encabezado Principal --}}
    <div class="mb-4 sm:mb-6 lg:mb-8">
        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold tracking-tight">Escanear Código QR</h1>
        <p class="text-xs sm:text-sm lg:text-base text-gray-500">
            Escanea los códigos QR de los estudiantes para registrar su asistencia.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8">
        <div class="bg-white rounded-lg sm:rounded-xl shadow-md overflow-hidden">
            <div class="p-4 sm:p-6">
                <h2 class="text-lg sm:text-xl font-semibold mb-2">Lector de QR</h2>
                <p class="text-xs sm:text-sm text-gray-500 mb-4 sm:mb-6">
                    Conecta el lector USB y escanea el código QR del estudiante
                </p>

                {{-- Área de entrada oculta --}}
                <input type="text" 
                    id="qrInput" 
                    class="sr-only"
                    autocomplete="off" 
                    placeholder="QR Data">

                {{-- Estado del lector --}}
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 flex items-center space-x-3">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-purple-900">Lector USB Activo</h3>
                        <p class="text-xs text-purple-700">Escanea un código QR para ver la información</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Panel de Información --}}
        <div class="bg-white rounded-lg sm:rounded-xl shadow-md">
            <div class="p-4 sm:p-6">
                <h2 class="text-lg sm:text-xl font-semibold mb-2">Información del Estudiante</h2>
                <p class="text-xs sm:text-sm text-gray-500 mb-4 sm:mb-6">
                    Datos del estudiante y su reserva
                </p>

                <div id="scanResult" class="min-h-[250px] sm:min-h-[300px]">
                    {{-- Estado inicial/error --}}
                    <div class="flex flex-col items-center justify-center h-full text-gray-400 p-4">
                        <svg class="w-12 h-12 sm:w-16 sm:h-16 mb-3 sm:mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="text-base sm:text-lg font-medium mb-2 text-center">No hay datos escaneados</h3>
                        <p class="text-xs sm:text-sm text-center">
                            Escanea un código QR para ver la información<br>del estudiante y su reserva
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.registrarAsistencia = function(reservationId) {
    fetch('{{ route('admin.qr.registrar-asistencia') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ reservationId: reservationId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar mensaje de éxito
            document.getElementById('scanResult').innerHTML = `
                <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <p class="text-green-700">Asistencia registrada correctamente</p>
                    </div>
                </div>
            `;
        } else {
            throw new Error(data.message || 'Error al registrar la asistencia');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError(error.message);
    });
};

document.addEventListener('DOMContentLoaded', function() {
    const qrInput = document.getElementById('qrInput');
    const scanResult = document.getElementById('scanResult');
    let buffer = '';
    let lastKeyTime = Date.now();

    // Mantener el foco en el input oculto
    qrInput.focus();
    document.addEventListener('click', () => qrInput.focus());

    // Capturar entrada del lector
    qrInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            
            if (buffer.length > 0) {
                // console.log('=== NUEVO ESCANEO ===');
                // console.log('QR Buffer:', buffer);
                // console.log('Buffer Length:', buffer.length);
                
                // ✅ LIMPIAR COMPLETAMENTE EL ESTADO ANTERIOR CON ID ÚNICO
                const loadingId = 'loading_' + Date.now();
                scanResult.innerHTML = `
                    <div class="flex items-center justify-center p-8" data-loading-id="${loadingId}">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                        <span class="ml-2 text-sm text-gray-600">Validando QR... (${loadingId.slice(-6)})</span>
                    </div>
                `;
                
                // ✅ COPIAR BUFFER Y LIMPIARLO INMEDIATAMENTE
                const qrDataToProcess = buffer;
                buffer = ''; // Limpiar buffer ANTES del procesamiento
                
                // ✅ PROCESAR CON DELAY MÍNIMO PARA EVITAR RACE CONDITIONS
                setTimeout(() => {
                    processQRData(qrDataToProcess);
                }, 10);
            }
        } else {
            const currentTime = Date.now();
            
            // ✅ REDUCIR TIMEOUT PARA SER MÁS ESTRICTO
            if (currentTime - lastKeyTime > 100) {
                // console.log('🔄 Reiniciando buffer por timeout');
                buffer = '';
            }
            
            buffer += e.key;
            lastKeyTime = currentTime;
        }
    });

    function processQRData(qrData) {
        // console.log('Procesando QR:', qrData);
        
        // ✅ CREAR TIMESTAMP ÚNICO PARA CADA PETICIÓN
        const uniqueTimestamp = Date.now() + Math.random();
        const requestId = 'req_' + uniqueTimestamp;
        
        // ✅ AGREGAR PARÁMETROS ANTI-CACHÉ MÁS AGRESIVOS
        const url = '{{ route('admin.qr.process') }}?' + new URLSearchParams({
            _t: uniqueTimestamp,
            _r: Math.random(),
            _rid: requestId,
            _force: 'true',
            _nocache: Date.now()
        });
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                // ✅ HEADERS ANTI-CACHÉ MÁS ESTRICTOS
                'Cache-Control': 'no-cache, no-store, must-revalidate, max-age=0',
                'Pragma': 'no-cache',
                'Expires': '0',
                'X-Requested-With': 'XMLHttpRequest',
                'X-Request-ID': requestId,
                'X-Force-Validation': 'true'
            },
            body: JSON.stringify({ 
                qrData: qrData,
                timestamp: uniqueTimestamp,
                requestId: requestId,
                forceValidation: true,
                clientTime: new Date().toISOString(), // ✅ Enviar hora del cliente
                validateDate: true // ✅ Flag específico para validar fecha
            })
        })
        .then(response => {
            // console.log('HTTP Status:', response.status);
            // console.log('Response Headers:', Object.fromEntries(response.headers.entries()));
            // console.log('Request ID:', requestId);
            
            // ✅ VERIFICAR STATUS HTTP ESTRICTAMENTE
            if (!response.ok) {
                throw new Error(`Error HTTP ${response.status}: ${response.statusText}`);
            }
            
            return response.json();
        })
        .then(data => {
            // console.log('=== RESPUESTA DEL SERVIDOR ===');
            // console.log('Request ID:', requestId);
            // console.log('Success:', data.success);
            // console.log('Message:', data.message);
            // console.log('Data:', data.data);
            // console.log('QR Data Original:', qrData);
            // console.log('================================');
            
            // ✅ VALIDACIÓN ESTRICTA CON VERIFICACIÓN ADICIONAL
            if (data.success === true && data.data) {
                // ✅ VALIDACIÓN ADICIONAL DEL LADO DEL CLIENTE
                if (validateQRDataClientSide(data.data, qrData)) {
                    // console.log('✅ QR VÁLIDO - Mostrando datos');
                    displayStudentData(data);
                } else {
                    // console.log('❌ QR FALLÓ VALIDACIÓN CLIENT-SIDE');
                    showError('QR inválido: La reserva ha expirado o no es válida');
                }
            } else {
                // console.log('❌ QR INVÁLIDO - Mostrando error');
                const errorMessage = data.message || 'QR inválido o expirado';
                showError(errorMessage);
            }
        })
        .catch(error => {
            // console.error('❌ ERROR EN FETCH:', error);
            showError(`Error de conexión: ${error.message}`);
        });
    }

    function validateQRDataClientSide(serverData, originalQrData) {
        // console.log('🔍 Validando QR del lado del cliente...');
        
        try {
            // ✅ Verificar que tenemos datos básicos
            if (!serverData || !serverData.fecha || !serverData.hora) {
                // console.log('❌ Faltan datos básicos del servidor');
                return false;
            }
            
            // ✅ Parsear la fecha de la clase desde el servidor
            const fechaClase = serverData.fecha; // Formato: "YYYY-MM-DD" o similar
            const horaClase = serverData.hora;   // Formato: "HH:MM" o similar
            
            // ✅ Crear objeto Date de la clase
            const fechaHoraClase = new Date(fechaClase + 'T' + horaClase);
            const ahora = new Date();
            
            // console.log('📅 Fecha/Hora de la clase:', fechaHoraClase.toLocaleString());
            // console.log('🕐 Hora actual:', ahora.toLocaleString());
            
            // ✅ Verificar si la clase ya pasó (más de 2 horas después del inicio)
            const dosHorasDespues = new Date(fechaHoraClase.getTime() + (2 * 60 * 60 * 1000));
            
            if (ahora > dosHorasDespues) {
                // console.log('❌ La clase expiró hace más de 2 horas');
                return false;
            }
            
            // ✅ Verificar si la clase es muy futura (más de 24 horas antes)
            const veinticuatroHorasAntes = new Date(fechaHoraClase.getTime() - (24 * 60 * 60 * 1000));
            
            if (ahora < veinticuatroHorasAntes) {
                // console.log('❌ La clase está programada para más de 24 horas en el futuro');
                return false;
            }
            
            // ✅ Verificar que el QR original contiene datos válidos
            if (!originalQrData || originalQrData.length < 10) {
                // console.log('❌ QR original inválido o muy corto');
                return false;
            }
            
            // console.log('✅ Validación del lado del cliente exitosa');
            return true;
            
        } catch (error) {
            // console.error('❌ Error en validación del lado del cliente:', error);
            return false;
        }
    }

    function displayStudentData(data) {
        // console.log('📋 Mostrando datos del estudiante');
        
        let alertHtml = '';
        if (data.alreadyScanned) {
            alertHtml = `
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="text-yellow-700">${data.message}</p>
                    </div>
                </div>
            `;
        }
        
        scanResult.innerHTML = `
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                ${alertHtml}
                <div class="p-6">
                    <h1 class="text-2xl font-bold mb-1">Información del Estudiante</h1>
                    <p class="text-gray-500 text-sm mb-6">Datos del estudiante y su reserva</p>
                    
                    <div class="flex items-center mb-6">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold mb-1">${data.data.estudiante}</h2>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 my-6"></div>
                    
                    <h2 class="text-lg font-semibold mb-4">Detalles de la Reserva</h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold">${data.data.clase}</h3>
                                <p class="text-gray-600 text-sm">Profesor: ${data.data.profesor}</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold">${data.data.fecha}</h3>
                                <p class="text-gray-600 text-sm">${data.data.hora}</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold">${data.data.aula}</h3>
                                <p class="text-gray-600 text-sm">Asiento: ${data.data.asiento}</p>
                            </div>
                        </div>
                    </div>
                    ${data.alreadyScanned ? '' : `
                        <div class="border-t border-gray-200 mt-6 pt-6">
                            <button 
                                onclick="registrarAsistencia('${data.data.id}')"
                                class="w-full bg-gradient-to-r from-purple-600 to-blue-500 text-white font-medium px-4 py-3 rounded-md hover:from-purple-700 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-colors">
                                Registrar Asistencia
                            </button>
                        </div>
                    `}
                </div>
            </div>
        `;
    }

    window.showError = function(message) {
        // console.log('🚫 Mostrando error:', message);
        
        // ✅ Agregar timestamp único para evitar caché visual
        const errorId = 'error_' + Date.now();
        
        scanResult.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full text-red-600 p-6 min-h-[250px]" data-error-id="${errorId}">
                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-lg font-medium mb-2 text-center text-red-800">Error al procesar QR</h3>
                <p class="text-sm text-center text-red-700 mb-2">
                    ${message}
                </p>
                <p class="text-xs text-center text-red-600">
                    Escanea un QR válido y dentro del horario permitido
                </p>
                <div class="mt-4 px-4 py-2 bg-red-100 rounded-lg">
                    <p class="text-xs text-red-700 text-center">
                        Error ID: ${errorId}<br>
                        Timestamp: ${new Date().toLocaleTimeString()}
                    </p>
                </div>
                <div class="mt-2 px-3 py-1 bg-red-50 rounded text-xs text-red-600">
                    💡 Si el problema persiste, verifica que el QR no haya expirado
                </div>
            </div>
        `;
    };
});
</script>
@endpush

@push('styles')
<style>
    #reader {
        border: none !important;
    }
    
    #reader video {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
    }

    /* Eliminar bordes blancos del lector */
    #reader div img {
        display: none !important;
    }

    /* Ocultar el mensaje de powered by */
    #reader__scan_region > div:last-child {
        display: none !important;
    }

    /* Eliminar padding innecesario */
    #reader__scan_region {
        padding: 0 !important;
    }
</style>
@endpush