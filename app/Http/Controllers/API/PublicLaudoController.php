<?php
   
namespace App\Http\Controllers\API;
   
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Laudo;
use App\Http\Resources\LaudoResource;
   
class PublicLaudoController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $laudos = Laudo::all();
    
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

}