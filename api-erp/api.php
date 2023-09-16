<?php

header('Access-Control-Allow-Origin:*');

header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");

header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

header("Allow: GET, POST, OPTIONS, PUT, DELETE");

$method = $_SERVER['REQUEST_METHOD'];

if($method == "OPTIONS") {

    die();

}

require_once 'vendor/autoload.php';



use Psr\Http\Message\ResponseInterface as Response;

use Psr\Http\Message\ServerRequestInterface as Request;



$app = new Slim\Slim();

//$db = new mysqli("localhost","marife","libido16","no-whaste");

$db = new mysqli("localhost","aprendea_erp","erp2023*","aprendea_erp");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


mysqli_set_charset($db, 'utf8');

if (mysqli_connect_errno()) {

    printf("Conexiónes fallida: %s\n", mysqli_connect_error());

    exit();

}

$data=array();



$app->get("/usuarios",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

    $resultado = $db->query("SELECT *  FROM usuarios order by id desc");

    $prods=array();

        while ($fila = $resultado->fetch_array()) {



            $prods[]=$fila;

        }

        $respuesta=json_encode($prods);

        echo  $respuesta;



    });



    $app->get("/tabla/:tabla/:id",function($tabla,$id) use($db,$app){

        header("Content-type: application/json; charset=utf-8");

        $resultado = $db->query("SELECT *  FROM {$tabla} where id={$id}");

        $prods=array();

            while ($fila = $resultado->fetch_array()) {



                $prods[]=$fila;

            }

            $respuesta=json_encode($prods);

            echo  $respuesta;



        });





    $app->get("/tabla/:tabla",function($tabla) use($db,$app){

        header("Content-type: application/json; charset=utf-8");

        $resultado = $db->query("SELECT *  FROM {$tabla} order by id desc");

        $prods=array();

            while ($fila = $resultado->fetch_array()) {



                $prods[]=$fila;

            }

            $respuesta=json_encode($prods);

            echo  $respuesta;



        });









 $app->delete("/usuario/:id",function($id) use($db,$app){

    header("Content-type: application/json; charset=utf-8");

       $json = $app->request->getBody();

       $j = json_decode($json,true);

       $data = json_decode($j['json']);

                  $query ="DELETE FROM usuario WHERE id='{$id}'";

                  if($db->query($query)){

       $result = array("STATUS"=>true,"messaje"=>"Usuario eliminado correctamente");

       }

       else{

        $result = array("STATUS"=>false,"messaje"=>"Error al eliminar usuario");

       }

        echo  json_encode($result);

    });

/**Agregar caja */


$app->post("/cajas",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

       $json = $app->request->getBody();

       $j = json_decode($json,true);

       $data = json_decode($j['json']);

       try {



        $sql="call p_cajas('{$data->nombre}','{$data->id_sucursal}',1,'{$data->usuario}')";

        $stmt = mysqli_prepare($db,$sql);

        mysqli_stmt_execute($stmt);

        $result = array("STATUS"=>true,"messaje"=>"Permiso registrado correctamente");

        }

        catch(PDOException $e) {



        $result = array("STATUS"=>false,"messaje"=>$e->getMessage());



    }
});



/*Agregar permisos*/

$app->post("/permisos",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

       $json = $app->request->getBody();

       $j = json_decode($json,true);

       $data = json_decode($j['json']);

       try {



        $sql="call p_permisos('{$data->id_usuario}','{$data->id_sucursal}',1,'{$data->usuario}')";

        $stmt = mysqli_prepare($db,$sql);

        mysqli_stmt_execute($stmt);

        $result = array("STATUS"=>true,"messaje"=>"Permiso registrado correctamente");

        }

        catch(PDOException $e) {



        $result = array("STATUS"=>false,"messaje"=>$e->getMessage());



    }



             echo  json_encode($result);

});


/*Agregar usuario*/

 $app->post("/usuario",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

       $json = $app->request->getBody();

       $j = json_decode($json,true);

       $data = json_decode($j['json']);

       try {



        $sql="call p_usuario('{$data->nombre}','{$data->correo}','{$data->rol}',{$data->estado})";

        $stmt = mysqli_prepare($db,$sql);

        mysqli_stmt_execute($stmt);

        $result = array("STATUS"=>true,"messaje"=>"Usuario registrado correctamente");

        }

        catch(PDOException $e) {



        $result = array("STATUS"=>false,"messaje"=>$e->getMessage());



    }



             echo  json_encode($result);

});

/**get cajas por usuario */
$app->get("/cajas/:uid",function($uid) use($db,$app){

    header("Content-type: application/json; charset=utf-8");

    $resultado = $db->query("SELECT c.id,c.nombre FROM cajas c inner JOIN sucursales s on c.id_sucursal=s.id inner JOIN permisos p on s.id=p.id_sucursal WHERE id_usuario='{$uid}'");

    $prods=array();

        while ($fila = $resultado->fetch_array()) {



            $prods[]=$fila;

        }

        $respuesta=json_encode($prods);

        echo  $respuesta;



    });




/*update usuario*/

/**get cajas */

$app->get("/cajas",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

    $resultado = $db->query("SELECT c.id, c.nombre,s.nombre ,c.estado,c.usuario,c.fecha_registro FROM aprendea_erp.cajas c
    inner join sucursales s on c.id_sucursal=s.id;");

    $prods=array();

        while ($fila = $resultado->fetch_array()) {



            $prods[]=$fila;

        }

        $respuesta=json_encode($prods);

        echo  $respuesta;



    });



/*permisos*/

$app->get("/permisos",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

    $resultado = $db->query("SELECT p.id, s.nombre sucursal,u.nombre ,p.estado,p.usuario,p.fecha_registro FROM aprendea_erp.permisos p inner join sucursales s inner join usuarios u
    where p.id_sucursal=s.id and p.id_usuario=u.id;");

    $prods=array();

        while ($fila = $resultado->fetch_array()) {



            $prods[]=$fila;

        }

        $respuesta=json_encode($prods);

        echo  $respuesta;



    });



$app->put("/usuario",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

       $json = $app->request->getBody();

       $j = json_decode($json,true);

       $data = json_decode($j['json']);

       try {



        $sql="call p_usuario_upd({$data->id},'{$data->nombre}','{$data->correo}',{$data->estado})";

        $stmt = mysqli_prepare($db,$sql);

        mysqli_stmt_execute($stmt);

        $result = array("STATUS"=>true,"messaje"=>"Usuario actualizado correctamente");

       }

        catch(PDOException $e) {

            $result = array("STATUS"=>true,"messaje"=>$e->getMessage());

             }

        $respuesta=json_encode($result);

        echo  $respuesta;



});



$app->post("/usuario_del",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

       $json = $app->request->getBody();

       $j = json_decode($json,true);

       $data = json_decode($j['json']);

       try {



        $sql="call p_usuario_del({$data->usuario->id})";

        $stmt = mysqli_prepare($db,$sql);

        mysqli_stmt_execute($stmt);

        $result = array("STATUS"=>true,"messaje"=>"Usuario eliminado correctamente");

       }

        catch(PDOException $e) {

            $result = array("STATUS"=>true,"messaje"=>$e->getMessage());

             }

        $respuesta=json_encode($result);

        echo  $respuesta;



});

/*productos*/

$app->get("/articulos/:criterio",function($criterio) use($db,$app){

    header("Content-type: application/json; charset=utf-8");

    $resultado = $db->query("SELECT p.id,p.codigo,p.nombre,c.nombre categoria,sc.nombre subcategoria,fa.nombre familia, p.unidad,p.precio,p.imagen

    FROM productos p right join categorias c on p.id_categoria=c.id   right join sub_categorias sc  on p.id_subcategoria=sc.id

    right join sub_sub_categorias fa on p.id_sub_sub_categoria=fa.id where p.nombre like '%{$criterio}%' order by id;");

    $prods=array();

        while ($fila = $resultado->fetch_array()) {



            $prods[]=$fila;

        }

        $respuesta=json_encode($prods);

        echo  $respuesta;



    });







$app->get("/articulos",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

    $resultado = $db->query("SELECT p.id,p.codigo,p.nombre,c.nombre categoria,sc.nombre subcategoria,fa.nombre familia, p.unidad,p.precio,p.imagen FROM productos p LEFT join categorias c on p.id_categoria=c.id LEFT join sub_categorias sc on p.id_subcategoria=sc.id LEFT join sub_sub_categorias fa on p.id_sub_sub_categoria=fa.id order by id");

    $prods=array();

        while ($fila = $resultado->fetch_array()) {
            $prods[]=$fila;
        }

        $respuesta=json_encode($prods);

        echo  $respuesta;



    });





    $app->get("/articulo/:id",function($id) use($db,$app){

        header("Content-type: application/json; charset=utf-8");

        $resultado = $db->query("SELECT p.*,p.id_sub_sub_categoria id_familia FROM productos p  WHERE id={$id}");

        $prods=array();

            while ($fila = $resultado->fetch_array()) {



                $prods[]=$fila;

            }

            $respuesta=json_encode($prods);

            echo  $respuesta;



    });





    $app->post("/producto",function() use($db,$app){

        header("Content-type: application/json; charset=utf-8");

           $json = $app->request->getBody();

           $j = json_decode($json,true);

           $data = json_decode($j['json']);





            $query = "INSERT INTO `apitest`.`productos`(`id_categoria`,`id_subcategoria`,`id_sub_sub_categoria`,`codigo`,`nombre`,`unidad`)

             values({$data->id_categoria} ,{$data->id_subcategoria},{$data->id_familia},'{$data->nombre}','{$data->codigo}','{$data->unidad}')";

            $proceso=$db->query($query);

            if($proceso){

           $result = array("STATUS"=>true,"messaje"=>"Producto creada correctamente");

            }else{

            $result = array("STATUS"=>false,"messaje"=>"Ocurrio un error en la creación");

            }

            echo  json_encode($result);

        });





        $app->put("/producto",function() use($db,$app){

            header("Content-type: application/json; charset=utf-8");

               $json = $app->request->getBody();

               $j = json_decode($json,true);

               $data = json_decode($j['json']);



               $archivo = $data->imagen;

               $archivo = base64_decode($archivo);



               if(isset($data->nombre_imagen)){

                $filePath = $_SERVER['DOCUMENT_ROOT']."/erp-api/upload/".$data->nombre_imagen;

                file_put_contents($filePath, $archivo);

                $query = "UPDATE `productos` SET id_categoria={$data->id_categoria}, id_subcategoria={$data->id_subcategoria},id_sub_sub_categoria={$data->id_familia},nombre='{$data->nombre}',codigo='{$data->codigo}',unidad='{$data->unidad}',precio={$data->precio},imagen='{$data->nombre_imagen}' WHERE id={$data->id}";

            }else{

                $query = "UPDATE `productos` SET id_categoria={$data->id_categoria}, id_subcategoria={$data->id_subcategoria},id_sub_sub_categoria={$data->id_familia},nombre='{$data->nombre}',codigo='{$data->codigo}',unidad='{$data->unidad}',precio={$data->precio} WHERE id={$data->id}";

            }











                $proceso=$db->query($query);

                if($proceso){

               $result = array("STATUS"=>true,"messaje"=>"Producto actualizado correctamente");

                }else{

                $result = array("STATUS"=>false,"messaje"=>"Ocurrio un error en la creación");

                }

                echo  json_encode($result);

            });





            $app->post("/del_producto",function() use($db,$app){

                header("Content-type: application/json; charset=utf-8");

                   $json = $app->request->getBody();

                   $j = json_decode($json,true);

                   $data = json_decode($j['json']);



                    $query = "DELETE FROM `productos` WHERE id={$data->producto->id}";

                    $proceso=$db->query($query);

                    if($proceso){

                   $result = array("STATUS"=>true,"messaje"=>"Producto eliminado correctamente");

                    }else{

                    $result = array("STATUS"=>false,"messaje"=>"Ocurrio un error en la creación");

                    }

                    echo  json_encode($result);

                });







 /*proveedores*/



 $app->get("/proveedores",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

    $resultado = $db->query("SELECT *  FROM proveedores order by id desc");

    $prods=array();

        while ($fila = $resultado->fetch_array()) {



            $prods[]=$fila;

        }

        $respuesta=json_encode($prods);

        echo  $respuesta;



    });





