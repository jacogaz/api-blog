<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facade\DB;

use App\User;

class JwtAuth{

  public $key;

  public function __construct(){
    $this->key = 'claveEphmJacobo140719';
  }

  public function signUp($email, $password, $getToken = null){
    //Buscar si existe el usuario con sus credenciales

    $user = User::where([
          'email'      => $email,
          'password'  => $password
    ])->first();

    //Comprobar si son correctas(objeto)

    $signup = false;

    if (is_object($user)) {
      $signup = true;
    }

    //Generar el token con los datos del usuario identificado

    if ($signup) {
      $token = array(
        'sub' => $user->id,
        'email' => $user->email,
        'role' => $user->role,
        'iat' =>  time(),
        'exp' =>  time() + (7*24*60*60)
       );

       $jwt = JWT::encode($token, $this->key, 'HS256');
       $decoded = JWT::decode($jwt, $this->key, ['HS256']);

       if (is_null($getToken)) {
         $data = $jwt;
       }else{
         $data = $decoded;
       }
    }else{
      $data = array(
          'status'  => 'error',
          'message' => 'el login ha fallado'
      );
    }

    //Devolver los datos decodificados o el token, en funcion de un parametro

    return $data;

  }

  public function checkToken($jwt, $getIdentity = false){

    $auth = false;

    try {
      $decoded = JWT::decode($jwt, $this->key, ['HS256']);
    } catch (\UnexpectedValueException $e) {
      $auth = false;
    } catch(\DomainException $e){
      $auth = false;
    }

    if (!empty($decoded) && is_object($decoded) && isset($decoded->sub)) {
      $auth = true;
    }else{
      $auth = false;
    }

    if ($getIdentity) {
      return $decoded;
    }

    return $auth;

  }
  
 
}