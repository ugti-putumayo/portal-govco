<?php
namespace App\Exports;

use App\Models\PlantOfficial;
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

class PlantOfficialExport implements FromCollection, WithTitle, WithStyles, WithDrawings, WithEvents
{
    protected $year;
    protected $month;

    public function __construct($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function collection()
    {
        $officials = PlantOfficial::where('year_plantofficial', $this->year)
            ->when($this->month, fn($q) => $q->where('month_plantofficial', $this->month))
            ->get();

        // Mapear para mostrar "Activo"/"Inactivo"
        return $officials->map(function ($item) {
            return [
                'Tipo de documento' => $item->document_type,
                'Número de documento' => $item->document_number,
                'Nombres y apellidos' => $item->fullname,
                'Cargo' => $item->charge,
                'Área' => $item->dependency,
                'Subdependencia' => $item->subdependencie,
                'Código' => $item->code,
                'Grado' => $item->grade,
                'Nivel' => $item->level,
                'Denominación' => $item->denomination,
                'Salario' => $item->total_value,
                'Gastos de representación' => $item->representation_expenses,
                'Fecha de inicio' => $item->init_date,
                'Fecha de vacaciones' => $item->vacation_date,
                'Fecha de bonificación' => $item->bonus_date,
                'Fecha de nacimiento' => $item->birthdate,
                'Correo electrónico' => $item->email,
                'Celular' => $item->cellphone,
                'EPS' => $item->eps,
                'Estado' => $item->is_active ? 'Activo' : 'Inactivo',
            ];
        });
    }

    public function styles(Worksheet $sheet)
    {
        return [
            6 => ['font' => ['bold' => true]], // Headers en fila 6
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
        return "Plant_Officials_{$this->month}-{$this->year}";
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function(BeforeSheet $event) {
                $event->sheet->getDelegate()->insertNewRowBefore(1, 5);
            },

            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Encabezados en fila 6
                $sheet->fromArray([
                    [
                        'Tipo de documento', 'Número de documento', 'Nombres y apellidos', 'Cargo', 'Área', 'Subdependencia',
                        'Código', 'Grado', 'Nivel', 'Denominación', 'Salario', 'Gastos de representación',
                        'Fecha de inicio', 'Fecha de vacaciones', 'Fecha de bonificación', 'Fecha de nacimiento',
                        'Correo electrónico', 'Celular', 'EPS', 'Estado'
                    ]
                ], null, 'A6');

                $sheet->getStyle('A6:T6')->getFont()->setBold(true);

                // Títulos de cabecera
                $sheet->mergeCells('B2:T2');
                $sheet->mergeCells('B3:T3');
                $sheet->mergeCells('B4:T4');

                $sheet->setCellValue('B2', 'RELACIÓN DE FUNCIONARIOS DE PLANTA ' . $this->year . ' - ' . strtoupper(now()->monthName));
                $sheet->setCellValue('B3', 'GOBERNACIÓN DEL PUTUMAYO');
                $sheet->setCellValue('B4', 'Fuente: Gestión Humana');

                $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('B3')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('B4')->getFont()->setItalic(true);

                $rowCount = $sheet->getHighestRow();
                $colCount = $sheet->getHighestColumn();
                $sheet->getStyle("A6:{$colCount}{$rowCount}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $sheet->getColumnDimension('A')->setWidth(20);
            }
        ];
    }
}