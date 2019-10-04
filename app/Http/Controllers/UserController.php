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
                $pwd = hash('sha256', $params->password);

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

        // Recibir los datos por post
        $json           = $request->getContent();
        $params         = json_decode($json);
        $params_array   = json_decode($json, true);

        // Validar datos
        $validate = \Validator::make($params_array, [

            'email'     => 'required|email', // Comprobar si el usuario existe
            'password'  => 'required'
        ]);

        if ($validate->fails()) {

            $signup = array(
                'status'  => 'error',
                'code'    => 404,
                'message' => 'El usuario no se ha podido loguear',
                'errors'  => $validate->errors()
            );

        
        } else {
            // Cifrar contraseña
            $pwd = hash('sha256', $params->password);

            // Devolver token o datos
            $signup = $jwtAuth->singup($params->email, $pwd);
            if(!empty($params->getToken)){
                $signup = $jwtAuth->singup($params->email, $pwd, true);
            }
        }

        return response()->json($signup, 200);
    }

    public function update(Request $request) {
        
        // Comprobar si el usuario esta identificado
        $token = $request->header('Authorization');

        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);

        // Recoger los datos
        $json = $request->getContent();
        $params_array = json_decode($json, true);

        if ($checkToken && !empty($params_array)) {

            // Sacar el usuario identifcado
            $user = $jwtAuth->checkToken($token, true);

            // Validar los datos
            $validate = \Validator::make($params_array, [
                'name'      => 'required|alpha',
                'surname'   => 'required|alpha',
                'email'     => 'required|email|unique:users,'.$user->sub, // Comprobar si el usuario existe

            ]);

            // Quitar los campos que no quiero actualizar
            unset($params_array['id']);
            unset($params_array['role']);
            unset($params_array['password']);
            unset($params_array['created_at']);
            unset($params_array['remember_token']);

            // Actualizar el usuario
            $userUpdate = User::where('id', $user->sub)->update($params_array);
            $data = array(
                'status'  => 'success',
                'code'    => 200,
                'user' => $userUpdate,
            );

        } else {
            $data = array(
                'status'  => 'error',
                'code'    => 404,
                'message' => 'El usuario no esta identificado correctamente',
            );
        }

        return response()->json($data, $data['code']);
    }

    public function upload(Request $request) {
        $data = array(
            'status'  => 'error',
            'code'    => 404,
            'message' => 'El usuario no esta identificado correctamente',
        );

        return response()->json($data, $data['code'])->header('Content-Type', 'text/plain');
    }


}
