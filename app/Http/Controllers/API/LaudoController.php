<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Laudo;
use Validator;
use App\Http\Resources\LaudoResource;
use Carbon\Carbon;
use App\Exports\ExportLaudo;
use App\Imports\ImportLaudo;
use Maatwebsite\Excel\Facades\Excel;
   
class LaudoController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $laudos = Laudo::with('responsavel')->get();
    
        return $this->sendResponse(LaudoResource::collection($laudos), 'Laudos técnicos encontrados com sucesso.');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        // Criador é sempre o usuário logado.
        // Responsável: Admin escolhe (ou fica o próprio, se nenhum for selecionado); demais usuários são sempre o próprio.
        $input['criador_id'] = auth()->id();
        if (!$this->isAdmin() || empty($input['responsavel_id'])) {
            $input['responsavel_id'] = auth()->id();
        }
   
        $validator = Validator::make($input, [
            'responsavel_id' => 'required|exists:users,id',
            'codigo' => 'required',
            'descricao' => 'required',
            'capacidade' => 'required',
            'massa' => 'required',
            'quantidade' => 'required',
            'tipo' => 'required',
            'local_geral' => 'required',
            'local_especifico' => 'required',
            'data' => 'required',
            'validade' => 'required',
            'relatorio' => 'required',
            'responsavel_tecnico' => 'required',
            'contratante' => 'required',
            'contratada' => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Erro de validação dos dados.', $validator->errors());
        }

        $laudo = Laudo::where('codigo', $input['codigo'])->get();
  
        if ($laudo->isEmpty()) {
            $input['data'] = Carbon::createFromFormat('d/m/Y', $input['data']);
            $input['validade'] = Carbon::createFromFormat('d/m/Y', $input['validade']);
       
            $laudo = Laudo::create($input);
       
            return $this->sendResponse(new LaudoResource($laudo), 'Laudo técnico registrado com sucesso.');
        }
        return $this->sendError('Laudo já registrado!');
    } 
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $laudo = Laudo::find($id);
  
        if (is_null($laudo)) {
            return $this->sendError('Laudo técnico não encontrado.');
        }
   
        return $this->sendResponse(new LaudoResource($laudo), 'Laudo técnico encontrado com sucesso.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Laudo $laudo)
    {
        $input = $request->all();

        if (!$this->podeAlterar($laudo)) {
            return $this->sendError('Você não tem permissão para alterar laudos dos quais não é responsável.', [], 403);
        }

        // O criador do laudo nunca é alterado
        unset($input['criador_id']);

        if (!$this->isAdmin()) {
            // Usuário não admin não altera código nem responsável
            $input['codigo'] = $laudo->codigo;
            $input['responsavel_id'] = $laudo->responsavel_id;
        } elseif (empty($input['responsavel_id'])) {
            // Admin sem responsável informado: mantém o atual
            $input['responsavel_id'] = $laudo->responsavel_id;
        }
   
        $validator = Validator::make($input, [
            'responsavel_id' => 'nullable|exists:users,id',
            'codigo' => 'required',
            'descricao' => 'required',
            'capacidade' => 'required',
            'massa' => 'required',
            'quantidade' => 'required',
            'tipo' => 'required',
            'local_geral' => 'required',
            'local_especifico' => 'required',
            'data' => 'required',
            'validade' => 'required',
            'relatorio' => 'required',
            'responsavel_tecnico' => 'required',
            'contratante' => 'required',
            'contratada' => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Erro de validação dos dados.', $validator->errors());
        }

        $codigo = $input['codigo'];

        $oldLaudo = Laudo::where('codigo', $codigo)->get();
  
        if ($oldLaudo->isEmpty() || $codigo === $laudo->codigo) {
            $input['data'] = Carbon::createFromFormat('d/m/Y', $input['data']);
            $input['validade'] = Carbon::createFromFormat('d/m/Y', $input['validade']);
    
            $laudo->codigo = $input['codigo'];
            $laudo->descricao = $input['descricao'];
            $laudo->capacidade = $input['capacidade'];
            $laudo->massa = $input['massa'];
            $laudo->quantidade = $input['quantidade'];
            $laudo->tipo = $input['tipo'];
            $laudo->local_geral = $input['local_geral'];
            $laudo->local_especifico = $input['local_especifico'];
            $laudo->data = $input['data'];
            $laudo->validade = $input['validade'];
            $laudo->relatorio = $input['relatorio'];
            $laudo->responsavel_tecnico = $input['responsavel_tecnico'];
            $laudo->contratante = $input['contratante'];
            $laudo->contratada = $input['contratada'];
            $laudo->observacao = $input['observacao'] ?? $laudo->observacao;
            $laudo->responsavel_id = $input['responsavel_id'];
            $laudo->save();
       
            return $this->sendResponse(new LaudoResource($laudo), 'Laudo técnico atualizado com sucesso.');    
        }
        return $this->sendError('Laudo já registrado!');
    }

    /**
     * Update field observacao in laudo.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateObservacao(Request $request)
    {
        $input = $request->all();

        $id = $input['id'];

        $laudo = Laudo::find($id);

        if (is_null($laudo)) {
            return $this->sendError('Laudo técnico não encontrado.');
        }

        if (!$this->podeAlterar($laudo)) {
            return $this->sendError('Você não tem permissão para alterar laudos dos quais não é responsável.', [], 403);
        }
  
        $laudo->observacao = $input['observacao'];
        $laudo->save();
       
        return $this->sendResponse(new LaudoResource($laudo), 'Observação de laudo técnico atualizada com sucesso.');    
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $local_geral
     * @param  string  $contratante
     * @return \Illuminate\Http\Response
     */
    public function getByLocalAndContratante($local_geral, $contratante)
    {
        $laudos = Laudo::where('local_geral', $local_geral)->where('contratante', $contratante)->get();

        if ($laudos->isEmpty()) {
            return $this->sendError('Laudos técnicos não encontrados.');
        }
    
        return $this->sendResponse(LaudoResource::collection($laudos), 'Laudos técnicos encontrados com sucesso.');
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $codigo
     * @return \Illuminate\Http\Response
     */
    public function getByCodigo($codigo)
    {
        $laudo = Laudo::where('codigo', $codigo)->get();
 
        if ($laudo->isEmpty()) {
            return $this->sendError('Laudo técnico não encontrado.');
        }
   
        return $this->sendResponse(new LaudoResource($laudo[0]), 'Laudo técnico encontrado com sucesso.');
    }

    /**
     * Import CSV File.
     *
     * @return \Illuminate\Http\Response
     */
    public function importCsv()
    {
        Excel::import(new ImportLaudo, request()->file('file'));   
        return $this->sendResponse([], 'Laudo(s) técnico(s) importado(s) com sucesso.');
    }

    /**
     * Export CSV File.
     * 
     * @return \Illuminate\Http\Response
     */
    public function exportCsv()
    {
        return Excel::download(new ExportLaudo, 'laudos.csv', \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Laudo $laudo)
    {
        if (!$this->podeAlterar($laudo)) {
            return $this->sendError('Você não tem permissão para excluir laudos dos quais não é responsável.', [], 403);
        }

        $laudo->delete();
   
        return $this->sendResponse([], 'Laudo excluido com sucesso.');
    }

    /**
     * Verifica se o usuário autenticado é administrador.
     */
    private function isAdmin(): bool
    {
        $user = auth()->user();
        return $user !== null && $user->hasRole('Admin');
    }

    /**
     * Admin altera qualquer laudo; demais usuários só os laudos dos quais são responsáveis.
     */
    private function podeAlterar(Laudo $laudo): bool
    {
        return $laudo->podeSerAlteradoPor(auth()->user());
    }
}
