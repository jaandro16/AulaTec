<?php

namespace App\View\Components;

use Illuminate\View\Component;

/**
 * Componente de Descarga de PDF
 * 
 * Componente Blade reutilizable que genera un botón o enlace
 * para descargar el PDF de confirmación de una reserva específica
 */
class DownloadPdf extends Component
{
    /**
     * La reserva para la cual se generará el PDF
     * 
     * @var mixed
     */
    public $reservation;

    /**
     * Crea una nueva instancia del componente
     *
     * @param mixed $reservation Los datos de la reserva
     */
    public function __construct($reservation)
    {
        // Almacenar la reserva para usar en la vista del componente
        $this->reservation = $reservation;
    }

    /**
     * Obtiene la vista que representa el componente
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        // Retorna la vista del componente ubicada en resources/views/components/
        return view('components.download-pdf');
    }
}