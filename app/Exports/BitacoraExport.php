<?php

namespace App\Exports;

use App\Models\Bitacora;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class BitacoraExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $filtros;

    public function __construct($filtros = [])
    {
        $this->filtros = $filtros;
    }

    /**
     * Obtener la colección de datos
     */
    public function collection()
    {
        $query = Bitacora::query()->orderBy('Fecha_Registro', 'desc');

        // Aplicar filtros
        if (isset($this->filtros['fecha_inicial'])) {
            $query->whereDate('Fecha_Registro', '>=', $this->filtros['fecha_inicial']);
        }

        if (isset($this->filtros['fecha_final'])) {
            $query->whereDate('Fecha_Registro', '<=', $this->filtros['fecha_final']);
        }

        if (isset($this->filtros['buscar'])) {
            $buscar = $this->filtros['buscar'];
            $query->where(function($q) use ($buscar) {
                $q->where('Nombre_Usuario', 'like', "%{$buscar}%")
                  ->orWhere('Accion', 'like', "%{$buscar}%")
                  ->orWhere('Observaciones', 'like', "%{$buscar}%")
                  ->orWhere('Modulo', 'like', "%{$buscar}%");
            });
        }

        return $query->get();
    }

    /**
     * Encabezados de las columnas
     */
    public function headings(): array
    {
        return [
            'Fecha y Hora',
            'Usuario',
            'Acción',
            'Módulo',
            'Observaciones',
            'Dirección IP'
        ];
    }

    /**
     * Mapear los datos de cada fila
     */
    public function map($bitacora): array
    {
        return [
            Carbon::parse($bitacora->Fecha_Registro)->format('d/m/Y H:i:s'),
            $bitacora->Nombre_Usuario,
            $bitacora->Accion,
            $bitacora->Modulo ?? '-',
            $bitacora->Observaciones ?? '-',
            $bitacora->IP_Address ?? '-'
        ];
    }

    /**
     * Estilos para el Excel
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '17A2B8']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF']]
            ],
        ];
    }

    /**
     * Título de la hoja
     */
    public function title(): string
    {
        return 'Bitácora';
    }
}
