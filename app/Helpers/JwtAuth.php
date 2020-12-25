<?php

namespace App\Helpers;

use App\Models\User;
use DomainException;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use UnexpectedValueException;

class JwtAuth
{
    public $key;

    public function __construct()
    {
        $this->key = 'clave_secreta';
    }

    public function signUp($email, $password, $getToken = null)
    {
        $user = User::where([
            'email' => $email,
            'password' => $password
        ])->first();

        $signup = false;

        if (is_object($user)) {
            $signup = true;
        }

        if ($signup) {
            $token = [
                'sub'       => $user->id,
                'name'      => $user->name,
                'surname'   => $user->surname,
                'email'     => $user->email,
                'iat'       => time(),
                'exp'       => time() + (60 * 60)
            ];
            $jwt = JWT::encode($token, $this->key, 'HS256');
            $tokenDecoded = JWT::decode($jwt, $this->key, ['HS256']);

            if (is_null($getToken)) {
                $res = [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Login completado correctamente',
                    'token' => $jwt
                ];
            } else {
                $res = [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Login completado correctamente',
                    'token' => $tokenDecoded
                ];
            }
        } else {
            $res = [
                'code' => 400,
                'status' => 'error',
                'message' => 'La contraseña o el email son incorrectos'
            ];
        }
        return $res;
    }

    public function checkToken($jwt, $getIdentity = false)
    {
        $auth = false;
        try {
            $jwt = str_replace('"','',$jwt);
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        } catch (UnexpectedValueException $ex) {
            $auth = false;
        } catch (DomainException $ex) {
            $auth = false;
        }
        if (!empty($decoded) && is_object($decoded) && isset($decoded->sub)) {
            $auth = true;
        } else {
            $auth = false;
        }
        if($getIdentity){
            return $decoded;
        }
        return $auth;
    }
}
