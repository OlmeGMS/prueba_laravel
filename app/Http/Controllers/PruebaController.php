<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Category;

class PruebaController extends Controller
{
    public function index() {
        
        $titulo = 'Animales';

        $animales = ['Perro', 'Gato', 'Tigre'];

        return view('pruebas.index', array(
            'titulo'    => $titulo,
            'animales'  => $animales
        ));
    }

    public function testOrm() {
        $posts = Post::all(); //saca todos los datos de post

        foreach ($posts as $post) {
            echo '<h1>'.$post->title.'</h1>';
            echo "<span>{$post->user->name}</span>";
            echo '<p>'.$post->content.'</p>';
            echo '<br>';
        }

        die();
    }
}
