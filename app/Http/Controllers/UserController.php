<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\User;

class UserController extends Controller
{
    public function pruebas(Request $request){
        return "Acción de prueba de user controller";
    }

    public function registro(Request $request){

        // Recoger los datos del usuario por post
        $json = $request->getContent();

        $params       = json_decode($json);
        $params_array = json_decode($json, true);
        //var_dump($params_array['name']); 

        if (!empty($params) && !empty($params_array)) {
            //Limpiar datos
            $params_array = array_map('trim', $params_array);

            // Validar datos
            $validate = \Validator::make($params_array, [
                'name'      => 'required|alpha',
                'surname'   => 'required|alpha',
                'email'     => 'required|email|unique:users', // Comprobar si el usuario existe
                'password'  => 'required'
            ]);

            if ($validate->fails()) {

                $data = array(
                    'status'  => 'error',
                    'code'    => 404,
                    'message' => 'El usuario no se ha creado correctamente',
                    'errors'  => $validate->errors()
                );

            
            } else {

                // Cifrar la contraseña
                $pwd = password_hash($params->password, PASSWORD_BCRYPT, ['cost' => 4]);

                // Crear el usuario
                $user = new User();

                $user->name      = $params_array['name'];
                $user->surname   = $params_array['surname'];
                $user->email     = $params_array['email'];
                $user->password  = $pwd;
                $user->role      = 'ROLE_USER';


                // Regresar Json
                $user->save();

                $data = array(
                    'status'  => 'success',
                    'code'    => 200,
                    'message' => 'El usuario se ha creado correctamente',
                    'user'    => $user
                );
            }
        } else {
            $data = array(
                'status'  => 'error',
                'code'    => 400,
                'message' => 'No se ha pasado los datos'
            );
        }
        

        

       

        

        return response()->json($data, $data['code']);
    }

    public function login(Request $request){

        $jwtAuth = new \JwtAuth();

       echo $jwtAuth->singup();

        return "Login del usuario";
    }


}
