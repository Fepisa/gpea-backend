<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Image;
use App\Http\Resources\ImageResource;
use Validator;
   
class ImageController extends BaseController
{

    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'codigo' => 'required',
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Erro ao realizar upload de imagem.', $validator->errors());
        }

        $image_path = $request->file('image')->store('image', 'public');

        $image = Image::create([
            'codigo' => $input['codigo'],
            'image' => $image_path
        ]);

        return $this->sendResponse(new ImageResource($image), 'Imagem enviada com sucesso.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $codigo
     * @return \Illuminate\Http\Response
     */
    public function show($codigo)
    {
        $images = Image::where('codigo', $codigo)->get();

        if (is_null($images)) {
            return $this->sendError('Imagem não encontrada.');
        }

        for ($i = 0; $i < sizeof($images); $i++) {
            $images[$i]->image = asset('storage/'.$images[$i]->image);
        }
   
        return $this->sendResponse(ImageResource::collection($images), 'Imagens encontradas com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $image = Image::find($id);
        $image_path = public_path().'/storage/'.$image->image;
        unlink($image_path);

        $image->delete();
   
        return $this->sendResponse([], 'Imagem excluida com sucesso.');
    }

}