/*Proveedores*/

$app->get("/proveedores",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

    $resultado = $db->query("SELECT `id`, `razon_social`,`num_documento`, `direccion`,`departamento`,`provincia`,`distrito` FROM `proveedores` order by id desc");

    $prods=array();

        while ($fila = $resultado->fetch_array()) {



            $prods[]=$fila;

        }

        $respuesta=json_encode($prods);

        echo  $respuesta;



});



$app->post("/del_proveedor",function() use($db,$app){

header("Content-type: application/json; charset=utf-8");

$json = $app->request->getBody();

$j = json_decode($json,true);

$data = json_decode($j['json']);

          $query ="DELETE FROM proveedores WHERE id='{$data->proveedor->id}'";

          if($db->query($query)){

$result = array("STATUS"=>true,"messaje"=>"Proveedor eliminado correctamente");

}

else{

$result = array("STATUS"=>false,"messaje"=>"Error al eliminar el proveedor");

}



echo  json_encode($result);

});







$app->post("/proveedor",function() use($db,$app){

header("Content-type: application/json; charset=utf-8");

$json = $app->request->getBody();

$j = json_decode($json,true);

$data = json_decode($j['json']);



$ruc=(is_array($data->num_documento))? array_shift($data->num_documento): $data->num_documento;

$razon_social=(is_array($data->razon_social)) ? array_shift(str_replace("'","\'",$data->razon_social)):str_replace("'","\'",$data->razon_social);

$direccion=(is_array($data->direccion))? array_shift($data->direccion): $data->direccion;

$departamento=(is_array($data->departamento))? array_shift($data->departamento): $data->departamento;

$provincia=(is_array($data->provincia))? array_shift($data->provincia): $data->provincia;

$distrito=(is_array($data->distrito))? array_shift($data->distrito): $data->distrito;

$num_documento=(is_array($data->num_documento))? array_shift($data->num_documento): $data->num_documento;



try {

$query ="INSERT INTO proveedores (razon_social,direccion, num_documento, departamento,provincia,distrito) VALUES ("

."'{$razon_social}',"

."'{$direccion}',"

."'{$ruc}',"

."'{$departamento}',"

."'{$provincia}',"

."'{$distrito}'".")";

$db->query($query);

  }

catch(PDOException $e) {

$result = array("STATUS"=>true,"messaje"=>$e->getMessage(),"string"=>$query);

}







echo  json_encode($result);

});





$app->put("/proveedor",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

       $json = $app->request->getBody();

       $j = json_decode($json,true);

       $data = json_decode($j['json']);

       try {

        $sql="call p_proveedor_upd({$data->id},'{$data->razon_social}','{$data->direccion}','{$data->num_documento}','{$data->telefono}','{$data->departamento}','{$data->provincia}','{$data->distrito}')";

        $stmt = mysqli_prepare($db,$sql);

        mysqli_stmt_execute($stmt);

        $result = array("STATUS"=>true,"messaje"=>"Proveedor actualizado correctamente");

       }

        catch(PDOException $e) {

            $result = array("STATUS"=>true,"messaje"=>$e->getMessage());

             }

        $respuesta=json_encode($result);

        echo  $respuesta;



});



/*proveedores fin*/





$app->get("/categorias",function() use($db,$app){

        header("Content-type: application/json; charset=utf-8");

        $resultado = $db->query("SELECT id, nombre  FROM  categorias order by id");

        $prods=array();

            while ($fila = $resultado->fetch_array()) {



                $prods[]=$fila;

            }

            $respuesta=json_encode($prods);

            echo  $respuesta;



        });





        $app->get("/sub_categorias",function() use($db,$app){

            header("Content-type: application/json; charset=utf-8");

            $resultado = $db->query("SELECT id, nombre  FROM  sub_categorias order by id");

            $prods=array();

                while ($fila = $resultado->fetch_array()) {



                    $prods[]=$fila;

                }

                $respuesta=json_encode($prods);

                echo  $respuesta;



            });







        $app->get("/familia",function() use($db,$app){

            header("Content-type: application/json; charset=utf-8");

            $resultado = $db->query("SELECT id, nombre  FROM  sub_sub_categorias order by id");

            $prods=array();

                while ($fila = $resultado->fetch_array()) {



                    $prods[]=$fila;

                }

                $respuesta=json_encode($prods);

                echo  $respuesta;



            });



            $app->get("/unidad",function() use($db,$app){

                header("Content-type: application/json; charset=utf-8");

                $resultado = $db->query("SELECT id, codigo,nombre  FROM  unidad  order by id");

                $prods=array();

                    while ($fila = $resultado->fetch_array()) {



                        $prods[]=$fila;

                    }

                    $respuesta=json_encode($prods);

                    echo  $respuesta;



                });





        $app->get("/subcategorias",function() use($db,$app){

            header("Content-type: application/json; charset=utf-8");

            $resultado = $db->query("SELECT s.id,c.id id_categoria,c.nombre categoria,s.nombre FROM sub_categorias s,categorias c WHERE s.id_categoria=c.id order by s.id");

            $prods=array();

                while ($fila = $resultado->fetch_array()) {



                    $prods[]=$fila;

                }

                $respuesta=json_encode($prods);

                echo  $respuesta;



            });





    $app->post("/categoria",function() use($db,$app){

        header("Content-type: application/json; charset=utf-8");

           $json = $app->request->getBody();

           $j = json_decode($json,true);

           $data = json_decode($j['json']);



            $nombre=(is_array($data->nombre))? array_shift($data->nombre): $data->nombre;



            $query ="INSERT INTO categorias (nombre) VALUES ('"."{$nombre}"."')";

            $proceso=$db->query($query);

            if($proceso){

           $result = array("STATUS"=>true,"messaje"=>"Categoria creada correctamente");

            }else{

            $result = array("STATUS"=>false,"messaje"=>"Ocurrio un error en la creación");

            }

            echo  json_encode($result);

        });



        $app->post("/subcategoria",function() use($db,$app){

            header("Content-type: application/json; charset=utf-8");

               $json = $app->request->getBody();

               $j = json_decode($json,true);

               $data = json_decode($j['json']);



               $query ="INSERT INTO sub_categorias (id_categoria,nombre) VALUES ({$data->id_categoria},'{$data->nombre}')";

                $proceso=$db->query($query);

                if($proceso){

               $result = array("STATUS"=>true,"messaje"=>"Subcategoria creada correctamente");

                }else{

                $result = array("STATUS"=>false,"messaje"=>"Ocurrio un error en la creación");

                }

                echo  json_encode($result);

        });



        $app->get("/subcategoria/:criterio",function($criterio) use($db,$app){

            header("Content-type: application/json; charset=utf-8");

            $resultado = $db->query("SELECT s.id,c.id id_categoria,c.nombre categoria,s.nombre FROM sub_categorias s,categorias c WHERE s.id_categoria=c.id and c.id={$criterio} order by s.id");

            $prods=array();

                while ($fila = $resultado->fetch_array()) {



                    $prods[]=$fila;

                }

                $respuesta=json_encode($prods);

                echo  $respuesta;



        });





        $app->post("/categoriadel",function() use($db,$app){

            header("Content-type: application/json; charset=utf-8");

               $json = $app->request->getBody();

               $j = json_decode($json,true);

               $data = json_decode($j['json']);

               $codigo=(is_array($data->id))? array_shift($data->id): $data->id;

               $query ="DELETE FROM categorias WHERE id="."'{$codigo}'";

               $operacion=$db->query($query);

               if($operacion){

               $result = array("STATUS"=>true,"messaje"=>"Categoria eliminada correctamente");

            }else{

                $result = array("STATUS"=>false,"messaje"=>'Ocurrio un error');

            }

                echo  json_encode($result);

            });



    $app->post("/productodel",function() use($db,$app){

        header("Content-type: application/json; charset=utf-8");

           $json = $app->request->getBody();

           $j = json_decode($json,true);

           $data = json_decode($j['json']);

           $codigo=(is_array($data->codigo))? array_shift($data->codigo): $data->codigo;

           $query ="DELETE FROM productos WHERE codigo="."'{$codigo}'";

           $db->query($query);



           $result = array("STATUS"=>true,"messaje"=>"Producto eliminado correctamente","string"=>$query);

            echo  json_encode($result);

        });





/*productos*/



$app->get("/producto/:id",function($id) use($db,$app){

    header("Content-type: application/json; charset=utf-8");

    try{

    $resultado = $db->query("SELECT `id`, `codigo`, `nombre`,`peso` FROM `productos` where id ={$id}");

    $prods=array();

    while ($fila = $resultado->fetch_array()) {



        $prods[]=$fila;

    }

    $respuesta=json_encode($prods);

}catch (PDOException $e){

    $respuesta=json_encode(array("status"=>$e->message));

}

             echo  $respuesta;



});



    $app->get("/productos/:criterio",function($criterio) use($db,$app){

            header("Content-type: application/json; charset=utf-8");

            try{

            $resultado = $db->query("SELECT `id`, `codigo`, `nombre` FROM `productos` where nombre like '%".$criterio."%'");

            $prods=array();

            while ($fila = $resultado->fetch_array()) {



                $prods[]=$fila;

            }

            $respuesta=json_encode($prods);

        }catch (PDOException $e){

            $respuesta=json_encode(array("status"=>$e->message));

        }

                     echo  $respuesta;



        });



    $app->post("/producto",function() use($db,$app){

        header("Content-type: application/json; charset=utf-8");

           $json = $app->request->getBody();

           $j = json_decode($json,true);

           $data = json_decode($j['json']);



           $codigo=(is_array($data->codigo))? array_shift($data->codigo): $data->codigo;

            $nombre=(is_array($data->nombre))? array_shift($data->nombre): $data->nombre;

            $peso=(is_array($data->peso))? array_shift($data->peso): $data->peso;

            $costo=(is_array($data->costo))? array_shift($data->costo): $data->costo;

            $categoria=(is_array($data->id_categoria))? array_shift($data->id_categoria): $data->id_categoria;

            $sub_categoria=(is_array($data->id_subcategoria))? array_shift($data->id_subcategoria): $data->id_subcategoria;

            $usuario=(is_array($data->usuario))? array_shift($data->usuario): $data->usuario;





           $query ="INSERT INTO productos (codigo,nombre,peso,costo,id_categoria,id_subcategoria,usuario) VALUES ("

          ."'{$codigo}',"

          ."'{$nombre}',"

          ."'{$peso}',"

          ."{$costo},"

          ."{$categoria},"

          ."{$sub_categoria},"

          ."'{$usuario}'".")";



          $insert=$db->query($query);



           $result = array("STATUS"=>true,"messaje"=>"Producto creado correctamente");

            echo  json_encode($result);

        });



        $app->post("/productoedit",function() use($db,$app){

            header("Content-type: application/json; charset=utf-8");

            $json = $app->request->getBody();

            $j = json_decode($json,true);

            $data = json_decode($j['json']);



            $codigo=(is_array($data->codigo))? array_shift($data->codigo): $data->codigo;

            $nombre=(is_array($data->nombre))? array_shift($data->nombre): $data->nombre;

            $peso=(is_array($data->peso))? array_shift($data->peso): $data->peso;

            $costo=(is_array($data->costo))? array_shift($data->costo): $data->costo;

            $precio=(is_array($data->precio_sugerido))? array_shift($data->precio_sugerido): $data->precio_sugerido;

            $categoria=(is_array($data->id_categoria))? array_shift($data->id_categoria): $data->id_categoria;

            $sub_categoria=(is_array($data->id_subcategoria))? array_shift($data->id_subcategoria): $data->id_subcategoria;

            $usuario=(is_array($data->usuario))? array_shift($data->usuario): $data->usuario;



            $sql = "UPDATE productos SET codigo='".$codigo."', nombre='".$nombre."',peso=".$peso.",costo=".$costo.", precio_sugerido=".$precio.",id_categoria=".$categoria.",id_subcategoria=".$sub_categoria.",usuario='".$usuario."' WHERE id={$data->id}";

            try {

            $db->query($sql);

             $result = array("STATUS"=>true,"messaje"=>"Producto actualizado correctamente","string"=>$sql);

             echo  json_encode($result);

            }

             catch(PDOException $e) {

        echo '{"error":{"text":'. $e->getMessage() .'}}';

        }





         });



