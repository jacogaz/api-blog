<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';

    // Relacion muchos a uno con la tabla usuarios
    public function user(){
        return $this->belongsTo('App/User', 'user_id');
    }

    // Relacion muchos a uno con la tabla usuarios
    public function category(){
        return $this->belongsTo('App\Category', 'category_id');
    }
}
