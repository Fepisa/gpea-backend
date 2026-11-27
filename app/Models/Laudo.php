<?php
  
namespace App\Models;
  
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
  
class Laudo extends Model
{
    use HasFactory;

    protected $table = 'laudo';

    protected $fillable = ['codigo', 'descricao', 'capacidade', 'massa', 'quantidade', 'tipo', 'local_geral', 'local_especifico', 
    'data', 'validade', 'relatorio', 'responsavel_tecnico', 'contratante', 'contratada', 'observacao',
    'criador_id', 'responsavel_id'];

    protected $dates = ['data', 'validade'];

    /**
     * Usuário que criou o laudo.
     */
    public function criador()
    {
        return $this->belongsTo(User::class, 'criador_id');
    }

    /**
     * Usuário responsável pelo laudo.
     */
    public function responsavel()
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    /**
     * Admin altera qualquer laudo; demais usuários só os laudos dos quais são responsáveis.
     */
    public function podeSerAlteradoPor(?User $user): bool
    {
        if (is_null($user)) {
            return false;
        }

        if ($user->hasRole('Admin')) {
            return true;
        }

        return !is_null($this->responsavel_id) && (int) $this->responsavel_id === (int) $user->id;
    }
}