$app->get("/empresas",function() use($db,$app){

    header("Content-type: application/json;");

    $resultado = $db->query("SELECT id, razon_social,num_documento,direccion,telefono,departamento,provincia,distrito,estado FROM  empresas order by id desc");

    $prods=array();

        while ($fila = $resultado->fetch_array()) {



            $prods[]=$fila;

        }

        $respuesta=json_encode($prods);

        echo  $respuesta;



});













/**Compras */



$app->post("/compra",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

       $json = $app->request->getBody();

       $j = json_decode($json,true);

       $data = json_decode($j['json']);

       try {

        $fecha=substr($data->fecha,0,10);

        $sql="call p_compra('{$data->comprobante}','{$data->num_comprobante}','{$data->descripcion}','{$fecha}',{$data->id_proveedor})";

        $stmt = mysqli_prepare($db,$sql);

        mysqli_stmt_execute($stmt);

        $datos=$db->query("SELECT max(id) ultimo_id FROM compras");

        $ultimo_id=array();

        while ($d = $datos->fetch_object()) {

         $ultimo_id=$d;

         }



         foreach($data->detalleCompra as $valor){

            $proc="call p_compra_detalle(0,{$valor->cantidad},{$valor->precio},{$ultimo_id->ultimo_id},'{$valor->descripcion}')";

           $stmt = mysqli_prepare($db,$proc);

            mysqli_stmt_execute($stmt);

            $proc="";

        }

        $result = array("STATUS"=>true,"messaje"=>"Compra registrada correctamente","string"=>$fecha);



        }

         catch(PDOException $e) {



        $result = array("STATUS"=>false,"messaje"=>$e->getMessage());



    }



             echo  json_encode($result);

});



$app->post("/comprobante",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

       $json = $app->request->getBody();

       $j = json_decode($json,true);

       $data = json_decode($j['json']);



       //$sql = "UPDATE compras SET comprobante='".$data->comprobante."',id_proveedor=".$data->id_proveedor.",num_comprobante='".$data->num_comprobante."', descripcion='".$data->descripcion."',fecha='".substr($data->fecha,0,10)."' WHERE id=".$data->id;

        /*$db->query($sql);

        $borra="DELETE FROM detalle_compras where id_compra={$data->id}";

        $db->query($borra);

        foreach($data->detalleCompra as $valor){

          $proc="call p_compra_detalle(0,{$valor->cantidad},{$valor->precio},{$data->id},'{$valor->descripcion}')";

           $stmt = mysqli_prepare($db,$proc);

            mysqli_stmt_execute($stmt);

            $proc="";*/

        $result = array("STATUS"=>true,"messaje"=>"Compra actualizada correctamente");



       /*  catch(PDOException $e) {

        $result = array("STATUS"=>true,"messaje"=>$e->getMessage());

         }*/

        echo  json_encode($data);

    });







$app->post("/compraedit",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

       $json = $app->request->getBody();

       $j = json_decode($json,true);

       $data = json_decode($j['json']);



       $sql = "UPDATE compras SET comprobante='".$data->comprobante."',id_proveedor=".$data->id_proveedor.",num_comprobante='".$data->num_comprobante."', descripcion='".$data->descripcion."',fecha='".substr($data->fecha,0,10)."' WHERE id=".$data->id;

       try {

        $db->query($sql);

        $borra="DELETE FROM detalle_compras where id_compra={$data->id}";

        $db->query($borra);

        foreach($data->detalleCompra as $valor){

          $proc="call p_compra_detalle(0,{$valor->cantidad},{$valor->precio},{$data->id},'{$valor->descripcion}')";

           $stmt = mysqli_prepare($db,$proc);

            mysqli_stmt_execute($stmt);

            $proc="";

        }



        $result = array("STATUS"=>true,"messaje"=>"Compra actualizada correctamente");

        }

         catch(PDOException $e) {

        $result = array("STATUS"=>true,"messaje"=>$e->getMessage());

         }

        echo  json_encode($result);





});



$app->get("/compra/:id",function($id) use($db,$app){

    header("Content-type: application/json; charset=utf-8");

    $resultado = $db->query("SELECT `id`, `descripcion`, `cantidad`, `precio`, `id_articulo`, `id_compra` FROM `detalle_compras` where id_compra={$id}");

    $prods=array();

        while ($fila = $resultado->fetch_array()) {

            $prods[]=$fila;

        }

        $respuesta=json_encode($prods);

        echo  $respuesta;

});





$app->get("/compras",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

    $resultado = $db->query("SELECT c.`id`, comprobante,`num_comprobante`, `descripcion`,DATE_FORMAT(fecha, '%Y-%m-%d') fecha, c.`id_proveedor`,p.razon_social, `id_usuario`,(select sum(precio) from detalle_compras where id_compra=c.id) total  FROM `compras` c, proveedores p where c.id_proveedor=p.id order by c.id desc");

    $prods=array();

        while ($fila = $resultado->fetch_array()) {

            $prods[]=$fila;

        }

        $respuesta=json_encode($prods);

        echo  $respuesta;

});





/*Inventarios*/



$app->get("/almacen",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

    $resultado=$db->query("SELECT i.id,id_producto,p.codigo,p.nombre,presentacion,unidad,id_producto,DATE_FORMAT(fecha_produccion, '%d-%m-%Y') fecha_produccion,DATE_FORMAT(fecha_vencimiento, '%d-%m-%Y') fecha_vencimiento,observacion,granel,cantidad, ROUND(i.peso/1000,2) peso,merma FROM inventario i, productos p where i.id_producto=p.id and i.cantidad>0 order by i.id desc;");

    $prods=array();

        while ($fila = $resultado->fetch_array()) {



            $prods[]=$fila;

        }

        $respuesta=json_encode($prods);

        echo  $respuesta;



});



$app->get("/inventarios",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

    $resultado = $db->query("SELECT i.id,p.codigo,`id_producto`,p.nombre, `id_producto`,`presentacion`,`granel`,`cantidad`,format((p.peso*i.cantidad)/1000,2) peso,`merma`, DATE_FORMAT(fecha_produccion, '%Y-%m-%d')  fecha_produccion,DATE_FORMAT(fecha_vencimiento, '%Y-%m-%d')  fecha_vencimiento,datediff(fecha_vencimiento,now()) `dias`, `estado`, `ciclo`, `id_usuario` FROM `inventario` i, productos p where i.cantidad>0 and i.id_producto=p.id");

    $prods=array();

        while ($fila = $resultado->fetch_array()) {



            $prods[]=$fila;

        }

        $respuesta=json_encode($prods);

        echo  $respuesta;

    });





    $app->get("/alertaintentario",function() use($db,$app){

        header("Content-type: application/json; charset=utf-8");

        $prods=array();

        $resultado = $db->query("SELECT i.id,id_producto,fecha_produccion,p.nombre,datediff(now(),fecha_produccion) dias FROM `inventario` i ,`productos` p where i.id_producto=p.id and datediff(now(),fecha_produccion) between 1 and 7 order by fecha_produccion");



        if($resultado->num_rows>0){

               while ($fila = $resultado->fetch_array()) {



                $prods[]=$fila;

            }

            $respuesta=json_encode($prods);

        }else{

            $respuesta=json_encode($prods);

        }

            echo  $respuesta;

        });





    $app->post("/inventario",function() use($db,$app){

        header("Content-type: application/json; charset=utf-8");

           $json = $app->request->getBody();

           $j = json_decode($json,true);

           $data = json_decode($j['json']);

           try {

            $fecha=substr($data->fecha_produccion,0,10);

            $fecha2=substr($data->fecha_vencimiento,0,10);

            $sql="call p_inventario({$data->id_producto},'{$data->presentacion}','{$data->unidad}',{$data->granel},{$data->cantidad},{$data->peso},{$data->merma},'{$fecha}','{$fecha2}','{$data->observacion}')";

            $stmt = mysqli_prepare($db,$sql);

            mysqli_stmt_execute($stmt);

            $result = array("STATUS"=>true,"messaje"=>"Inventario registrado correctamente","string"=>$fecha);

           }

            catch(PDOException $e) {

                $result = array("STATUS"=>true,"messaje"=>$e->getMessage());

                 }



                 $respuesta=json_encode($result);

                echo  $respuesta;



        });







        $app->put("/inventario",function() use($db,$app){

            header("Content-type: application/json; charset=utf-8");

               $json = $app->request->getBody();

               $j = json_decode($json,true);

               $data = json_decode($j['json']);

               try {

                $fecha_prod=substr($data->fecha_produccion,0,10);

                $fecha_venc=substr($data->fecha_vencimiento,0,10);

                $sql="call p_inventario_upd({$data->id},'{$fecha_prod}','{$fecha_venc}','{$data->presentacion}',{$data->cantidad})";

                $stmt = mysqli_prepare($db,$sql);

                mysqli_stmt_execute($stmt);

                $result = array("STATUS"=>true,"messaje"=>"Inventario actualizado correctamente");

               }

                catch(PDOException $e) {

                    $result = array("STATUS"=>true,"messaje"=>$e->getMessage());

                     }

                $respuesta=json_encode($result);

                echo  $respuesta;



        });



        $app->delete("/movimiento/:id",function($id) use($db,$app){

            header("Content-type: application/json; charset=utf-8");

               $json = $app->request->getBody();

               $j = json_decode($json,true);

               $data = json_decode($j['json']);

                          $query ="DELETE FROM dosimetria_movimientos WHERE id='{$id}'";

                          if($db->query($query)){

               $result = array("STATUS"=>true,"messaje"=>"Movimiento  eliminado correctamente");

               }

               else{

                $result = array("STATUS"=>false,"messaje"=>"Error al eliminar item");

               }



                echo  json_encode($result);

            });





        $app->delete("/inventario/:id",function($id) use($db,$app){

            header("Content-type: application/json; charset=utf-8");

               $json = $app->request->getBody();

               $j = json_decode($json,true);

               $data = json_decode($j['json']);

                          $query ="DELETE FROM inventario WHERE id='{$id}'";

                          if($db->query($query)){

               $result = array("STATUS"=>true,"messaje"=>"Item de inventario  eliminado correctamente");

               }

               else{

                $result = array("STATUS"=>false,"messaje"=>"Error al eliminar item");

               }



                echo  json_encode($result);

            });



