<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Http\Resources\UserResource;
use Validator;
   
class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
    
        return $this->sendResponse(UserResource::collection($users), 'Usuários encontrados com sucesso.');
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

        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Erro de validação dos dados.', $validator->errors());
        }
   
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('GpeaApi')->plainTextToken;
        $success['name'] =  $user->name;
   
        return $this->sendResponse($success, 'Usuário registrado com sucesso.');  
    }
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
  
        if (is_null($user)) {
            return $this->sendError('Usuário não encontrado.');
        }
   
        return $this->sendResponse(new UserResource($user), 'Usuário encontrado com sucesso.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => 'required|email',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Erro de validação dos dados.', $validator->errors());
        }

        $user->name = $input['name'];
        $user->email = $input['email'];

        if ($input['password']) {
            if ($input['password'] == $input['c_password']) {
                $user->password = bcrypt($input['password']);
            } else {
                return $this->sendError('Erro de validação dos dados.', $validator->errors());
            }
        }

        $user->save();
   
        return $this->sendResponse(new UserResource($user), 'Usuário atualizado com sucesso.');
    }

}