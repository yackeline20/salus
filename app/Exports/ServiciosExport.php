<?php

namespace App\Exports;

use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ServiciosExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    public function collection()
    {
        try {
            $response = Http::timeout(5)->get('http://localhost:3000/tratamiento');
            
            if ($response->successful()) {
                $tratamientos = collect($response->json());
                
                return $tratamientos->map(function ($item) {
                    return [
                        'Código' => $item['Cod_Tratamiento'] ?? '',
                        'Servicio' => $item['Nombre_Tratamiento'] ?? '',
                        'Descripción' => $item['Descripcion'] ?? '',
                        'Precio' => 'L ' . number_format($item['Precio_Estandar'] ?? 0, 2),
                        'Duración' => ($item['Duracion_Estimada_Min'] ?? 0) . ' min',
                    ];
                });
            }
            
            return collect([]);
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    public function headings(): array
    {
        return ['Código', 'Servicio', 'Descripción', 'Precio', 'Duración'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '8B4513']]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 35,
            'C' => 50,
            'D' => 15,
            'E' => 15,
        ];
    }
}