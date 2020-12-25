<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\Models\User;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    public function register(Request $request)
    {
        if (empty($request->all())) {
            $res = [
                'code' => 404,
                'status' => 'error',
                'message' => 'no se han introducido datos',
            ];
        } else {
            $res = array_map('trim', json_decode($request->input('json', null), true));
            $validate = Validator::make(
                $res,
                [
                    'name' => 'required|alpha',
                    'surname' => 'required|alpha',
                    'email' => 'required|email|unique:users',
                    'password' => 'required'
                ]
            );
            if ($validate->fails()) {
                $res = [
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'el usuario no se ha creado',
                    'errors' => $validate->errors()
                ];
            } else {
                $user = new User();
                $user->name = $res['name'];
                $user->surname = $res['surname'];
                $user->email = $res['email'];
                $user->password = hash('sha256', $res['password']);
                $user->role = '$res[role]';
                if ($user->save()) {
                    $res = [
                        'code' => 200,
                        'status' => 'success',
                        'message' => 'Se ha registrado exitosamente',
                        'user' => $user
                    ];
                } else {
                    $res = [
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'Ha ocurrido un error, intentelo nuevamente mas tarde',
                        'user' => $user
                    ];
                }
            }
        }
        return response()->json($res, $res['code']);
    }
    public function login(Request $request)
    {
        $jwt = new JwtAuth();

        $data = json_decode($request->input('json', null), true);

        $validate = Validator::make($data,[
                'email' => 'required|email',
                'password' => 'required'
            ]
        );
        if ($validate->fails()) {
            $res = [
                'code' => 404,
                'status' => 'error',
                'message' => 'Formulario invalido',
                'errors' => $validate->errors()
            ];
        } else {
            $email = $data['email'];
            $password = hash('sha256', $data['password']);
            $res = $jwt->signUp($email, $password);
            if (!empty($data->getToken)) {
                $res = $jwt->signUp($email, $password, true);
            }
        };
        return response()->json($res, $res['code']);
    }
    public function update(Request $request)
    {
        $jwt = new JwtAuth();
        $token = $request->header('Authorization');
        $user = $jwt->checkToken($token, true);
        $data = $request->input('json', null);
        $data = json_decode($data, true);
        $validate = Validator::make($data, [
            'name' => 'required|alpha',
            'surname' => 'required|alpha',
            'email' => 'required|email|unique:users'
        ]);
        if(!$validate->fails()){
            $userUpdate = User::where('id', $user->sub)->update($data);
            if ($userUpdate) {
                $res = [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Los cambios se han guardado correctamente',
                    'updated fields' => $data
                ];
            } else {
                $res = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Ha ocurrido un error, intentelo mas tarde.'
                ];
            }
        }else{
            $res = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error en el formulario',
                'errors' => $validate->errors(),
            ];
        }
        return response()->json($res, $res['code']);
    }
    public function upload(Request $request)
    {
        $fileName = time().$request->file0->getClientOriginalName();
        

        $validate = Validator::make($request->all(), [
            'file0' => 'image'
        ]);
        if($validate->fails()){
            $res = [
                'code' => 400,
                'status' => 'error',
                'message' => $validate->errors(),
                
            ];
        }else{
            $imageSaved = $request->file0->storeAs('users', $fileName, 'public');
            if(Storage::disk('public')->exists('users/'.$fileName)){
                $res = [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'La imagen se ha guardado',
                    'img' => $imageSaved
                ];
            }else{
                $res = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Ha ocurrido un error al guardar la imagen',
                ];
            }
            
        }
        return response()->json($res, $res['code']);
    }
    public function getImage($imageName)
    {
        $isset = Storage::disk('public')->exists('users/'.$imageName);
        if($isset){
            $res = Storage::url('users/' . $imageName);
            
        }else{
            $res = [
                'code' => 400,
                'status' => 'error',
                'message' => 'La imagen no existe',
        ];
        }
        return $res;
    }
    public function details($id)
    {
        $user = User::find($id);
        if(is_object($user)){
            $res = [
                'code' => 200,
                'status' => 'success',
                'message' => 'Usuario encontrado',
                'user' => $user
            ];
        }else{
            $res = [
                'code' => 400,
                'status' => 'error',
                'message' => 'El usuario no existe',
            ];
        }
        return response()->json($res, $res['code']);
    }
}
