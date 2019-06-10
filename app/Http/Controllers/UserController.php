<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

use App\Mail\SendMail;

class UserController extends Controller
{
    public function pruebas(Request $req){
        return 'pruebas user controller';
    }
    
    //funcion que realiza el registro en la base de datos
    
    public function register(Request $req){ 

        //Recoger los datos del usuario por post
  
        $json = $req->input('json', null);
  
        $params = json_decode($json); //objeto
        $params_array = json_decode($json, true);//array
  
  
        if (!empty($params) && !empty($params_array)) {
          //Limpiar datos
  
          $params_array = array_map('trim', $params_array);
  
          //Validar datos
  
          $validate = \Validator::make($params_array,[
            'email'       => 'required|email|unique:users',
            'password'    => 'required'
          ]);
  
          if ($validate->fails()) {
            //La validacion falla
  
            $data = array(
              "status"  => 'error',
              "code"    => 404,
              "message" => 'El usuario no se ha creado correctamente',
              "errors"  => $validate->errors()
            );
          }else{
            //La validacion ha sido correcta
  
            //Cifrar pass
  
            $pwd = hash('sha256',$params->password);
  
            //Crear usuario
  
            $user = new User();
            $user->email = $params_array['email'];
            $user->password = $pwd;
            $user->role = 'ROLE_USER';
  
            //Guardar Usuario
            $user->save();
  
            $data = array(
              "status"  => 'success',
              "code"    => 200,
              "message" => 'el usuario se ha creado correctamente',
              'user'    =>  $user
            );
          }
        }else{
          $data = array(
            "status"  => 'error',
            "code"    => 404,
            "message" => 'los datos enviados no son correctos'
          );
        }
  
        return response()->json($data, $data['code']);
      }

      //Comprueba que exista un usuario y  que la clave sea la correcta
      public function login(Request $req){

        $jwtAuth = new \JwtAuth();
  
        //Recibir por post los datos
  
        $json = $req->input('json', null);
        $params = json_decode($json); //objeto
        $params_array = json_decode($json, true);
  
        //Validar datos
  
        $validate = \Validator::make($params_array,[
          'email'       => 'required|email',
          'password'    => 'required'
        ]);
  
        if ($validate->fails()) {
          //La validacion falla
  
          $signup = array(
            "status"  => 'error',
            "code"    => 404,
            "message" => 'el usuario no se ha indentificado correctamente',
            "errors"  => $validate->errors()
          );
        }else{
          //Cifrar contraseña
          $pwd = hash('sha256',$params->password);
          //Devolver token o datos
          $signup = $jwtAuth->signUp($params->email, $pwd);
  
          if (!empty($params->gettoken)) {
            $signup = $jwtAuth->signUp($params->email, $pwd,true);
          }
  
        }
  
  
        return response()->json($signup, 200);
  
      }

      public function update(Request $req){

        //Comprobar si el usuario esta identificado
    
        $token = $req->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);
    
        //Recoger los datos por post
    
        $json = $req->input('json', null);
    
        $params = json_decode($json);
        $params_array = json_decode($json, true);
    
        if ($checkToken && !empty($params_array)) {
    
          //Sacar usuario identificado
    
          $user = $jwtAuth->checkToken($token, true);
    
          //Validar datos
          $validate = \Validator::make($params_array, [
            'email'       => 'required|email|unique:users'
          ]);

          if($validate->fails()) {
            $data = array(
              'code' => 400,
              'status' => 'error',
              'message' => 'El e-mail ya está en uso'
            );
          } else {

          //Quitar campos que no quiero actualizar
    
          unset($params_array['id']);
          unset($params_array['role']);
          unset($params_array['password']);
          unset($params_array['created_at']);
          unset($params_array['remember_token']);
    
          //Actualizar usuario
          

          $user_update = User::where('id', $user->sub)->update($params_array);
          
    
          //Devolver Array
    
          $data = array(
            'code' => 200,
            'status' => 'success',
            'user' => $user,
            'changes' => $params
          );
          }
    
        }else{
          $data = array(
            'code' => 400,
            'status' => 'error',
            'message' => 'El usuario no esta identificado'
          );
        }
    
        return response()->json($data, $data['code']);
      }

      public function upload(Request $req){ //no uso este método
        $data = array(
            'code' => 400,
            'status' => 'error',
            'message' => 'Error al subir imagen'
          );

          return response()->json($data, $data['code']);
      }

      //Conseguir la informacion de un usuario
      public function detail($id){
        $user = User::find($id);
  
        if (is_object($user)) {
          $data = array(
            'code' => 200,
            'status' => 'success',
            'user' => $user
          );
        }else{
          $data = array(
            'code' => 400,
            'status' => 'error',
            'message' => 'El usuario no existe'
          );
        }
  
        return response()->json($data, $data['code']);
      }
  
}
