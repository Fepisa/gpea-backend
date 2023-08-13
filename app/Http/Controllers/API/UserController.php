<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
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
        $user->assignRole($input['role']);
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

        $userResource = new UserResource($user);
        $userResource->roles = $user->getRoleNames();
   
        return $this->sendResponse($userResource, 'Usuário encontrado com sucesso.');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function currentUser()
    {
        $user = Auth::user(); 
  
        if (is_null($user)) {
            return $this->sendError('Usuário não encontrado.');
        }

        $userResource = new UserResource($user);
        $userResource->roles = $user->getRoleNames();
   
        return $this->sendResponse($userResource, 'Usuário encontrado com sucesso.');
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
        $user->local = $input['local'];
        $user->empresa = $input['empresa'];

        if ($input['password']) {
            if ($input['password'] == $input['c_password']) {
                $user->password = bcrypt($input['password']);
            } else {
                return $this->sendError('Erro de validação dos dados.', $validator->errors());
            }
        }

        $user->save();

        $userResource = new UserResource($user);
        $userResource->roles = $user->getRoleNames();
   
        return $this->sendResponse($userResource, 'Usuário atualizado com sucesso.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateCurrentUser(Request $request)
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => 'required|email',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Erro de validação dos dados.', $validator->errors());
        }

        $user = Auth::user(); 
  
        if (is_null($user)) {
            return $this->sendError('Usuário não encontrado.');
        }

        $user->name = $input['name'];
        $user->email = $input['email'];
        $user->local = $input['local'];
        $user->empresa = $input['empresa'];

        if ($input['password']) {
            if ($input['password'] == $input['c_password']) {
                $user->password = bcrypt($input['password']);
            } else {
                return $this->sendError('Erro de validação dos dados.', $validator->errors());
            }
        }

        $user->save();

        $userResource = new UserResource($user);
        $userResource->roles = $user->getRoleNames();
   
        return $this->sendResponse($userResource, 'Usuário atualizado com sucesso.');
    }

    /**
     * Update role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateRole(Request $request)
    {
        $input = $request->all();
        $user = User::find($input['id']);
  
        if (is_null($user)) {
            return $this->sendError('Usuário não encontrado.');
        }

        $user->removeRole($input["old_role"]);
        $user->assignRole($input['new_role']);

        $user->save();

        $userResource = new UserResource($user);
        $userResource->roles = $user->getRoleNames();
  
        return $this->sendResponse($userResource, 'Usuário atualizado com sucesso.');
    }

}