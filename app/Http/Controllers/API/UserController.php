<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Validator;
   
class UserController extends BaseController
{
    /**
     * Perfis de acesso disponíveis.
     */
    private const ROLES = ['Admin', 'Viewer'];

    /**
     * Mensagens de validação em português.
     */
    private const MENSAGENS = [
        'name.required' => 'O nome é obrigatório.',
        'email.required' => 'O e-mail é obrigatório.',
        'email.email' => 'Informe um e-mail válido.',
        'email.unique' => 'Já existe um usuário cadastrado com este e-mail.',
        'password.required' => 'A senha é obrigatória.',
        'c_password.required' => 'A confirmação de senha é obrigatória.',
        'c_password.required_with' => 'Confirme a nova senha.',
        'c_password.same' => 'A confirmação de senha não confere com a senha.',
        'empresa.required' => 'A empresa é obrigatória.',
        'local.required' => 'O local é obrigatório.',
        'role.required' => 'A permissão de acesso é obrigatória.',
        'role.in' => 'Permissão de acesso inválida.',
        'new_role.required' => 'A permissão de acesso é obrigatória.',
        'new_role.in' => 'Permissão de acesso inválida.',
        'id.required' => 'Usuário não informado.',
    ];

    /**
     * Retorna erro de validação com a primeira mensagem como mensagem principal.
     */
    private function erroValidacao($validator)
    {
        return $this->sendError($validator->errors()->first(), $validator->errors(), 422);
    }

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

        $input['role'] = $input['role'] ?? 'Viewer';

        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'empresa' => 'required',
            'local' => 'required',
            'role' => ['required', Rule::in(self::ROLES)],
        ], self::MENSAGENS);
   
        if($validator->fails()){
            return $this->erroValidacao($validator);
        }
   
        $input['password'] = bcrypt($input['password']);

        // Cria o usuário e atribui o perfil na mesma transação, evitando usuário sem perfil
        $user = DB::transaction(function () use ($input) {
            $user = User::create($input);
            $user->assignRole($input['role']);
            return $user;
        });

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
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'empresa' => 'required',
            'local' => 'required',
            'c_password' => 'required_with:password|same:password',
        ], self::MENSAGENS);
   
        if($validator->fails()){
            return $this->erroValidacao($validator);
        }

        $user->name = $input['name'];
        $user->email = $input['email'];
        $user->local = $input['local'];
        $user->empresa = $input['empresa'];

        if (!empty($input['password'])) {
            $user->password = bcrypt($input['password']);
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
   
        $user = Auth::user(); 
  
        if (is_null($user)) {
            return $this->sendError('Usuário não encontrado.');
        }

        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'empresa' => 'required',
            'local' => 'required',
            'c_password' => 'required_with:password|same:password',
        ], self::MENSAGENS);
   
        if($validator->fails()){
            return $this->erroValidacao($validator);
        }

        $user->name = $input['name'];
        $user->email = $input['email'];
        $user->local = $input['local'];
        $user->empresa = $input['empresa'];

        if (!empty($input['password'])) {
            $user->password = bcrypt($input['password']);
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

        $validator = Validator::make($input, [
            'id' => 'required',
            'new_role' => ['required', Rule::in(self::ROLES)],
        ], self::MENSAGENS);

        if($validator->fails()){
            return $this->erroValidacao($validator);
        }

        $user = User::find($input['id']);
  
        if (is_null($user)) {
            return $this->sendError('Usuário não encontrado.');
        }

        // Substitui o(s) perfil(is) atual(is) pelo novo; funciona também para usuário sem perfil
        $user->syncRoles([$input['new_role']]);

        $userResource = new UserResource($user);
        $userResource->roles = $user->getRoleNames();
  
        return $this->sendResponse($userResource, 'Usuário atualizado com sucesso.');
    }

    /**
     * Remove the specified user. Rota protegida por manage-users (somente Admin).
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if (Auth::id() === $user->id) {
            return $this->sendError('Você não pode excluir o seu próprio usuário.', [], 403);
        }

        DB::transaction(function () use ($user) {
            // Encerra as sessões ativas e remove os vínculos de perfil antes de excluir
            $user->tokens()->delete();
            $user->syncRoles([]);
            $user->delete();
        });

        return $this->sendResponse([], 'Usuário excluído com sucesso.');
    }

}
