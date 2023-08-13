<?php

namespace App\Imports;

use App\Models\Laudo;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportLaudo implements ToModel, WithStartRow, WithCustomCsvSettings
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $laudo = Laudo::where('codigo', $row[0])->get();
 
        if ($laudo->isEmpty()) {
            return new Laudo([
                'codigo' => $row[0],
                'descricao' => $row[1],
                'capacidade' => $row[2],
                'massa' => $row[3],
                'quantidade' => $row[4],
                'tipo' => $row[5],
                'local_geral' => $row[6],
                'local_especifico' => $row[7],
                'data' => Carbon::createFromFormat('d/m/Y', $row[8]),
                'validade' => Carbon::createFromFormat('d/m/Y', $row[9]),
                'relatorio' => $row[10],
                'responsavel_tecnico' => $row[11],
                'contratante' => $row[12],
                'contratada' => $row[13],
                'observacao' => $row[14],
            ]);    
        }
        return null;
    }

    public function startRow(): int {
        return 2;
    }

    public function getCsvSettings(): array {
      return [
          'input_encoding' => 'UTF-8'
      ];
    }
}
