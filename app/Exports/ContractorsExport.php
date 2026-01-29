<?php
namespace App\Exports;

use App\Models\Contractor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;

class ContractorsExport implements FromCollection, WithTitle, WithStyles, WithDrawings, WithEvents
{
    protected $year;
    protected $month;

    public function __construct($year, $month)
    {
        $this->year  = $year;
        $this->month = $month;
    }

    public function collection()
    {
        return Contractor::where('year_contract', $this->year)
            ->when($this->month, fn($q) => $q->where('month_contract', $this->month))
            ->get([
                'contract_number', 'date_contract', 'code_secop', 'class_contract',
                'contractor', 'firm_contractor', 'process_modality', 'object',
                'contract_term', 'start_date', 'cutoff_date', 'total_value',
                'dependency', 'supervision', 'expense_class', 'link_secop'
            ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo Institucional');
        $drawing->setPath(public_path('logos/logo_gobernacion_ptyo.png'));
        $drawing->setHeight(100);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(5);
        $drawing->setOffsetY(5);
        return $drawing;
    }

    public function title(): string
    {
        return "Contratistas_{$this->month}-{$this->year}";
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $event->sheet->getDelegate()->insertNewRowBefore(1, 5);
            },

            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Encabezados de tabla
                $sheet->fromArray([
                    ['# Contrato', 'Fecha', 'Código secop', 'Clase contrato', 'Contratista', 'Firma contratista',
                     'Modalidad proceso', 'Objeto', 'Plazo del contrato', 'Fecha inicio', 'Fecha fin',
                     'Valor contrato', 'Dependencia', 'Supervisor', 'Clase de gasto', 'Link']
                ], null, 'A6');

                $sheet->getStyle('A6:P6')->getFont()->setBold(true);

                // Encabezados de título
                $sheet->mergeCells('B2:R2');
                $sheet->mergeCells('B3:R3');
                $sheet->mergeCells('B4:R4');

                // Nombre del mes según el mes pasado al export
                $months = [
                    1  => 'ENERO',
                    2  => 'FEBRERO',
                    3  => 'MARZO',
                    4  => 'ABRIL',
                    5  => 'MAYO',
                    6  => 'JUNIO',
                    7  => 'JULIO',
                    8  => 'AGOSTO',
                    9  => 'SEPTIEMBRE',
                    10 => 'OCTUBRE',
                    11 => 'NOVIEMBRE',
                    12 => 'DICIEMBRE',
                ];

                $monthLabel = $this->month
                    ? ($months[(int) $this->month] ?? '')
                    : 'TODOS LOS MESES';

                $sheet->setCellValue('B2', 'RELACIÓN CONTRATOS ' . $this->year . ' - ' . $monthLabel);
                $sheet->setCellValue('B3', 'GOBERNACIÓN DEL PUTUMAYO');
                $sheet->setCellValue('B4', 'Fuente: Oficina Contratación');

                $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('B3')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('B4')->getFont()->setItalic(true);

                // Bordes a toda la tabla de datos
                $rowCount = $sheet->getHighestRow();
                $colCount = $sheet->getHighestColumn();

                $sheet->getStyle("A6:{$colCount}{$rowCount}")
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color'       => ['argb' => '000000'],
                            ],
                        ],
                    ]);

                $sheet->getColumnDimension('A')->setWidth(17);
            },
        ];
    }
}