/*vendedores*/





$app->post("/vendedores",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

       $json = $app->request->getBody();

       $j = json_decode($json,true);

       $data = json_decode($j['json']);



       $query ="INSERT INTO vendedor (nombre,apellidos,dni,razon_social,ruc) VALUES ('{$data->nombre}','{$data->apellidos}','{$data->dni}','{$data->razon_social}','{$data->ruc}')";

        $proceso=$db->query($query);

        if($proceso){

       $result = array("STATUS"=>true,"messaje"=>"Vendedor creado correctamente");

        }else{

        $result = array("STATUS"=>false,"messaje"=>"Ocurrio un error en la creación");

        }

        echo  json_encode($result);

});



$app->get("/vendedores",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

    $resultado = $db->query("SELECT `id`, `nombre`, `apellidos`, `dni`, `razon_social`, `ruc`,`fecha_registro` FROM `vendedor` order by id desc");

    $vendedores=array();

        while ($fila = $resultado->fetch_array()) {



            $vendedores[]=$fila;

        }

        $respuesta=json_encode($vendedores);

        echo  $respuesta;

    });



    $app->delete("/vendedores/:id",function($id) use($db,$app){

        header("Content-type: application/json; charset=utf-8");

        $resultado = $db->query("DELETE FROM `vendedor` where  id={$id}");



        if($resultado){

            $result = array("STATUS"=>true,"messaje"=>"Vendedor eliminado correctamente");

             }else{

             $result = array("STATUS"=>false,"messaje"=>"Ocurrio un error en la creación");

             }

             echo  json_encode($result);

        });



/*ventas*/



$app->get("/pendientes",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

    $resultado = $db->query("SELECT v.id,v.tipoDoc, v.id_usuario,u.nombre usuario,ve.id id_vendedor,concat(ve.nombre,' ',ve.apellidos) vendedor,c.id id_cliente,c.num_documento,c.direccion,concat(c.nombre,' ',c.apellido) cliente,igv,monto_igv,valor_neto,valor_total, estado, comprobante,nro_comprobante, DATE_FORMAT(v.fecha, '%Y-%m-%d') fecha,observacion FROM ventas v,usuarios u,clientes c,vendedor ve where v.id_vendedor=ve.id and v.id_cliente=c.id and v.id_usuario=u.id and v.comprobante ='Pendiente' and v.tipoDoc=1 union all SELECT v.id,v.tipoDoc,v.id_usuario,u.nombre usuario,ve.id id_vendedor,concat(ve.nombre,' ',ve.apellidos) vendedor,c.id id_cliente,c.num_documento,c.direccion,concat(c.razon_social) cliente,igv,monto_igv,valor_neto,valor_total, v.estado, comprobante,nro_comprobante,DATE_FORMAT(v.fecha, '%Y-%m-%d') fecha,observacion FROM ventas v,usuarios u,empresas c,vendedor ve where v.id_vendedor=ve.id and v.id_cliente=c.id and v.id_usuario=u.id and v.comprobante='Pendiente' and v.tipoDoc=2 order by id desc;");

    $prods=array();

        while ($fila = $resultado->fetch_array()) {

            $prods[]=$fila;

        }

        $respuesta=json_encode($prods);

        echo  $respuesta;

});





$app->get("/ventas",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

     $resultado = $db->query("SELECT v.id,c.nombre as cliente,u.nombre,v.tipoDoc,v.id_vendedor,v.id_sucursal,DATE_FORMAT(v.fecha_registro, '%d-%m-%Y') fechaPago,IF(v.pendientes=0,'No','Si') pendientes,v.igv,v.monto_igv,v.descuento,v.valor_neto,v.valor_total,v.observacion FROM ventas v inner join clientes c on v.id_cliente=c.id inner join usuarios u on v.id_usuario=u.id order by 1 desc");

    $prods=array();

        while ($fila = $resultado->fetch_array()) {

            $prods[]=$fila;

        }

        $respuesta=json_encode($prods);

        echo  $respuesta;

});



$app->get("/inventarios/:id",function($id) use($db,$app){

    header("Content-type: application/json; charset=utf-8");

    $resultado = $db->query("SELECT i.id,p.codigo,id_producto,p.nombre,p.precio_sugerido precio,`presentacion`,`cantidad`,i.peso,i.unidad,DATE_FORMAT(fecha_produccion,'%Y-%m-%d')  fecha_produccion,datediff(now(),fecha_produccion) `dias`, `estado`, `ciclo`, `id_usuario` FROM `inventario` i, productos p where i.id_producto=p.id and id_producto={$id} and i.cantidad>0 order by fecha_produccion asc");

    $prods=array();

        while ($fila = $resultado->fetch_array()) {



            $prods[]=$fila;

        }

        $respuesta=json_encode($prods);

        echo  $respuesta;

    });



    $app->post("/venta",function() use($db,$app){

        header("Content-type: application/json; charset=utf-8");
        $json = $app->request->getBody();
        $j = json_decode($json,true);
        $data = json_decode($j['json']);
        $detalle = json_decode($j['detalle']);
        $valor_total=0;
                try {
                   $sql="call p_venta('{$data->usuario}','{$data->vendedor}','{$data->cliente}',{$data->sucursal},'{$data->entrega}','{$data->tipoDoc}',{$data->neto},{$data->total},{$data->igv},'{$data->comentario}')";

                   $stmt = mysqli_prepare($db,$sql);
                    mysqli_stmt_execute($stmt);
                    $datos=$db->query("SELECT max(id) ultimo_id FROM ventas");
                    $ultimo_id=array();
                    while ($d = $datos->fetch_object()) {
                     $ultimo_id=$d;
                     }


                     foreach($data->pagos as $pago){
                    $procP="call p_venta_pago({$ultimo_id->ultimo_id},'{$pago->tipoPago}',{$pago->montoPago})";

                    $stmtP = mysqli_prepare($db,$procP);
                    mysqli_stmt_execute($stmtP);
                     }


                    foreach($detalle as $item){
                    /*inserta detalla*/

                    $proc="call p_venta_detalle({$ultimo_id->ultimo_id},{$item->id},{$item->id},'',{$item->cantidad},{$item->pendiente},{$item->descuento},{$item->precio})";
                    $stmt = mysqli_prepare($db,$proc);
                    mysqli_stmt_execute($stmt);
                    $stmt->close();

                    $sql="INSERT INTO salidas_articulos  (`codigo`,`id_venta`,`cantidad`,`id_sucursal`,`usuario`)  VALUES({$item->id},{$ultimo_id->ultimo_id},{$item->cantidad},$data->sucursal,'{$data->usuario}')";
                    $stmt2 = mysqli_prepare($db,$sql);
                    mysqli_stmt_execute($stmt2);
                    $stmt2->close();


                    //$actualiza="call p_actualiza_inventario({$valor->codProductob->id},{$valor->codProducto},{$valor->cantidad},{$valor->peso},'{$valor->unidadmedida}')";
                    //$stmtb = mysqli_prepare($db,$actualiza);
                    //mysqli_stmt_execute($stmtb);
                    //$stmtb->close();

                    }




                       $result = array("STATUS"=>true,"messaje"=>"Venta registrada correctamente con el número: ".$ultimo_id->ultimo_id);

                    }

                     catch(PDOException $e) {



                    $result = array("STATUS"=>false,"messaje"=>$e->getMessage());



                }





            echo  json_encode($result);

    });



    $app->post("/factura",function() use($db,$app){

        header("Content-type: application/json; charset=utf-8");

           $json = $app->request->getBody();

           $j = json_decode($json,true);

           $data = json_decode($j['json']);



             try {

           $sql="call p_factura('{$data->hash}',{$data->sunatResponse->cdrResponse->code},'{$data->sunatResponse->cdrResponse->description}','{$data->sunatResponse->cdrResponse->id}','{$data->sunatResponse->cdrZip}','{$data->sunatResponse->success}')";

           $stmt = mysqli_prepare($db,$sql);

           mysqli_stmt_execute($stmt);

           $stmt->close();

           $datos=$db->query("SELECT max(id)+1 ultimo_id FROM facturas");

           $ultimo_id=array();

           while ($d = $datos->fetch_object()) {

            $ultimo_id=$d;

            }



            $result = array("STATUS"=>true,"messaje"=>"Factura grabada correctamente","max"=>$ultimo_id);

            }

             catch(PDOException $e) {

                $result = array("STATUS"=>false,"messaje"=>$e->getMessage());

        }

            echo  json_encode($result);

     });



     /*guia remision*/



$app->get("/guias",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

    $resultado = $db->query("SELECT g.id,concat('T001-',g.id) numero, `tipoDoc`, if(destinatario=1,'DNI','RUC') doc, DATE_FORMAT(g.fechaemision, '%Y-%m-%d') `fechaemision`, `peso_bruto`, `nro_bultos`, `ubigeo_partida`, `partida`, `ubigeo_llegada`, `llegada`, `transp_tipoDoc`, `nro_transportista`, `nombre_transportista`, `nro_placa`, `observacion`,`tipo_destinatario`, concat(c.nombre,' ',c.apellido) destinatario,c.num_documento ,g.`fecha_registro`, `usuario`  FROM `guias` g, `clientes` c where g.destinatario=c.id and g.tipo_destinatario='1' union all SELECT g.id,concat('T001-',g.id) numero, `tipoDoc`, if(destinatario=1,'DNI','RUC') doc,DATE_FORMAT(g.fechaemision, '%Y-%m-%d') `fechaemision`, `peso_bruto`, `nro_bultos`, `ubigeo_partida`, `partida`, `ubigeo_llegada`, `llegada`, `transp_tipoDoc`, `nro_transportista`, `nombre_transportista`, `nro_placa`, `observacion`,`tipo_destinatario`, (c.razon_social) destinatario, c.num_documento, g.`fecha_registro`, `usuario`  FROM `guias` g, `empresas` c where g.destinatario=c.id and g.tipo_destinatario='6' order by id desc");

    $prods=array();

        while ($fila = $resultado->fetch_array()) {

            $prods[]=$fila;

        }

        $respuesta=json_encode($prods);

        echo  $respuesta;

});



$app->get("/guia/:id",function($id) use($db,$app){

    header("Content-type: application/json; charset=utf-8");

    $resultado = $db->query("SELECT v.`id`, `id_producto`,p.codigo,p.`nombre`,`unidad_medida` ,`cantidad` FROM `guia_detalle` v ,productos p where v.id_producto=p.codigo and id_guia={$id}");

    $prods=array();

        while ($fila = $resultado->fetch_array()) {

            $prods[]=$fila;

        }

        $respuesta=json_encode($prods);

        echo  $respuesta;

});





