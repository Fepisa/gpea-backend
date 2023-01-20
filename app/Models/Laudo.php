<?php
  
namespace App\Models;
  
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
  
class Laudo extends Model
{
    use HasFactory;

    protected $table = 'laudo';

    protected $fillable = ['codigo', 'descricao', 'capacidade', 'massa', 'quantidade', 'tipo', 'local_geral', 'local_especifico', 
    'data', 'validade', 'relatorio', 'responsavel_tecnico', 'contratante', 'contratada', 'observacao'];

    protected $dates = ['data', 'validade'];
}
