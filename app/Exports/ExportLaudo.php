<?php

namespace App\Exports;

use App\Models\Laudo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportLaudo implements FromCollection, WithHeadings, WithStrictNullComparison, WithColumnFormatting, WithMapping
{
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Todos os usuários (qualquer perfil) exportam os laudos de todas as unidades e empresas
        return Laudo::all(
            'codigo', 'descricao', 'capacidade', 'massa', 'quantidade', 'tipo', 'local_geral', 'local_especifico', 
            'data', 'validade', 'relatorio', 'responsavel_tecnico', 'contratante', 'contratada', 'observacao'
        );
    }

    public function headings(): array
    {
        return [
            'Código',
            'Descrição',
            'Capacidade',
            'Massa',
            'Quantidade',
            'Tipo',
            'Local Geral',
            'Local Específico',
            'Data',
            'Validade',
            'Relatório',
            'Responsável Técnico',
            'Contratante',
            'Contratada',
            'Observação',
        ];
    }

    public function map($laudo): array {
        return [
            strval($laudo->codigo),
            $laudo->descricao,
            $laudo->capacidade,
            $laudo->massa,
            $laudo->quantidade,
            $laudo->tipo,
            $laudo->local_geral,
            $laudo->local_especifico,
            Date::dateTimeToExcel($laudo->data),
            Date::dateTimeToExcel($laudo->validade),
            $laudo->relatorio,
            $laudo->responsavel_tecnico,
            $laudo->contratante,
            $laudo->contratada,
            $laudo->observacao
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'J' => NumberFormat::FORMAT_DATE_DDMMYYYY
        ];
    }

}