$app->post("/guia",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

       $json = $app->request->getBody();

       $j = json_decode($json,true);

       $data = json_decode($j['json']);

        //token desarrollo

        $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2MjE5OTc5NjQsImV4cCI6NDc3NTU5Nzk2NCwidXNlcm5hbWUiOiJ2dG9yZXJvIiwiY29tcGFueSI6IjIwNTIxMDQ4ODI1In0.bHutFGMZAqODBqfJnlSQNMuonyC3d5elHpy1wIXRwB3QtIPk7y3rnELjk1JBZF7G54vy5AsyJQPSpRLGs8Llo1QUC2g0yC83LBI1QGhpZ5W85PyPnTqldUoYTtXMvmChlz9CnExb-5sReOPTFlhy2IgUSpNdYIXC5G3YUZ32XgkRiiRWytS1swMQ-Nk52CBzzwH34oD6JwjMUS71T7_949CgsCyHZq5mSclXdGsIq6jfqi1JBo0na3OY0KmSpuJfsSmomJrbeqZauPLg50obAE029sgdjD7uW739aPNh1MP-_ZETa9b36gFcDQ_q3gytfVMissnLjNl1r92efY_WQ4wewa897hRPDm6i7XbRCkILPiYiWDxQ3tNsQIvg1Kg9Gu-090jdcdo5Mwx2mD4KGugPsYzfbmxCBUIRciFIaNiKnNFzQyELu7N0ghWJ6d-2vV1hQLEaZJPScqvVQbcJWwqawZEJ7cPLnlDXS2z-s59RtcMlH3gkVT9aT5df6oOe4yJzOpEa_zoPnumFwKHGVO5G0m2gzsNfEjz7B-zLa32lTxjPEUBUgFnuh7Xdoo88PHmKgAZ_JhxpU-Tq7dM3_dMZJwBigw_kLO1n3SI6ozPiPkBOQ0_un2ZP3dVRZ1ABfCjp2KK8NF0FIJAkLk-alrGCwNT4HgprxMnV0DmQyQU";



        //toque produccion

        //$token="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2MTAxNzI4ODYsInVzZXJuYW1lIjoidnRvcmVybyIsImNvbXBhbnkiOiIyMDYwNTE3NDA5NSIsImV4cCI6NDc2Mzc3Mjg4Nn0.nL93zWcOm8w3ZAkWm_Ia9vF7DaFnP_wzuSgeF7_X1CvzvQOotSK12WphLo7jFmBRfLJm2UBPoUucOSNzbk3zdbvjCdG1p2tQ7CQlypxWggSxQ76Wma6cFJL7NF9ULxOQEWGm3b2CVVfDq2MgSyE6xNmOGuzP_7CsSyukd-Q8MOqjgtefBRPeN0XtX85s0Ie-5Twy_AP-MXOFj1gYapEpSPWN4QrK4wibAu8tVs01ipczGsZXzrQWZFVpmozluXPtsx6hNHy_XAGhJSIU1Ftj8rc5xIa7RD2-VO-nJVlChmTPB5TGNH4YsQOASAaGXBtZmqxtpK9RJAmSFPpvxBr3XC6bcBGBRPUy0CmcH6VeVPJNTRcNzP7H11hFi49iS8P04ViccR8kMnUd-ABIGRSuhdxy6yv3JjV6P9MuyjSFmJFi1Mlw8lGFLI9UeHxLAr1AXk0MvD1-MtFMrHWc0JrqeiW8EU9RbwGAxGdVxCM9bVinQ6fYzou6W9lcjnHbktR3VqLiI6kkJlOIYRzByHLmOX59BlPhTcqJTC-jGKsuR7rJfMljKmknzDhnKy3eD16FShpzzpEXtta5tf_RvF4sMeX6XTT2WSN2z6RbtGvTyJ9bG3COpv7_iByUpHXh8VJTF5nzloKwS_lj7w45PP0_Rb7Al31POnfFOarU8dRM9LA";



       try {



        $datos=$db->query("SELECT max(id) ultimo_id FROM guias");

        $ultimo_id=array();

        while ($d = $datos->fetch_object()) {

         $ultimo_id=$d;

         }



        $data->correlativo= $ultimo_id->ultimo_id+1;

        $postdata=json_encode($data);



        $ch = curl_init('https://facturacion.apisperu.com/api/v1/despatch/send');

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

         curl_setopt($ch, CURLOPT_POST, 1);

         curl_setopt($ch, CURLOPT_POSTFIELDS,$postdata);

         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

         curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Bearer '. $token));

         $result = curl_exec($ch);

         curl_close($ch);

         $response_sunat=json_decode($result,false);



         if($response_sunat->sunatResponse->success==1)

         {

         $fecha=substr($data->fechaEmision,0,10);

         $sql="call p_guia('{$data->tipoDoc}','{$data->destinatario->tipoDoc}','{$data->destinatario->id}','{$fecha}','{$data->envio->pesoTotal}','{$data->envio->numBultos}','{$data->envio->partida->ubigueo}', '{$data->envio->partida->direccion}','{$data->envio->llegada->ubigueo}','{$data->envio->llegada->direccion}','{$data->envio->transportista->tipoDoc}','{$data->envio->transportista->choferDoc}','{$data->envio->transportista->rznSocial}','{$data->envio->transportista->placa}','{$data->observacion}','{$data->usuario}')";

        $stmt = mysqli_prepare($db,$sql);

         mysqli_stmt_execute($stmt);

         foreach($data->details as $valor){

          /*inserta detalle*/

         $proc="call p_guia_detalle({$data->correlativo},'{$valor->codigo}','{$valor->id}','{$valor->unidad}',{$valor->cantidad})";

         $stmt1 = mysqli_prepare($db,$proc);

         mysqli_stmt_execute($stmt1);

         }

            $result = array("STATUS"=>true,"messaje"=>"Guía registrada correctamente","sunat"=> $response_sunat->sunatResponse->cdrResponse->description);

         }else{

             $result = array("STATUS"=>false,"messaje"=>"Mensaje SUNAT","sunat"=>$response_sunat->sunatResponse->error->message);

         }

        }

         catch(PDOException $e) {



        $result = array("STATUS"=>false,"messaje"=>$e->getMessage());



    }



        echo  json_encode($result);

});







/*notas*/

$app->get("/notas",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

    $resultado = $db->query("SELECT v.id,v.codMotivo,if(v.tipDocAfectado='01','Factura','Boleta') tipDocAfectado,v.desMotivo,v.id_usuario,u.nombre usuario,c.id id_cliente,c.num_documento,c.direccion,concat(c.nombre,' ',c.apellido) cliente,igv,monto_igv,valor_neto,valor_total, estado, tipoDoc , if(tipoDoc= '07','Nota Credito','Nota Debito') NombreDoc,comprobante,nro_nota,nro_comprobante numDocfectado, DATE_FORMAT(v.fecha, '%d-%m-%y') fecha FROM notas v,usuarios u,clientes c where v.id_cliente=c.id and v.id_usuario=u.id and v.comprobante='Boleta' union all SELECT v.id,v.codMotivo,if(v.tipDocAfectado='01','Factura','Boleta') tipDocAfectado,v.desMotivo,v.id_usuario,u.nombre usuario,c.id id_cliente,c.num_documento,c.direccion,concat(c.razon_social) cliente,igv,monto_igv,valor_neto,valor_total, v.estado,tipoDoc,if(tipoDoc= '07','Nota Credito','Nota Debito') NombreDoc ,comprobante,nro_nota,nro_comprobante numDocfectado,DATE_FORMAT(v.fecha, '%Y-%m-%d') fecha FROM notas v,usuarios u,empresas c where v.id_cliente=c.id and v.id_usuario=u.id and v.comprobante='Factura' order by id desc;");

    $prods=array();

        while ($fila = $resultado->fetch_array()) {

            $prods[]=$fila;

        }

        $respuesta=json_encode($prods);

        echo  $respuesta;

});





$app->post("/nota",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

       $json = $app->request->getBody();

       $j = json_decode($json,true);

       $data = json_decode($j['json']);

       $valor_total=0;

       /*total de la venta*/



       foreach($data->detalleVenta as $value){

            $valor_total+=$value->cantidad*$value->mtoValorUnitario;

       }



      try {

        $fecha=substr($data->fecha,0,10);

        $sql="call p_nota('{$data->id_usuario}','{$data->cliente->id}','{$data->tipoDoc}','{$data->codMotivo}','{$data->desMotivo}','{$data->tipDocAfectado}', '{$data->comprobante}','{$data->numDocfectado}','{$data->nro_nota}','{$fecha}',{$valor_total},{$data->igv})";

        $stmt = mysqli_prepare($db,$sql);

        mysqli_stmt_execute($stmt);

        $datos=$db->query("SELECT max(id) ultimo_id FROM notas");

        $ultimo_id=array();

        while ($d = $datos->fetch_object()) {

         $ultimo_id=$d;

         }

        foreach($data->detalleVenta as $valor){



                     /*inserta detalle*/

        $proc="call p_nota_detalle({$ultimo_id->ultimo_id},'{$valor->codProducto->id}','{$valor->unidadmedida}',{$valor->cantidad},{$valor->peso},{$valor->mtoValorUnitario})";

        $stmt = mysqli_prepare($db,$proc);

        mysqli_stmt_execute($stmt);

        $stmt->close();



        }

        $result = array("STATUS"=>true,"messaje"=>"Nota registrada correctamente con el nro:".$ultimo_id->ultimo_id);



        }

         catch(PDOException $e) {



        $result = array("STATUS"=>false,"messaje"=>$e->getMessage());



    }



        echo  json_encode($result);

});



$app->get("/nota/:id",function($id) use($db,$app){

    header("Content-type: application/json; charset=utf-8");

    $resultado = $db->query("SELECT v.`id`, `id_producto`,p.codigo,p.`nombre`,`unidad_medida` ,`cantidad`,v.`peso` ,`precio`, `subtotal` FROM `nota_detalle` v ,productos p where v.id_producto=p.id and id_venta={$id}");

    $prods=array();

        while ($fila = $resultado->fetch_array()) {

            $prods[]=$fila;

        }

        $respuesta=json_encode($prods);

        echo  $respuesta;

});



     $app->post("/notacredito",function() use($db,$app){

        header("Content-type: application/json; charset=utf-8");

           $json = $app->request->getBody();

           $j = json_decode($json,true);

           $data = json_decode($j['json']);

                  try {

           $sql="call p_notacredito('{$data->hash}',{$data->sunatResponse->cdrResponse->code},'{$data->sunatResponse->cdrResponse->description}','{$data->sunatResponse->cdrResponse->id}','{$data->sunatResponse->cdrZip}','{$data->sunatResponse->success}')";

           $stmt = mysqli_prepare($db,$sql);

           mysqli_stmt_execute($stmt);

           $stmt->close();

           $datos=$db->query("SELECT max(id) ultimo_id FROM notascredito");

           $ultimo_id=array();

           while ($d = $datos->fetch_object()) {

            $ultimo_id=$d;

            }



            $result = array("STATUS"=>true,"messaje"=>"Nota  registrada correctamente","max"=>$ultimo_id);

            }

             catch(PDOException $e) {

                $result = array("STATUS"=>false,"messaje"=>$e->getMessage());

        }

            echo  json_encode($result);

     });





     $app->post("/boleta",function() use($db,$app){

        header("Content-type: application/json; charset=utf-8");

           $json = $app->request->getBody();

           $j = json_decode($json,true);

           $data = json_decode($j['json']);

                  try {

           $sql="call p_boleta('{$data->hash}',{$data->sunatResponse->cdrResponse->code},'{$data->sunatResponse->cdrResponse->description}','{$data->sunatResponse->cdrResponse->id}','{$data->sunatResponse->cdrZip}','{$data->sunatResponse->success}')";

           $stmt = mysqli_prepare($db,$sql);

           mysqli_stmt_execute($stmt);

           $stmt->close();

           $datos=$db->query("SELECT max(id) ultimo_id FROM boletas");

           $ultimo_id=array();

           while ($d = $datos->fetch_object()) {

            $ultimo_id=$d;

            }



            $result = array("STATUS"=>true,"messaje"=>"Boleta grabada correctamente","max"=>$ultimo_id);

            }

             catch(PDOException $e) {

                $result = array("STATUS"=>false,"messaje"=>$e->getMessage());

        }

            echo  json_encode($result);

     });





    $app->get("/correlativo/:tabla",function($tabla) use($db,$app){

        header("Content-type: application/json; charset=utf-8");

        $resultado = $db->query("SELECT max(id)+1 ultimo  FROM {$tabla}");



        $prods=array();

            while ($fila = $resultado->fetch_array()) {

                if($fila["ultimo"]==NULL){

                $prods[]=array(0=>1,"ultimo"=>1);

                }else{

                $prods[]=$fila;

                }

            }

            $respuesta=json_encode($prods);

            echo  $respuesta;

    });





    $app->get("/venta/:id",function($id) use($db,$app){

        header("Content-type: application/json; charset=utf-8");

        $resultado = $db->query("SELECT a.nombre, d.* FROM aprendea_erp.venta_detalle d,articulos a  where a.id=d.id_producto and id_venta={$id}");

        $prods=array();

            while ($fila = $resultado->fetch_array()) {

                $prods[]=$fila;

            }

            $respuesta=json_encode($prods);

            echo  $respuesta;

    });



