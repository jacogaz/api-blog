<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Category;

class CategoryController extends Controller
{

    //Le decimos que estos metodos no pasen por la comprobacion del middleware
    public function __construct(){
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    //Conseguir todas las categorias 
    public function index(){
        $categories = Category::all();//cogemos todas las categorias
        return response()->json([
            'code' => 200,
            'status' => 'success',
            'categories' => $categories  
        ]);
    }

    //Enseñar una categoria especifica
    public function show($id){
        $category = Category::find($id);//Buscamos la categoria

        if (is_object($category)){
            $data = [
                'code' => 200,
                'status' => 'success',
                'categories' => $category  
            ];
        }else { 
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'La categoria no existe'  
            ];
        }

        return response()->json($data, $data['code']);
    }

    //Guardar una categoria
    public function store(Request $req){
        $json = $req->input('json', null);//cogemos los datos que nos vienen del front
        $params_array = json_decode($json, true);//Convertimos los datos a un array

        if(!empty($params_array)){
            $validate = \Validator::make($params_array,[ //Validamos los datos que nos llegan desde el front
                'name' =>   'required'
            ]);
    
            if ($validate->fails()){
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha podido guardar la categoria'  
                ];
            }else {
                $category = new Category();//Instanciamos el objeto Categoria
                $category->name = $params_array['name'];//Cogemos el nombre del array para guardarlo en la bbdd
                $category->save();
    
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'message' => $category
                ];
            }
    
        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ha enviado ninguna categoria'  
            ];
        }

        
        return response()->json($data, $data['code']);
    }

    //Actualizar una categoria
    public function update($id, Request $req){
        $json = $req->input('json', null);
        $params_array = json_decode($json, true);

        if(!empty($params_array)){
            $validate = \Validator::make($params_array,[
                'name' => 'required'
            ]);

            unset($params_array['id']);//Quitamos los datos que no queremos actualizar
            unset($params_array['created_at']);

            $category = Category::where('id', $id)->update($params_array);//Actualizamos la categoria que nos llega 

            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => $params_array
            ];

        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ha enviado ninguna categoria'  
            ];
        }

        return response()->json($data, $data['code']);
    }
}
