<?php
namespace App\Imports;

use App\Models\Contractor;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ContractorsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Procesar fechas si vienen como valores numéricos
        $date_contract = $row['fecha'];
        if (is_numeric($date_contract)) {
            $date_contract = Date::excelToDateTimeObject($date_contract)->format('Y-m-d');
        }

        $start_date = $row['fecha_inicio'];
        if (is_numeric($start_date)) {
            $start_date = Date::excelToDateTimeObject($start_date)->format('Y-m-d');
        }

        $cutoff_date = $row['fecha_vencimiento'];
        if (is_numeric($cutoff_date)) {
            $cutoff_date = Date::excelToDateTimeObject($cutoff_date)->format('Y-m-d');
        }

        // Extraer mes y año del contrato
        $year = date('Y', strtotime($date_contract));
        $month = date('m', strtotime($date_contract));

        return new Contractor([
            'year_contract'     => $year,
            'month_contract'    => $month,
            'contract_number'   => $row['nro_contrato'],
            'date_contract'     => $date_contract,
            'code_secop'        => $row['codigo_secop_ii'],
            'class_contract'    => $row['clase_contrato'],
            'contractor'        => $row['contratista'],
            'firm_contractor'   => $row['firma_contratista'],
            'process_modality'  => $row['modalidad_proceso'],
            'object'            => $row['objeto'],
            'contract_term'     => $row['plazo_del_contrato'],
            'start_date'        => $start_date,
            'cutoff_date'       => $cutoff_date,
            'total_value'       => $row['valor_contrato'],
            'dependency'        => $row['secretaria_ejecutora'],
            'link_secop'        => $row['link'],
            'supervision'       => $row['supervisor'],
            'expense_class'     => $row['clase_de_gasto'],
        ]);
    }
}