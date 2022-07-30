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

}