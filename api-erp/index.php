<?php

require_once 'vendor/autoload.php';

$app = new Slim\Slim();

$app->get("/hola/:nombre",function($nombre)use ($app){
    echo "Holax ". $nombre;
    var_dump($app->request->params());
});

function prueba1(){
    echo "prueba 1";
}



$app->get("/pruebas(/:uno(/:dos))",'prueba1',function($uno=NULL,$dos=NULL){
echo $uno.'<br>';
echo $dos.'<br>';
})->conditions(array(
    "uno"=> "[a-zA-Z]*",
    "dos"=> "[0-9]*"
        )
);

$app->run();