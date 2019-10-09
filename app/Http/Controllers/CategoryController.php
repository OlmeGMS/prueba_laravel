<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Category;

class CategoryController extends Controller
{

    public function __construct(){
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    public function pruebas(Request $request){
        return "Acción de prueba de category controller";
    }

    public function index(){

        $category = Category::all();
        return response()->json([
            'code'       => 200,
            'status'     => 'success',
            'categories' => $category
        ]);

    }

    public function show($id){

        $category = Category::find($id);
        if (is_object($category)) {
            $data = [
                'code'       => 200,
                'status'     => 'success',
                'category' => $category
            ];
        } else {
            $data = [
                'code'       => 404,
                'status'     => 'error',
                'message' => 'La categoria no existe'
            ];
        }

        return response()->json($data, $data['code']);
        
    }


    public function store(Request $request){
        
        //Recoger los dator por post
        $json = $request->getContent();
        $params_array = json_decode($json, true);
        if (isset($params_array)) {
            //Validar los datos
            $validate = \Validator::make($params_array,[
                'name' => 'required'
            ]);
            if ($validate->fails()) {
                $data = [
                    'code'       => 400,
                    'status'     => 'error',
                    'message' => 'No se pudo guardar la categoria'
                ];
            } else {
                //Guardar la categoria
                $category = new Category();
                $category->name = $params_array['name'];
                $category->save();

                $data = [
                    'code'       => 200,
                    'status'     => 'success',
                    'category'   => $category
                ];
            }
        } else {
            $data = [
                'code'       => 400,
                'status'     => 'error',
                'message' => 'No has enviado ninguna categoria'
            ];
        }
        

        

        return response()->json($data, $data['code']);
        
    }

    public function update($id, Request $request){

        //Recoger los datos por post
        $json = $request->getContent();
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {

            //Validar los datos
            $validate = \Validator::make($params_array, [
                'name' => 'required'
            ]);

            //Quitar lo que no quiero actualizar
            unset($params_array['id']);
            unset($params_array['created_at']);

            //Actualizar el registro
            $category = Category::where('id', $id)->update($params_array);
            $data = [
                'code'       => 200,
                'status'     => 'success',
                'category'   => $params_array
            ];

        } else {
            $data = [
                'code'       => 400,
                'status'     => 'error',
                'message' => 'No has enviado ninguna categoria'
            ];
        }
        

        return response()->json($data, $data['code']);

    }
}
