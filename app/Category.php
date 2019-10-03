<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categies';

    // Relación de uno a muchos
    public function posts(){
        return $this->hasMany('App\Post');
    }
}
