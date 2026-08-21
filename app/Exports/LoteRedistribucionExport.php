<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Models\StockVentasSucursal;

class LoteRedistribucionExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithCustomStartCell,
    WithEvents
{
    protected $lote;

    /**
     * Descripciones agrupadas por código
     *
     * [
     *     '060616748' => 'REMERA M/C TP COLEGIAL NIÑOS',
     *     'BOMJUV001' => 'BOMBER JUVENIL',
     * ]
     */
    protected $descripciones = [];

    public function __construct($lote)
    {
        $this->lote = $lote;

        /*
         * ============================================================
         * CARGAR DESCRIPCIONES DESDE STOCK_VENTAS_SUCURSALES
         * ============================================================
         *
         * La descripción se encuentra en:
         *
         * stock_ventas_sucursales.grupo_plan
         *
         * Relación:
         *
         * codigo -> grupo_plan
         */

        $codigos = $lote->detalles
            ->pluck('codigo')
            ->filter()
            ->unique()
            ->values();

        if ($codigos->isNotEmpty()) {

            $this->descripciones = StockVentasSucursal::whereIn(
                'codigo',
                $codigos
            )
                ->whereNotNull('grupo_plan')
                ->where('grupo_plan', '<>', '')
                ->get([
                    'codigo',
                    'grupo_plan'
                ])
                ->groupBy('codigo')
                ->map(function ($items) {

                    /*
                     * Si un código aparece varias veces,
                     * tomamos la primera descripción encontrada.
                     */

                    return $items->first()->grupo_plan;
                })
                ->toArray();
        }
    }

    /**
     * ================================================================
     * COLECCIÓN
     * ================================================================
     */
    public function collection()
    {
        return $this->lote->detalles;
    }

    /**
     * ================================================================
     * CELDA INICIAL
     * ================================================================
     *
     * Los datos comienzan desde A4.
     */
    public function startCell(): string
    {
        return 'A4';
    }

    /**
     * ================================================================
     * ENCABEZADOS
     * ================================================================
     */
    public function headings(): array
    {
        return [
            'Sucursal Origen',
            'Sucursal Destino',
            'Código',
            'Descripción',
            'Cantidad',
            'Estado',
        ];
    }

    /**
     * ================================================================
     * MAPEO DE DATOS
     * ================================================================
     */
    public function map($detalle): array
    {
        $codigo = $detalle->codigo ?? '';

        /*
         * Buscar descripción usando:
         *
         * codigo -> grupo_plan
         */
        $descripcion = $this->descripciones[$codigo]
            ?? 'SIN DESCRIPCIÓN';

        return [
            $detalle->origen->suc_descri ?? '',
            $detalle->destino->suc_descri ?? '',
            $codigo,
            $descripcion,
            $detalle->cantidad ?? 0,
            $detalle->estado ?? '',
        ];
    }

    /**
     * ================================================================
     * DISEÑO DEL EXCEL
     * ================================================================
     */
    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                /*
                 * ====================================================
                 * TÍTULO
                 * ====================================================
                 */

                $sheet->mergeCells('A1:F1');

                $sheet->setCellValue(
                    'A1',
                    'LOTE DE REDISTRIBUCIÓN'
                );

                /*
                 * ====================================================
                 * NÚMERO DE LOTE
                 * ====================================================
                 */

                $sheet->mergeCells('A2:F2');

                $sheet->setCellValue(
                    'A2',
                    'N° Lote: ' . $this->lote->numero_lote
                );

                /*
                 * ====================================================
                 * ESTILO DEL TÍTULO
                 * ====================================================
                 */

                $sheet->getStyle('A1:F1')->applyFromArray([

                    'font' => [
                        'bold' => true,
                        'size' => 16,
                    ],

                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical'   => 'center',
                    ],
                ]);

                /*
                 * ====================================================
                 * ESTILO DEL NÚMERO DE LOTE
                 * ====================================================
                 */

                $sheet->getStyle('A2:F2')->applyFromArray([

                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],

                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical'   => 'center',
                    ],
                ]);

                /*
                 * ====================================================
                 * ENCABEZADOS
                 * ====================================================
                 */

                $sheet->getStyle('A4:F4')->applyFromArray([

                    'font' => [
                        'bold' => true,
                    ],

                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical'   => 'center',
                    ],

                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => 'thin',
                        ],
                    ],
                ]);

                /*
                 * ====================================================
                 * DETECTAR ÚLTIMA FILA
                 * ====================================================
                 */

                $ultimaFila = 4 + $this->lote->detalles->count();

                /*
                 * ====================================================
                 * BORDES DE LA TABLA
                 * ====================================================
                 */

                if ($ultimaFila >= 4) {

                    $sheet
                        ->getStyle('A4:F' . $ultimaFila)
                        ->applyFromArray([

                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => 'thin',
                                ],
                            ],

                            'alignment' => [
                                'vertical' => 'center',
                            ],
                        ]);
                }

                /*
                 * ====================================================
                 * ALINEACIÓN
                 * ====================================================
                 */

                if ($ultimaFila >= 5) {

                    // Código
                    $sheet
                        ->getStyle('C5:C' . $ultimaFila)
                        ->getAlignment()
                        ->setHorizontal('center');

                    // Cantidad
                    $sheet
                        ->getStyle('E5:E' . $ultimaFila)
                        ->getAlignment()
                        ->setHorizontal('center');

                    // Estado
                    $sheet
                        ->getStyle('F5:F' . $ultimaFila)
                        ->getAlignment()
                        ->setHorizontal('center');
                }

                /*
                 * ====================================================
                 * ANCHO DE COLUMNAS
                 * ====================================================
                 */

                $sheet->getColumnDimension('A')->setWidth(30);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(55);
                $sheet->getColumnDimension('E')->setWidth(12);
                $sheet->getColumnDimension('F')->setWidth(18);

                /*
                 * ====================================================
                 * ALTURA DE FILAS
                 * ====================================================
                 */

                $sheet->getRowDimension(1)->setRowHeight(28);
                $sheet->getRowDimension(2)->setRowHeight(24);
                $sheet->getRowDimension(4)->setRowHeight(25);

                /*
                 * ====================================================
                 * FORMATO DE CANTIDAD
                 * ====================================================
                 */

                if ($ultimaFila >= 5) {

                    $sheet
                        ->getStyle('E5:E' . $ultimaFila)
                        ->getNumberFormat()
                        ->setFormatCode('#,##0');
                }

                /*
                 * ====================================================
                 * ACTIVAR FILTROS
                 * ====================================================
                 */

                $sheet->setAutoFilter(
                    'A4:F' . $ultimaFila
                );

                /*
                 * ====================================================
                 * CONGELAR ENCABEZADOS
                 * ====================================================
                 */

                $sheet->freezePane('A5');

                /*
                 * ====================================================
                 * TEXTO DE DESCRIPCIÓN
                 * ====================================================
                 */

                if ($ultimaFila >= 5) {

                    $sheet
                        ->getStyle('D5:D' . $ultimaFila)
                        ->getAlignment()
                        ->setWrapText(true);
                }
            },
        ];
    }
}
