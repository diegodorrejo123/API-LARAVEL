<?php

namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('api.auth')
        ->except([
            'show',
            'index'
        ]);
    }
    public function index()
    {
        $categories = Category::all();
        $res = [
            'code' => 200,
            'status' => 'success',
            'message' => '',
            'categories' => $categories
        ];
        return response()->json($res,$res['code']);
    }

    public function show($id)
    {
        $category = Category::find($id);
        if(is_object($category)){
            $res = [
                'code' => 200,
                'status' => 'success',
                'message' => '',
                'category' => $category
            ];
        }else{
            $res = [
                'code' => 400,
                'status' => 'error',
                'message' => 'La categoría no existe',
            ];
        }
        return response()->json($res,$res['code']);
    }

    public function store(Request $request)
    {
        $data = json_decode($request->input('json', null), true);

        if($data && !is_null($data)){
            $validate = Validator::make($data, [
                'name' => 'required'
            ]);
    
            if($validate->fails()){
                $res = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Formulario inválido',
                    'errors' => $validate->errors()
                ];
            }else{
                Category::create($data);
                $res = [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Se ha guardado la categoría',
                    'category' => $data
                ];
            }
        }else{
            $res = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No hemos recibido datos de la categoría'
            ];
        }
        return response()->json($res, $res['code']);
    }
    public function update($id, Request $request)
    {
        $data = json_decode($request->input('json', null), true);

        if(is_null($data)){
            $res = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No hemos recibido datos de la categoría'
            ];
        }else{
            $validate = Validator::make($data, [
                'name' => 'required'
            ]);
            if($validate->fails()){
                $res = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Formulario inválido',
                    'errors' => $validate->errors()
                ];
            }else{
                $category = Category::find($id);
                if(is_object($category)){
                    $update = $category->update($data);
                    if($update){
                        $res = [
                            'code' => 200,
                            'status' => 'success',
                            'message' => 'Se han guardado los cambios',
                            'category' => $data
                        ];
                    }else{
                        $res = [
                            'code' => 400,
                            'status' => 'error',
                            'message' => 'Ha ocurrido un error al actualizar los cambios',
                        ];
                    }
                }else{
                    $res = [
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'No existe la categoría',
                    ];
                }
            }
        }
        return response()->json($res, $res['code']);
    }

}