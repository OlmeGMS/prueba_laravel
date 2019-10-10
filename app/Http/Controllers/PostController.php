<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Post;
use App\Helpers\JwtAuth;

class PostController extends Controller
{   

    public function __construct(){
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    public function pruebas(Request $request){
        return "Acción de prueba de post controller";
    }

    public function index(){
        $posts = Post::all()->load('category');
        return response()->json([
            'code'       => 200,
            'status'     => 'success',
            'posts'      => $posts
        ], 200);
    }

    public function show($id){
        $post = Post::find($id)->load('category');
        if (is_object($post)) {
            $data = [
                'code'       => 200,
                'status'     => 'success',
                'post'       => $post
            ];
        } else {
            $data = [
                'code'       => 404,
                'status'     => 'error',
                'message'    => 'El Post no existe'
            ];
        }

        return response()->json($data, $data['code']);
        
    }


    public function store(Request $request){

        //Recoger los datos por post
        $json = $request->getContent();
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            //Conseguir el usuario identificado
            $user = $this->getIdentity($request);

            //Validar los datos
            $validate = \Validator::make($params_array, [
                'title'       => 'required',
                'content'     => 'required',
                'category_id' => 'required',
                'image'       => 'required'
            ]);
            
            if ($validate->fails()) {
                $data = [
                    'code'       => 400,
                    'status'     => 'error',
                    'message'    => 'No se pudo guardar el post'
                ];
            } else {
                //Guardar el post
                $post = new Post();
                $post->user_id      = $user->sub;
                $post->category_id  = $params->category_id;
                $post->title        = $params->title;
                $post->content      = $params->content;
                $post->image        = $params->image;

                $post->save();

                $data = [
                    'code'       => 200,
                    'status'     => 'success',
                    'post'       => $post
                ];

            }
            
        } else {
            $data = [
                'code'       => 400,
                'status'     => 'error',
                'message'    => 'Envia los datos correctamente'
            ];
        }

        return response()->json($data, $data['code']);

    }

    public function update($id, Request $request){

        //Recoger los datos de post
        $json = $request->getContent();
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            // Validar los datos
            $validate = \Validator::make($params_array, [
                'title'         => 'required',
                'content'       => 'required',
                'category_id'   => 'required'
            ]);

            if ($validate->fails()) {
                $data = [
                    'code'       => 400,
                    'status'     => 'error',
                    'message'    => 'No has enviado el post'
                ];
            } else {
                
                //Eliminar lo que no queremos acrualizar 
                unset($params_array['id']);
                unset($params_array['user_id']);
                unset($params_array['created_at']);
                unset($params_array['user']);

                //Conseguir el usuario identificado
                $user = $this->getIdentity($request);

                //Conseguri el post
                $post = Post::where('id', $id)->where('user_id', $user->sub)->first();

                if (!empty($post) && is_object($post)) {
                    //Acutalizar post
                    $post = Post::where('id', $id)->updateOrCreate($params_array);

                    $data = [
                        'code'       => 200,
                        'status'     => 'success',
                        'post'       => $post
                    ];
                } else {
                    $data = [
                        'code'       => 400,
                        'status'     => 'error',
                        'message'    => 'No tienes eres el creador del post'
                    ];
                }
                

                
            }
            

        } else {
            $data = [
                'code'       => 400,
                'status'     => 'error',
                'message'    => 'No has enviado el post'
            ];
        }
        

        

        return response()->json($data, $data['code']);
    }


    public function destroy($id, Request $request){

        //Conseguir usuario identificado
        $user = $this->getIdentity($request);

        //Conseguri el post
        $post = Post::where('id', $id)->where('user_id', $user->sub)->first();
        if (!empty($post)) {
           //Borrar post
            $post->delete();
            $data = [
                'code'       => 200,
                'status'     => 'success',
                'post'       => $post
            ];
        } else {
            $data = [
                'code'       => 400,
                'status'     => 'error',
                'message'    => 'No se encontro el post que deseas eliminar'
            ];
        }
        

        

        return response()->json($data, $data['code']);
    } 
    
    
    private function getIdentity(Request $request){
        $jwtAuth  = new JwtAuth();
        $token    = $request->header('Authorization', null);
        $user     = $jwtAuth->checkToken($token, true);

        return $user;
    }
}
