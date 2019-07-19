<?php
namespace App\Views;

class JsonView{
    public static function render($data){
        exit(json_encode($data));
    }
}