/*clientes*/





$app->get("/clientes",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

    $resultado = $db->query("SELECT * FROM clientes order by id desc");

    $prods=array();

        while ($fila = $resultado->fetch_array()) {



            $prods[]=$fila;

        }

        $respuesta=json_encode($prods);

        echo  $respuesta;



});



$app->post("/cliente",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

       $json = $app->request->getBody();

       $j = json_decode($json,true);

       $data = json_decode($j['json']);

       try {

        $query ="INSERT INTO clientes (num_documento,nombre,telefono,direccion,email,departamento,provincia,distrito,estado)

        VALUES ('{$data->num_documento}','{$data->nombre}','{$data->telefono}','{$data->direccion}','{$data->email}','{$data->departamento}','{$data->provincia}','{$data->distrito}',1)";

        $db->query($query);

        $result = array("STATUS"=>true,"messaje"=>"Ciente registrado correctamente");

          }

         catch(PDOException $e) {

        $result = array("STATUS"=>false,"messaje"=>$e->getMessage());

    }

        echo  json_encode($result);

});



$app->get("/clientes/:criterio",function($criterio) use($db,$app){

    header("Content-type: application/json; charset=utf-8");

    $resultado = $db->query("SELECT `id`, `nombre`,`direccion`,`num_documento` FROM `clientes` where apellido like '%".$criterio."%' or nombre like '%".$criterio."%'");

    $prods=array();

        while ($fila = $resultado->fetch_array()) {



            $prods[]=$fila;

        }

        $respuesta=json_encode($prods);

        echo  $respuesta;



});



$app->get("/cliente/:id",function($id) use($db,$app){

    header("Content-type: application/json; charset=utf-8");

    $resultado = $db->query("SELECT `id`, `nombre`,`apellido`,`direccion`,`num_documento` FROM `clientes` where id={$id}");

    $prods=array();

        while ($fila = $resultado->fetch_array()) {



            $prods[]=$fila;

        }

        $respuesta=json_encode($prods);

        echo  $respuesta;



});



$app->put("/cliente",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

       $json = $app->request->getBody();

       $j = json_decode($json,true);

       $data = json_decode($j['json']);

       try {

        $query ="UPDATE clientes SET nombre='{$data->nombre}',direccion='{$data->direccion}',telefono='{$data->telefono}',num_documento='{$data->num_documento}',email='{$data->email}',departamento='{$data->departamento}',provincia='{$data->provincia}',distrito='{$data->distrito}' where id={$data->id}";

        $db->query($query);

        $result = array("STATUS"=>true,"messaje"=>"Ciente actualizado correctamente");

          }

         catch(PDOException $e) {

        $result = array("STATUS"=>false,"messaje"=>$e->getMessage());

    }

        echo  json_encode($result);

});





$app->post("/del_cliente",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

       $json = $app->request->getBody();

       $j = json_decode($json,true);

       $data = json_decode($j['json']);

                  $query ="DELETE FROM clientes WHERE id='{$data->cliente->id}'";

                  if($db->query($query)){

       $result = array("STATUS"=>true,"messaje"=>"Cliente eliminado correctamente");

       }

       else{

        $result = array("STATUS"=>false,"messaje"=>"Error al eliminar el cliente");

       }



        echo  json_encode($result);

    });



/*empresas*/



$app->get("/empresas/:criterio",function($criterio) use($db,$app){

    header("Content-type: application/json; charset=utf-8");

    $resultado = $db->query("SELECT `id`, `razon_social`,`num_documento`, `direccion`,`telefono`,`departamento`,`provincia`,`distrito`,`estado` FROM `empresas` where razon_social like '%".$criterio."%'");

    $prods=array();

        while ($fila = $resultado->fetch_array()) {



            $prods[]=$fila;

        }

        $respuesta=json_encode($prods);

        echo  $respuesta;



});



$app->delete("/empresa/:ruc",function($ruc) use($db,$app){

    header("Content-type: application/json; charset=utf-8");

       $json = $app->request->getBody();

       $j = json_decode($json,true);

       $data = json_decode($j['json']);

                  $query ="DELETE FROM empresas WHERE num_documento='{$ruc}'";

                  if($db->query($query)){

       $result = array("STATUS"=>true,"messaje"=>"Empresa eliminado correctamente");

       }

       else{

        $result = array("STATUS"=>false,"messaje"=>"Error al eliminar empresa");

       }



        echo  json_encode($result);

    });



    $app->put("/empresa",function() use($db,$app){

        header("Content-type: application/json; charset=utf-8");

           $json = $app->request->getBody();

           $j = json_decode($json,true);

           $data = json_decode($j['json']);

           try {

            $query ="UPDATE empresas SET razon_social='{$data->razon_social}',direccion='{$data->direccion}',telefono='{$data->telefono}',departamento='{$data->departamento}',provincia='{$data->provincia}',distrito='{$data->distrito}' where id={$data->id}";

            $db->query($query);

            $result = array("STATUS"=>true,"messaje"=>"Ciente actualizado correctamente");

              }

             catch(PDOException $e) {

            $result = array("STATUS"=>false,"messaje"=>$e->getMessage());

        }

            echo  json_encode($result);

    });



$app->post("/empresa",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

       $json = $app->request->getBody();

       $j = json_decode($json,true);

       $data = json_decode($j['json']);



       $query ="INSERT INTO empresas (razon_social, direccion, num_documento, departamento,provincia,distrito,estado) VALUES ("

      ."'{$data->razon_social}',"

      ."'{$data->direccion}',"

      ."'{$data->num_documento}',"

      ."'{$data->departamento}',"

      ."'{$data->provincia}',"

      ."'{$data->distrito}',"

      ."'{$data->estado}'".")";

         $exe=$db->query($query);

        if($exe){

        $result = array("STATUS"=>true,"messaje"=>"Cliente registrado correctamente");

        }else {

            $result = array("STATUS"=>false,"messaje"=>"Cliente no registrado correctamente");

        }



      echo  json_encode($result);

    });



    $app->get("/numeroletras/:cantidad",function($cantidad) use($db,$app){

        //header("Content-type: application/json; charset=utf-8");

        $json = file_get_contents("https://nal.azurewebsites.net/api/Nal?num={$cantidad}");

        $data = json_decode($json);

           echo json_encode($data->letras);

        });





$app->post("/bancosget",function() use($db,$app) {

header("Content-type: application/json; charset=utf-8");

    $json = $app->request->getBody();

    $data = json_decode($json, true);

      $datos=$db->query("SELECT * FROM api.dash_bancario WHERE usuario='{$data["empresa"]}'");

       $infocliente=array();

  while ($cliente = $datos->fetch_object()) {

            $infocliente[]=$cliente;

        }

        $return=array("data"=>$infocliente);



           echo  json_encode($return);

});







