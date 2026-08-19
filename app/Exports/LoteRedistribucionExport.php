<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class LoteRedistribucionExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithCustomStartCell,
    WithEvents
{
    protected $lote;

    public function __construct($lote)
    {
        $this->lote = $lote;
    }

    public function collection()
    {
        return $this->lote->detalles;
    }

    /**
     * Los datos empiezan desde A4
     */
    public function startCell(): string
    {
        return 'A4';
    }

    /**
     * Cabecera de columnas
     */
    public function headings(): array
    {
        return [
            'Sucursal Origen',
            'Sucursal Destino',
            'Código',
            'Cantidad',
            'Estado',
        ];
    }

    /**
     * Datos
     */
    public function map($detalle): array
    {
        return [
            $detalle->origen->suc_descri ?? '',
            $detalle->destino->suc_descri ?? '',
            $detalle->codigo ?? '',
            $detalle->cantidad ?? 0,
            $detalle->estado ?? '',
        ];
    }

    /**
     * Diseño del Excel
     */
    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                // Título
                $sheet->mergeCells('A1:E1');

                $sheet->setCellValue(
                    'A1',
                    'LOTE DE REDISTRIBUCIÓN'
                );

                // Número de lote
                $sheet->mergeCells('A2:E2');

                $sheet->setCellValue(
                    'A2',
                    'N° Lote: ' . $this->lote->numero_lote
                );

                // Estilo título
                $sheet->getStyle('A1:E1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                    ],
                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical' => 'center',
                    ],
                ]);

                // Estilo número de lote
                $sheet->getStyle('A2:E2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical' => 'center',
                    ],
                ]);

                // Estilo encabezados
                $sheet->getStyle('A4:E4')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical' => 'center',
                    ],
                ]);

                // Ancho de columnas
                $sheet->getColumnDimension('A')->setWidth(30);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(12);
                $sheet->getColumnDimension('E')->setWidth(18);

                // Altura de filas
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(2)->setRowHeight(22);
                $sheet->getRowDimension(4)->setRowHeight(22);
            },
        ];
    }
}
