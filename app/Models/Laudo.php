<?php
  
namespace App\Models;
  
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
  
class Laudo extends Model
{
    use HasFactory;

    protected $table = 'laudo';

    protected $fillable = ['codigo', 'descricao', 'capacidade', 'massa', 'quantidade', 'tipo', 'empresa', 
    'local_geral', 'local_especifico', 'data', 'contrato', 'crea', 'art', 'capacidade_2', 'massa_2', 'quantidade_2', 
    'tipo_2', 'empresa_2', 'local_geral_2', 'local_especifico_2', 'data_2', 'contrato_2', 'crea_2', 'art_2'];

    protected $dates = ['data', 'data_2'];
}