$app->post("/generalget",function() use($db,$app) {

header("Content-type: application/json; charset=utf-8");

    $json = $app->request->getBody();

    $data = json_decode($json, true);

      $datos=$db->query("SELECT * FROM api.dash_general WHERE empresa='{$data["empresa"]}'");

       $infocliente=array();

  while ($cliente = $datos->fetch_object()) {

            $infocliente[]=$cliente;

        }

        $return=array("data"=>$infocliente);



           echo  json_encode($return);

});





 $app->post("/banco",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

       $json = $app->request->getBody();

       $j = json_decode($json,true);

       $data = json_decode($j['json']);





        $empresa=(is_array($data->empresa))? array_shift($data->empresa): $data->empresa;

        $entidad=(is_array($data->entidad))? array_shift($data->entidad): $data->entidad;

        $beneficiario=(is_array($data->beneficiario))? array_shift($data->beneficiario): $data->beneficiario;

        $persona=(is_array($data->persona))? array_shift($data->persona): $data->persona;

        $dom_entidad=(is_array($data->dom_entidad))? array_shift($data->dom_entidad): $data->dom_entidad;

        $ciudad=(is_array($data->ciudad))? array_shift($data->ciudad): $data->ciudad;

        $sucursal=(is_array($data->sucursal))? array_shift($data->sucursal): $data->sucursal;

        $tipocuenta=(is_array($data->tipocuenta))? array_shift($data->tipocuenta): $data->tipocuenta;

        $numerocta=(is_array($data->numerocta))? array_shift($data->numerocta): $data->numerocta;

        $aba=(is_array($data->aba))? array_shift($data->aba): $data->aba;

        $swift=(is_array($data->swift))? array_shift($data->swift): $data->swift;

        $contactobco=(is_array($data->contactobco))? array_shift($data->contactobco): $data->contactobco;

        $tlfcontacto=(is_array($data->tlfcontacto))? array_shift($data->tlfcontacto): $data->tlfcontacto;

        $bancointer=(is_array($data->bancointer))? array_shift($data->bancointer): $data->bancointer;

        $abainter=(is_array($data->abainter))? array_shift($data->abainter): $data->abainter;







        $contar=array();

        $cantidad=$db->query("SELECT * FROM api.dash_bancario WHERE usuario='{$empresa}'");

  while ($cliente = $cantidad->fetch_array()) {

            $contar[]=$cliente;

        }





if(count($contar)>0){



     $query ="UPDATE api.dash_bancario  SET "

        ."entidad ='{$entidad}',"

        ."beneficiario = '{$beneficiario}',"

        ."persona = '{$persona}',"

        ."dom_entidad = '{$dom_entidad}',"

        ."ciudad = '{$ciudad}',"

        ."sucursal = '{$sucursal}',"

        ."tipocuenta= '{$tipocuenta}',"

        ."numerocta= '{$numerocta}',"

        ."aba = '{$aba}',"

        ."swift = '{$swift}',"

        ."contactobco = '{$contactobco}',"

        ."tlfcontacto = '{$tlfcontacto}',"

        ."bancointer = '{$bancointer}',"

        ."abainter = '{$abainter}'"

        ." WHERE usuario='{$empresa}'";



          $update=$db->query($query);





    }else{

        $query ="INSERT INTO api.dash_bancario (usuario,entidad,beneficiario,persona,dom_entidad,ciudad,sucursal,tipocuenta,numerocta,aba,swift,contactobco,

        tlfcontacto,bancointer,abainter) VALUES ("

      ."'{$empresa}',"

      ."'{$entidad}',"

      ."'{$beneficiario}',"

      ."'{$persona}',"

      ."'{$dom_entidad}',"

      ."'{$ciudad}',"

      ."'{$sucursal}',"

      ."'{$tipocuenta}',"

      ."'{$numerocta}',"

      ."'{$aba}',"

      ."'{$swift}',"

      ."'{$contactobco}',"

      ."'{$tlfcontacto}',"

      ."'{$bancointer}',"

      ."'{$abainter}'"

        .")";



      $insert=$db->query($query);

    }

       if(count($contar)>0){

       $result = array("STATUS"=>true,"messaje"=>"Datos actualizados correctamente");

        }else{

        $result = array("STATUS"=>false,"messaje"=>"Datos creados correctamente");

        }

        echo  json_encode($result);

    });







 $app->post("/general",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

       $json = $app->request->getBody();

       $j = json_decode($json,true);

       $data = json_decode($j['json']);





        $nombres=(is_array($data->nombres))? array_shift($data->nombres): $data->nombres;

        $correo=(is_array($data->correo))? array_shift($data->correo): $data->correo;

        $telefono=(is_array($data->telefono))? array_shift($data->telefono): $data->telefono;

        $sociedad=(is_array($data->sociedad))? array_shift($data->sociedad): $data->sociedad;

        $paginas=(is_array($data->paginas))? array_shift($data->paginas): $data->paginas;

        $rut=(is_array($data->rut))? array_shift($data->rut): $data->rut;

        $domicilio=(is_array($data->domicilio))? array_shift($data->domicilio): $data->domicilio;

        $calle=(is_array($data->calle))? array_shift($data->calle): $data->calle;

        $numero=(is_array($data->numero))? array_shift($data->numero): $data->numero;

        $ciudad=(is_array($data->ciudad))? array_shift($data->ciudad): $data->ciudad;

        $pais=(is_array($data->pais))? array_shift($data->pais): $data->pais;

        $confinanzas=(is_array($data->confinanzas))? array_shift($data->confinanzas): $data->confinanzas;

        $tlffinanzas=(is_array($data->tlffinanzas))? array_shift($data->tlffinanzas): $data->tlffinanzas;

        $correofinan=(is_array($data->correofinan))? array_shift($data->correofinan): $data->correofinan;

        $medios=(is_array($data->medios))? array_shift($data->medios): $data->medios;

        $empresa=$data->empresa;







        $contar=array();

        $cantidad=$db->query("SELECT * FROM api.dash_general WHERE empresa='{$empresa}'");

  while ($cliente = $cantidad->fetch_array()) {

            $contar[]=$cliente;

        }





if(count($contar)>0){



     $query ="UPDATE api.dash_general  SET "

        ."nombres ='{$nombres}',"

        ."correo = '{$correo}',"

        ."telefono = '{$telefono}',"

        ."sociedad = '{$sociedad}',"

        ."paginas = '{$paginas}',"

        ."rut = '{$rut}',"

        ."domicilio = '{$domicilio}',"

        ."calle = '{$calle}',"

        ."numero = '{$numero}',"

        ."ciudad = '{$ciudad}',"

        ."pais = '{$pais}',"

        ."confinanzas = '{$confinanzas}',"

        ."tlffinanzas = '{$tlffinanzas}',"

        ."correofinan = '{$correofinan}',"

        ."medios = '{$medios}'"

        ." WHERE empresa='{$empresa}'";



          $update=$db->query($query);





    }else{

        $query ="INSERT INTO api.dash_general (correo,empresa,nombres,telefono,sociedad,paginas,rut,domicilio,calle,numero,ciudad,pais,confinanzas,tlffinanzas,correofinan,medios) VALUES ("

      ."'{$correo}',"

      ."'{$empresa}',"

      ."'{$nombres}',"

      ."'{$telefono}',"

      ."'{$sociedad}',"

      ."'{$paginas}',"

      ."'{$rut}',"

      ."'{$domicilio}',"

      ."'{$calle}',"

      ."'{$numero}',"

      ."'{$ciudad}',"

      ."'{$pais}',"

      ."'{$confinanzas}',"

      ."'{$tlffinanzas}',"

      ."'{$correofinan}',"

      ."'{$medios}'"

          .")";



      $insert=$db->query($query);

    }

       if(count($contar)>0){

       $result = array("STATUS"=>true,"messaje"=>"Usuario actualizado correctamente");

        }else{

        $result = array("STATUS"=>false,"messaje"=>"Usuario creado correctamente");

        }

        echo  json_encode($result);

    });



/*login*/

   $app->post("/login",function() use($db,$app){

         $json = $app->request->getBody();

        $data = json_decode($json, true);



        $resultado = $db->query("SELECT * FROM usuarios where nombre='".$data['usuario']."' and contrasena='".$data['password']."'");

        $usuario=array();

        while ($fila = $resultado->fetch_object()) {

        $usuario[]=$fila;

        }

        if(count($usuario)==1){

            $data = array("status"=>true,"rows"=>1,"data"=>$usuario);

        }else{

            $data = array("status"=>false,"rows"=>0,"data"=>null);

        }

        echo  json_encode($data);

    });



    $app->get("/vendedor/:criterio",function($criterio) use($db,$app){

        header("Content-type: application/json; charset=utf-8");

        $resultado = $db->query("SELECT id, nombre,apellidos FROM `vendedor` where nombre like '%{$criterio}%'");

        $prods=array();

            while ($fila = $resultado->fetch_array()) {

                $prods[]=$fila;

            }

            $respuesta=json_encode($prods);

            echo  $respuesta;

        });

/*reporte productos*/



$app->get("/reportesubcategoria",function() use($db,$app){

    $json = $app->request->getBody();

       $dat = json_decode($json, true);

       $fechainicio= $dat["inicio"];

       $fechafin=$dat["fin"];

       $sucur=array();

       $result=$db->query("SELECT s.nombre,count(*) total from productos p,  sub_categorias s where p.id_subcategoria=s.id  group by 1 order by 2 desc");



      $datos=array();

       while ($filas = $result->fetch_array()){

               $datos[]=$filas;

           }

            $data = array("status"=>200,"data"=>$datos);



             echo  json_encode($data);



   });



