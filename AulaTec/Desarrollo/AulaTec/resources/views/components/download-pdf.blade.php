{{-- Componente reutilizable para descargar comprobante de reserva en PDF --}}
{{-- Recibe $reservation con los datos de la reserva a descargar --}}

{{-- ======= FORMULARIO DE DESCARGA ======= --}}
{{-- Formulario POST que envía datos al controlador de PDF --}}
<form action="{{ route('reservations.download-pdf') }}" method="POST" class="w-full">
    {{-- Token CSRF para protección contra ataques de falsificación --}}
    @csrf
    
    {{-- ======= CAMPO OCULTO CON ID DE RESERVA ======= --}}
    {{-- ID de la reserva que se quiere descargar, pasado al backend --}}
    <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
    
    {{-- ======= BOTÓN DE DESCARGA ======= --}}
    {{-- Botón con estilo secundario (borde gris, fondo blanco) --}}
    <button type="submit" class="w-full py-2 px-4 bg-white border border-gray-300 text-gray-700 font-medium rounded-md shadow-sm hover:bg-gray-50">
        {{-- ======= CONTENIDO DEL BOTÓN ======= --}}
        {{-- Contenedor flex para centrar icono y texto --}}
        <span class="flex items-center justify-center">
            {{-- ======= ICONO DE DESCARGA ======= --}}
            {{-- SVG de documento con flecha hacia abajo --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                {{-- Trazos del icono: línea vertical y flechas hacia abajo --}}
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            {{-- Texto descriptivo del botón --}}
            Descargar Comprobante
        </span>
    </button>
</form>