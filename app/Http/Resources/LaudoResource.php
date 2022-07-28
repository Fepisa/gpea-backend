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
            'empresa' => $this->empresa,
            'local_geral' => $this->local_geral,
            'local_especifico' => $this->local_especifico,
            'data' => $this->data->format('d/m/Y'),
            'contrato' => $this->contrato,
            'crea' => $this->crea,
            'art' => $this->art,
            'capacidade_2' => $this->capacidade_2,
            'massa_2' => $this->massa_2,
            'quantidade_2' => $this->quantidade_2,
            'tipo_2' => $this->tipo_2,
            'empresa_2' => $this->empresa_2,
            'local_geral_2' => $this->local_geral_2,
            'local_especifico_2' => $this->local_especifico_2,
            'data_2' => $this->data_2->format('d/m/Y'),
            'contrato_2' => $this->contrato_2,
            'crea_2' => $this->crea_2,
            'art_2' => $this->art_2
        ];
    }
}