/*dashboard adops*/



   $app->post("/reporte",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

    $json = $app->request->getBody();

    $dat = json_decode($json, true);

    $hash=$dat['emp'];

    $arraymeses=array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');

    $arraynros=array('01','02','03','04','05','06','07','08','09','10','11','12');

    $mes1=substr($dat['ini'], 0,3);

    $mes2=substr($dat['fin'], 0,3);

    $dia1=substr($dat['ini'], 3,2);

    $dia2=substr($dat['fin'], 3,2);

    $ano1=substr($dat['ini'], 5,4);

    $ano2=substr($dat['fin'], 5,4);

    $fmes1=str_replace($arraymeses,$arraynros,$mes1);

    $fmes2=str_replace($arraymeses,$arraynros,$mes2);

    $ini=$ano1.'-'.$fmes1.'-'.$dia1;

    $fin=$ano2.'-'.$fmes2.'-'.$dia2;

    $inicio=$dia1.'/'.$fmes1;

    $final=$dia2.'/'.$fmes2;



   $datocliente=$db->query("SELECT * FROM api.usuarios where hash='".$hash."'");

   $infocliente=array();

  while ($cliente = $datocliente->fetch_array()) {

            $infocliente[]=$cliente;

        }



        $tasa=(float) $infocliente[0]["tasa"];

        $emp=$infocliente[0]["empresa"];

        $cpm=(float) $infocliente[0]["cpm"];



        if($emp=='Latina.pe' and $fmes1=='08' and $fmes2=='08'){

            $tasa=1;

            $cpm=1;



        }



        $numeromes=(int)$fmes1;

        if($emp=='America Economia' and $numeromes>=10 and $ano1=='2020'){

            $tasa=0.70;

            $cpm=0.70;

        }





$ingreso=$db->query("SELECT FORMAT(sum(columnad_exchange_estimated_revenue*".$cpm.")/(sum(columnad_exchange_impressions))*1000,2) ingreso_cpm,FORMAT(ROUND(sum(columnad_exchange_estimated_revenue)*".$tasa.",2),2) ingreso_total ,FORMAT(sum(columnad_exchange_impressions),0) impresiones FROM adops.11223363888   where dimensionad_exchange_network_partner_name='".$emp."' and dimensiondate between '".$ini."' and '".$fin."'");

       $infoingreso=array();

  while ($row = $ingreso->fetch_array()) {

            $infoingreso[]=$row;

        }





              $resultado_desk = $db->query("SELECT concat(SUBSTRING(dimensiondate,6,2),'/',SUBSTRING(dimensiondate,9,2)) dimensiondate,FORMAT(sum(columnad_exchange_estimated_revenue)*".$tasa.",2) as total FROM adops.11223363888

    where  dimensionad_exchange_network_partner_name='".$emp."' and

    dimensiondate between '".$ini."' and '".$fin."' group by 1 order by 1 asc");

    $infodesk=array();

        while ($filadesk= $resultado_desk->fetch_array()) {



            $infodesk[]=$filadesk;

        }







          $resultado_table = $db->query("SELECT dimensiondate,round(sum(columnad_exchange_estimated_revenue),2)*".$tasa." as total FROM adops.11223363888

    where  dimensionad_exchange_network_partner_name='".$emp."' and

    dimensiondate between '".$ini."' and '".$fin."' and round(columnad_exchange_estimated_revenue,2)>0.00 and dimensionad_exchange_device_category='Tablets' group by 1 order by 1 asc");

    $infotablet=array();

        while ($filatab = $resultado_table->fetch_array()) {



            $infotablet[]=$filatab;

        }





          $resultado_mobil = $db->query("SELECT dimensiondate,round(sum(columnad_exchange_estimated_revenue),2)*".$tasa." as total FROM adops.11223363888

    where dimensionad_exchange_network_partner_name='".$emp."' and

    dimensiondate between '".$ini."' and '".$fin."' and round(columnad_exchange_estimated_revenue,2)>0.00 and dimensionad_exchange_device_category='High-end mobile devices' group by 1 order by 1 asc");

    $infomovil=array();

        while ($filamob = $resultado_mobil->fetch_array()) {



            $infomovil[]=$filamob;

        }





    $resultado = $db->query("SELECT REPLACE(dimensionad_exchange_device_category,'High-end mobile devices','Mobile') dimensionad_exchange_device_category,round(sum(columnad_exchange_estimated_revenue),2)*".$tasa." as total FROM adops.11223363888

    where  dimensionad_exchange_network_partner_name='".$emp."'  and

    dimensiondate between '".$ini."' and '".$fin."' and round(columnad_exchange_estimated_revenue,2)>0.00 group by 1 order by 2 desc");

    $info=array();

        while ($fila = $resultado->fetch_array()) {



            $info[]=$fila;

        }



     $result_creative = $db->query("SELECT dimensionad_exchange_creative_sizes,round(sum(columnad_exchange_estimated_revenue),2)*".$tasa." as total FROM adops.11223363888

    where dimensionad_exchange_network_partner_name='".$emp."'  and

    dimensiondate between '".$ini."' and '".$fin."' and round(columnad_exchange_estimated_revenue,2)>0.00 group by 1 order by 2 desc limit 5");

    $info_creative=array();

        while ($filac = $result_creative->fetch_array()) {



            $info_creative[]=$filac;

        }



        $data = array("status"=>200,"data"=>$info,"ingreso"=>$infoingreso,"creatives"=>$info_creative,"diario_desktop"=>$infodesk,"diario_tablet"=>$infotablet,"inicio"=>$inicio,"final"=>$final);

        echo json_encode($data);

        });





$app->post("/dashb",function() use($db,$app){

 $json = $app->request->getBody();

    $dat = json_decode($json, true);

    $suc=$dat["sucursal"];

    $fechainicio= $dat["inicio"];

    $fechafin=$dat["fin"];

    $sucur=array();



    $sucursales = $db->query("SELECT id,sucursal FROM dashboard.usuario where id<>1");

    while ($filatabla = $sucursales->fetch_array()) {

            $sucur[]=$filatabla;

        }



$sql="(SELECT pg.idOrden,UPPER(c.nombreCliente) nombreCliente,o.fechaCreado,o.idUsuario, u.sucursal,pg.pago1 AS pago, IF(tipoPago1=0,'EFECTIVO','TARJETA') modoPago,IF(o.Estado=0,'ENTREGA','RECOJO') Movimiento FROM  (SELECT * FROM dashboard.Pago WHERE fechaPago BETWEEN '".$fechainicio." 00:00:00' AND '".$fechafin. " 23:59:59'  AND pago1>0) pg INNER JOIN dashboard.Orden o ON o.idOrden=pg.idOrden AND o.tipoPago IN(1) AND o.estado=0 INNER JOIN dashboard.usuario u ON o.idUsuario=u.id INNER JOIN dashboard.Cliente c ON o.idCliente=c.idCliente AND u.id IN(" .$suc. ") ORDER BY modoPago) UNION ALL  (SELECT pg.idOrden,UPPER(c.nombreCliente) nombreCliente,pg.fechaActualizado AS fechaCreado,o.idUsuario, u.sucursal,pg.pago1 AS pago,IF(pg.tipoPago2=0,'EFECTIVO','TARJETA') modoPago, IF(o.Estado=0,'ENTREGA','RECOJO') Movimiento FROM (SELECT * FROM dashboard.Pago WHERE fechaPago BETWEEN '".$fechainicio." 00:00:00' AND '".$fechafin." 23:59:59' ) pg INNER JOIN dashboard.Orden o ON o.idOrden=pg.idOrden AND o.tipoPago IN(2) AND o.estado IN(0,1) INNER JOIN dashboard.usuario u ON o.idUsuario=u.id  INNER JOIN dashboard.Cliente c ON o.idCliente=c.idCliente AND  u.id IN(" .$suc. ") ORDER BY modoPago) UNION ALL  (SELECT pg.idOrden,UPPER(c.nombreCliente) nombreCliente,pg.fechaActualizado AS fechaCreado,o.idUsuario, u.sucursal, pg.pago2 AS pago ,IF(pg.tipoPago2=0,'EFECTIVO','TARJETA') modoPago, IF(o.Estado=0,'ENTREGA','RECOJO') Movimiento FROM (SELECT * FROM dashboard.Pago WHERE fechaActualizado BETWEEN '".$fechainicio." 00:00:00' AND '".$fechafin." 23:59:59') pg INNER JOIN dashboard.Orden o ON o.idOrden=pg.idOrden AND o.tipoPago IN(2) AND o.estado IN(1) INNER JOIN dashboard.usuario u ON o.idUsuario=u.id  INNER JOIN dashboard.Cliente c ON o.idCliente=c.idCliente AND u.id IN(". $suc.")  ORDER BY modoPago) ORDER BY modopago,idOrden";

 $result = $db->query($sql);



   $datos=array();

    while ($filas = $result->fetch_array()){

            $datos[]=$filas;

        }

         $data = array("status"=>200,"sucursal"=>$sucur,"sql"=>$sql,"data"=>$datos);



          echo  json_encode($data);







}) ;





$app->post("/inicio",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");

    $json = $app->request->getBody();

    $dat = json_decode($json, true);

    $date = new DateTime();

    $date2 = new DateTime();

    $date->modify('last day of this month');

    $date2->modify('first day of this month');

    $date->format('Y-m-d');

    $ini=substr( $date->format('Y-m-d'),0,7).'-01';

    $fin = substr($date->format('Y-m-d'),0,10);

    $inicio=$date2->format('d/m');

    $final=date("d/m",strtotime("- 1 days"));

    $hash=$dat['emp'];





    $datocliente=$db->query("SELECT * FROM api.usuarios where hash='".$hash."'");

       $infocliente=array();

      while ($cliente = $datocliente->fetch_array()) {

            $infocliente[]=$cliente;

        }



        $tasa=(float) $infocliente[0]["tasa"];

        $emp=$infocliente[0]["empresa"];

        $cpm=(float) $infocliente[0]["cpm"];



  $resultado_diario = $db->query("SELECT dimensiondate ,dimensionad_exchange_creative_sizes ,dimensionad_exchange_device_category  ,columnad_exchange_impressions ,columnad_exchange_estimated_revenue*".$tasa." columnad_exchange_estimated_revenue FROM adops.11223363888

    where dimensionad_exchange_network_partner_name='".$emp."' and

    dimensiondate between '".$ini."' and '".$fin."' and round(columnad_exchange_estimated_revenue,2)>0.00  order by 1 desc");

    $infotabla=array();

        while ($filatabla = $resultado_diario->fetch_array()) {



            $infotabla[]=$filatabla;

        }







  $resultado_diario = $db->query("SELECT dimensiondate,round(sum(columnad_exchange_estimated_revenue),2)*".$tasa." as total FROM adops.11223363888

    where dimensionad_exchange_network_partner_name='".$emp."' and

    dimensiondate between '".$ini."' and '".$fin."' and round(columnad_exchange_estimated_revenue,2)>0.00 group by 1 order by 1 asc");

    $infodia=array();

        while ($filadia = $resultado_diario->fetch_array()) {



            $infodia[]=$filadia;

        }





   $resultado_desk = $db->query("SELECT concat(SUBSTRING(dimensiondate,6,2),'/',SUBSTRING(dimensiondate,9,2)) dimensiondate,FORMAT(sum(columnad_exchange_estimated_revenue)*".$tasa.",2) as total FROM adops.11223363888

    where  dimensionad_exchange_network_partner_name='".$emp."' and

    dimensiondate between '".$ini."' and '".$fin."' group by 1 order by 1 asc");

    $infodesk=array();

        while ($filadesk= $resultado_desk->fetch_array()) {



            $infodesk[]=$filadesk;

        }







          $resultado_table = $db->query("SELECT dimensiondate,round(sum(columnad_exchange_estimated_revenue),2)*".$tasa." as total FROM adops.11223363888

    where dimensionad_exchange_network_partner_name='".$emp."' and

    dimensiondate between '".$ini."' and '".$fin."' and round(columnad_exchange_estimated_revenue,2)>0.00 and dimensionad_exchange_device_category='Tablets' group by 1 order by 1 asc");

    $infotablet=array();

        while ($filatab = $resultado_table->fetch_array()) {



            $infotablet[]=$filatab;

        }





          $resultado_mobil = $db->query("SELECT dimensiondate,round(sum(columnad_exchange_estimated_revenue),2)*".$tasa." as total FROM adops.11223363888

    where dimensionad_exchange_network_partner_name='".$emp."' and

    dimensiondate between '".$ini."' and '".$fin."' and round(columnad_exchange_estimated_revenue,2)>0.00 and dimensionad_exchange_device_category='High-end mobile devices' group by 1 order by 1 asc");

    $infomovil=array();

        while ($filamob = $resultado_mobil->fetch_array()) {



            $infomovil[]=$filamob;

        }







    $resultado = $db->query("SELECT REPLACE(dimensionad_exchange_device_category,'High-end mobile devices','Mobile') dimensionad_exchange_device_category,round(sum(columnad_exchange_estimated_revenue),2)*".$tasa." as total FROM adops.11223363888

    where dimensionad_exchange_network_partner_name='".$emp."'  and

    dimensiondate between '".$ini."' and '".$fin."' group by 1 order by 2 desc");

    $info=array();

        while ($fila = $resultado->fetch_array()) {



            $info[]=$fila;

        }



    $result_creative = $db->query("SELECT dimensionad_exchange_creative_sizes,round(sum(columnad_exchange_estimated_revenue),2)*".$tasa." as total FROM adops.11223363888

    where  dimensionad_exchange_network_partner_name='".$emp."'  and

    dimensiondate between '".$ini."' and '".$fin."' and round(columnad_exchange_estimated_revenue,2)>0.00 group by 1 order by 2 desc limit 5");

    $info_creative=array();

        while ($filac = $result_creative->fetch_array()) {



            $info_creative[]=$filac;

        }





       $ingreso=$db->query("SELECT FORMAT(sum(columnad_exchange_estimated_revenue*".$cpm.")/(sum(columnad_exchange_impressions))*1000,2) ingreso_cpm,FORMAT(ROUND(sum(columnad_exchange_estimated_revenue)*".$tasa.",2),2) ingreso_total,FORMAT(sum(columnad_exchange_impressions),0) impresiones  FROM adops.11223363888   where  dimensionad_exchange_network_partner_name='".$emp."' and dimensiondate between '".$ini."' and '".$fin."'");

       $infoingreso=array();

  while ($row = $ingreso->fetch_array()) {

            $infoingreso[]=$row;

        }



        $data = array("status"=>200,"data"=>$info,"ingreso"=>$infoingreso,"diario"=>$infodia,"diario_desktop"=>$infodesk,"diario_tablet"=>$infotablet,"diario_movil"=>$infomovil,"creatives"=>$info_creative,"inicio"=>$inicio,"final"=>$final);

        echo  json_encode($data);









    });





/*final adops dashobard*/





function traer_datos($ini,$fin,$emp,$tasa){

$db=new mysqli("localhost","marife","libido16","adops");



    $sql="SELECT ROUND(sum(columnad_exchange_ad_ecpm)*".$tasa.",2) ingreso_cpm,ROUND(sum(columnad_exchange_estimated_revenue)*".$tasa.",2) ingreso_total  FROM adops.11223363888   where dimensionad_exchange_network_partner_name='".$emp."' and dimensiondate between ".$ini." and ".$fin;



 $ingreso=$db->query($sql);



     $data=array();

       while ($row = $ingreso->fetch_array()) {

         $data[]=$row;

     }

        return $data;

}



function numero_letras(){

    header("Content-type: application/json; charset=utf-8");

    $json = file_get_contents("https://nal.azurewebsites.net/api/Nal?num={$cantidad}");

    $data = json_decode($json);

    return json_encode($data);



}



function ordena_fecha($inicio,$fin){

    $arraymeses=array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');

    $arraynros=array('01','02','03','04','05','06','07','08','09','10','11','12');

    $mes1=substr($inicio, 0,3);

    $mes2=substr($fin, 0,3);

    $dia1=substr($inicio, 3,2);

    $dia2=substr($fin, 3,2);

    $ano1=substr($inicio, 5,4);

    $ano2=substr($fin, 5,4);

    $fmes1=str_replace($arraymeses,$arraynros,$mes1);

    $fmes2=str_replace($arraymeses,$arraynros,$mes2);

    $ini=$ano1.'-'.$fmes1.'-'.$dia1;

    $fin=$ano2.'-'.$fmes2.'-'.$dia2;

    return array("inicio"=>$ini,"final"=>$fin);



}



$app->run();