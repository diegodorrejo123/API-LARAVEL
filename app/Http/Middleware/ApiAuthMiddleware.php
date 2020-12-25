<?php

namespace App\Http\Middleware;

use App\Helpers\JwtAuth;
use Closure;
use Illuminate\Http\Request;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $jwt = new JwtAuth();
        $token = $request->header('Authorization');
        $checkToken = $jwt->checkToken($token);

        if($checkToken){
            return $next($request);
        }else{
            $res = [
                'code' => 400,
                'status' => 'error',
                'message' => 'El usuario no esta identificado'
            ];
            return response()->json($res, $res['code']);
        }

        
    }
}
