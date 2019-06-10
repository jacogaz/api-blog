<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Post;
use App\Helpers\JwtAuth;

class PostController extends Controller
{   
    //Le decimos que estos metodos no pasen por la comprobacion del middleware
    public function __construct(){
        $this->middleware('api.auth', ['except' => ['index', 'show', 'getImage',
         'getPostsByCategory', 'getPostsByUser']]);
    }

    //Recojo todos los post de una categoria
    public function index(){
        $posts = Post::all()->load('category');

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'posts' => $posts
        ], 200);
    }

    //Consigo un solo post especifico 
    public function show($id){
        $post = Post::find($id)->load('category');

        if(is_object($post)){

            $data = [
                'code' => 200,
                'status' => 'success',
                'posts' => $post
            ];

        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El post no existe'
            ];
        }

        return response()->json($data, $data['code']);
    }

    //Guardar un post
    public function store(Request $req){
        $json = $req->input('json', null);

        $params = json_decode($json);
        
        $params_array = json_decode($json, true);
        /*var_dump($params_array);
        die();*/

        if(!empty($params_array)){
            $jwt = new JwtAuth();
            $token = $req->header('Authorization', null);//Recojo el token de la cabecera
            $user = $jwt->checkToken($token, true);//Compruebo que el token sea válido

            $validate = \Validator::make($params_array, [
                'title' => 'required',
                'content' => 'required',
                'image' => 'required'
            ]);

            if($validate->fails()){
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Faltan datos'
                ];
            } else {
                $post = new Post(); //instancio el objeto Post
                $post->user_id = $user->sub; //Le asigno el id, el content, etc
                $post->title = $params->title; 
                $post->content = $params->content;
                $post->image = $params->image;

                $post->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'post' => $post
                ];
                
            }
        }else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Envia los datos correctamente'
            ];
        }

        return response()->json($data, $data['code']);
    }

    //Actualizar un post
    public function update($id, Request $req){

        $json = $req->input('json', null);

        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if(!empty($params_array)){
            $validate = \Validator::make($params_array, [
                'title' => 'required',
                'content' => 'required'
            ]);

            if ($validate->fails()){
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Envia los datos correctamente'
                ];
            } else {
                unset($params_array['id']);
                unset($params_array['user_id']);
                unset($params_array['created_at']);
                unset($params_array['user']);

                $jwt = new JwtAuth();
                $token = $req->header('Authorization', null);
                $user = $jwt->checkToken($token, true);

                $post = Post::where('id', $id)->where('user_id', $user->sub)->update($params_array); //Actualizo los datos de un post donde el id  sea
                //el que me llega como parametro, y donde el usuario sea el creador del post

                if($post){
                    $data = array(
                        'code' => 200,
                        'status' => 'success',
                        'post' => $params_array
                    );
                } else {
                    $data = [
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'No eres el creador de este post'
                    ];
                }   
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Envia los datos correctamente'
            ];
        }
        return response()->json($data, $data['code']);
    }

    //Eliminar un post
    public function destroy($id, Request $req){

        $jwt = new JwtAuth();
        $token = $req->header('Authorization', null);
        $user = $jwt->checkToken($token, true);

        $post = Post::where('id', $id)->where('user_id', $user->sub)->first();//Borro el post que me llega como parametro siempre que el creador de ese post
        //sea el que está logueado

        if(!empty($post)){
            $post->delete();

            $data = [
                'code' => 200,
                'status' => 'success',
                'post' => $post
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El post no existe'
            ];
        }
        return response()->json($data, $data['code']);
    }

    //Subir una imagen
    public function upload(Request $req){
        $image = $req->file('file0');

        $validate = \Validator::make($req->all(), [
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif'//Valido que el fichero sea requerida, que sea una imagen y que tenga esos formatos
        ]);

        if (!$image || $validate->fails()){
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'Error al subir la imagen'
            ];
        } else {
            $image_name = time().$image->getClientOriginalName(); // Time() te devuelve el tiempo en formato unix 
            //consigo el nombre y se lo concateno para que sea unico
            \Storage::disk('images')->put($image_name, \File::get($image)); //almacenar la imagen
            $data = [
                'code' => 200,
                'status' => 'success',
                'image' => $image_name
            ];
        }
        return response()->json($data, $data['code']);
    }

    //Conseguir imagen
    public function getImage($filename){
        $exist = \Storage::disk('images')->exists($filename);//Compruebo que el archivo existe 

        if($exist){
            $file = \Storage::disk('images')->get($filename);//si el archivo existe 

            return new Response($file, 200);//Devuelvo el nombre del archivo
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'No se ha encontrado la imagen'
            ];

            return response()->json($data, $data['code']);
        }
    }

    //Consseguir los post de una categoria
    public function getPostsByCategory($id){
        $posts = Post::where('category_id', $id)->get();
        return response()->json([
            'status' => 'success',
            'posts' => $posts
        ], 200);
    }
    //Consseguir los post de un usuario
    public function getPostsByUser($id){
        $posts = Post::where('user_id', $id)->get();
        return response()->json([
            'status' => 'success',
            'posts' => $posts
        ], 200);
    }
}
