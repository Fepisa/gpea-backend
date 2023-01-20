<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LaudoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'descricao' => $this->descricao,
            'capacidade' => $this->capacidade,
            'massa' => $this->massa,
            'quantidade' => $this->quantidade,
            'tipo' => $this->tipo,
            'local_geral' => $this->local_geral,
            'local_especifico' => $this->local_especifico,
            'data' => $this->data->format('d/m/Y'),
            'validade' => $this->validade->format('d/m/Y'),
            'relatorio' => $this->relatorio,
            'responsavel_tecnico' => $this->responsavel_tecnico,
            'contratante' => $this->contratante,
            'contratada' => $this->contratada,
        ];
    }
}
