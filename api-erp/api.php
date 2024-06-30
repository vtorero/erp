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



        $resultado = $db->query("SELECT *  FROM {$tabla} order by id asc");



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







        $sql="call p_cajas('{$data->nombre}','{$data->tipo}','{$data->id_sucursal}',1,'{$data->usuario}')";



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



/*Agregar vendedor*/





$app->post("/vendedor",function() use($db,$app){



    header("Content-type: application/json; charset=utf-8");



       $json = $app->request->getBody();



       $j = json_decode($json,true);



       $data = json_decode($j['json']);



       try {







        $sql="call p_vendedor('{$data->nombre}','{$data->correo}','{$data->rol}',{$data->estado})";



        $stmt = mysqli_prepare($db,$sql);



        mysqli_stmt_execute($stmt);



        $result = array("STATUS"=>true,"messaje"=>"Vendedor registrado correctamente");



        }



        catch(PDOException $e) {







        $result = array("STATUS"=>false,"messaje"=>$e->getMessage());







    }







             echo  json_encode($result);



});



/**listar vendedores */



$app->get("/vendedores",function() use($db,$app){



    header("Content-type: application/json; charset=utf-8");



    $resultado = $db->query("SELECT *  FROM vendedor order by id desc");



    $prods=array();



        while ($fila = $resultado->fetch_array()) {







            $prods[]=$fila;



        }



        $respuesta=json_encode($prods);



        echo  $respuesta;







    });

/**buscar clientes */



$app->get("/buscarclientes/:criterio",function($criterio) use($db,$app){
    header("Content-type: application/json; charset=utf-8");

    try{
    $resultado = $db->query("SELECT * FROM aprendea_erp.clientes where nombre like '%".$criterio."%' OR num_documento like '%".$criterio."%'");
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


    $app->get("/buscarproducto/:criterio",function($criterio) use($db,$app){
        header("Content-type: application/json; charset=utf-8");

        try{
        $resultado = $db->query("SELECT * FROM aprendea_erp.productos where nombre like '%".$criterio."%' OR id like '%".$criterio."%'");
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


/**buscar proveedor */





$app->get("/buscarproveedor/:criterio",function($criterio) use($db,$app){



    header("Content-type: application/json; charset=utf-8");



    try{



    $resultado = $db->query("SELECT * FROM aprendea_erp.proveedores where razon_social like '%".$criterio."%' OR num_documento like '%".$criterio."%'");



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



/*Agregar usuario*/



 $app->post("/usuario",function() use($db,$app){



    header("Content-type: application/json; charset=utf-8");



       $json = $app->request->getBody();



       $j = json_decode($json,true);



       $data = json_decode($j['json']);



       try {







        $sql="call p_usuario('{$data->nombre}','{$data->correo}','{$data->contrasena}','{$data->rol}',{$data->estado})";



        $stmt = mysqli_prepare($db,$sql);



        mysqli_stmt_execute($stmt);



        $result = array("STATUS"=>true,"messaje"=>"Usuario registrado correctamente");



        }



        catch(PDOException $e) {







        $result = array("STATUS"=>false,"messaje"=>$e->getMessage());







    }







             echo  json_encode($result);



});



/*get sucursal usuario**/



$app->get("/permisos/:uid",function($uid) use($db,$app){



    header("Content-type: application/json; charset=utf-8");



    $resultado = $db->query("SELECT s.id,s.nombre FROM aprendea_erp.permisos p inner join sucursales s on p.id_sucursal=s.id  where id_usuario={$uid}");



    $prods=array();



        while ($fila = $resultado->fetch_array()) {







            $prods[]=$fila;



        }



        $respuesta=json_encode($prods);



        echo  $respuesta;







    });





/**get cajas por usuario */

$app->get("/cajas/:uid",function($uid) use($db,$app){



    header("Content-type: application/json; charset=utf-8");



    $resultado = $db->query("SELECT c.id,c.nombre,c.tipo FROM cajas c inner JOIN permisos_caja p on c.id=p.id_caja and id_usuario='{$uid}'");



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

$palabras = explode(" ",$criterio);

$criterios="";
foreach ($palabras as $valor) {
    $criterios.= " p.nombre like '%".$valor."%' AND";
}

$refinado=substr($criterios, 0, -3);



    $resultado = $db->query("SELECT p.id,p.codigo,p.nombre,c.nombre categoria,sc.nombre subcategoria,fa.nombre familia, p.unidad,p.precio,p.imagen FROM productos p INNER join categorias c on p.id_categoria=c.id INNER join sub_categorias sc on p.id_subcategoria=sc.id INNER join sub_sub_categorias fa on p.id_sub_sub_categoria=fa.id and {$refinado} order by id;");



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
           $archivo = $data->imagen;
           $archivo = base64_decode($archivo);
           $filePath = $_SERVER['DOCUMENT_ROOT']."/erp-api/upload/".$data->nombre_imagen;
           file_put_contents($filePath, $archivo);

           try{
            $query = "INSERT INTO productos (`id_categoria`,`id_subcategoria`,`id_sub_sub_categoria`,`codigo`,`nombre`,`unidad`,`precio`,`imagen`)
             values({$data->id_categoria} ,{$data->id_subcategoria},{$data->id_familia},'{$data->codigo}','{$data->nombre}','{$data->unidad}',{$data->precio},'{$data->nombre_imagen}')";
            $proceso=$db->query($query);



            if($proceso){
                $datos=$db->query("SELECT max(id) ultimo_id FROM productos");
                $ultimo_id=array();
                while ($d = $datos->fetch_object()) {
                 $ultimo_id=$d;
                 }
            $db->query("INSERT INTO inventario (producto_id,id_almacen,cantidad,comentario)
            values({$ultimo_id->ultimo_id},1,0,'carga inicial')");
            $db->query("INSERT INTO inventario (producto_id,id_almacen,cantidad,comentario)
            values({$ultimo_id->ultimo_id},2,0,'carga inicial')");

            $db->query("INSERT INTO movimiento_articulos (codigo_prod,tipo_movimiento,cantidad_ingreso,cantidad_salida,cantidad_acumulada,precio,comentario,id_sucursal,usuario)
            values({$ultimo_id->ultimo_id},'Ingreso',0,0,0,0,'carga inicial',1,'admin')");
            $db->query("INSERT INTO movimiento_articulos (codigo_prod,tipo_movimiento,cantidad_ingreso,cantidad_salida,cantidad_acumulada,precio,comentario,id_sucursal,usuario)
            values({$ultimo_id->ultimo_id},'Ingreso',0,0,0,0,'carga inicial',2,'admin')");

           $result = array("STATUS"=>true,"messaje"=>"Producto creada correctamente");
            }else{



            $result = array("STATUS"=>false,"messaje"=>"Ocurrio un error en la creación");



            }
        }
        catch(PDOException $e){

            $result = array("STATUS"=>false,"messaje"=> $e->getMessage(),);
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



                $resultado = $db->query("SELECT id, codigo,nombre  FROM  unidad  order by nombre");



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





        /*buscar general*/



        $app->post("/buscargeneral", function() use($db,$app){

            header("Content-type: application/json; charset=utf-8");



            $json = $app->request->getBody();

            $j = json_decode($json,true);

            $data = json_decode($j['json']);



            if($data->tipo=='categoria'){

            $sql="SELECT * FROM aprendea_erp.productos where  id_categoria={$data->cat}";

            }



            if($data->tipo=='subcategoria'){

            $sql="SELECT * FROM aprendea_erp.productos where  id_categoria={$data->cat} and id_subcategoria={$data->sub}";

            }

            if($data->tipo=='familia'){

             $sql="SELECT * FROM aprendea_erp.productos where  id_categoria={$data->cat} and id_subcategoria={$data->sub} and id_sub_sub_categoria={$data->fam}";

            }



            $resultado = $db->query($sql);

            $prods=array();

            while ($fila = $resultado->fetch_array()) {

                $prods[]=$fila;

              }



               $respuesta=json_encode($prods);

              echo $respuesta;



            });









        $app->get("/familia/:criterio",function($criterio) use($db,$app){



            header("Content-type: application/json; charset=utf-8");



            $resultado = $db->query("SELECT categoria3 as nombre,id_sub_sub_categoria as id ,id_subcategoria from productos where id_subcategoria={$criterio} group by 1,2 order by 1 asc");



            $prods=array();



                while ($fila = $resultado->fetch_array()) {







                    $prods[]=$fila;



                }



                $respuesta=json_encode($prods);



                echo  $respuesta;







        });





        $app->get("/subcategoria/:criterio",function($criterio) use($db,$app){



            header("Content-type: application/json; charset=utf-8");



            $resultado = $db->query("SELECT categoria2 as nombre,id_subcategoria as id ,id_categoria from productos where id_categoria={$criterio} group by 1,2 order by 1 asc;");



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



    $resultado = $db->query("SELECT a.nombre, d.* FROM aprendea_erp.compra_detalle d,productos a  where a.id=d.id_producto and id_compra={$id}");



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


        $app->get("/inventario/:id",function($id) use($db,$app){

            header("Content-type: application/json; charset=utf-8");


            $resultado = $db->query("SELECT producto_id,a.nombre,id_almacen, s.nombre as almacen ,cantidad,fecha_actualizacion  FROM aprendea_erp.inventario i, productos a,sucursales s  where i.id_almacen=s.id and a.id=i.producto_id and i.id_almacen={$id}");

            $prods=array();



                while ($fila = $resultado->fetch_array()) {







                    $prods[]=$fila;



                }



                $respuesta=json_encode($prods);



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



$app->get("/inventario",function() use($db,$app){



    header("Content-type: application/json; charset=utf-8");



     $resultado = $db->query("SELECT producto_id,a.nombre,id_almacen, s.nombre as almacen ,cantidad,fecha_actualizacion  FROM aprendea_erp.inventario i, productos a,sucursales s  where i.id_almacen=s.id and a.id=i.producto_id");



    $prods=array();



        while ($fila = $resultado->fetch_array()) {



            $prods[]=$fila;



        }



        $respuesta=json_encode($prods);



        echo  $respuesta;



});





$app->get("/movimiento/:id",function($id) use($db,$app){



    header("Content-type: application/json; charset=utf-8");



    $resultado = $db->query("SELECT  p.id,p.nombre, m.tipo_movimiento,cantidad_ingreso,cantidad_salida,m.precio FROM aprendea_erp.movimiento_articulos m, productos p where m.codigo_prod=p.id and p.id={$id} order by id desc");



    $prods=array();



        while ($fila = $resultado->fetch_array()) {







            $prods[]=$fila;



        }



        $respuesta=json_encode($prods);



        echo  $respuesta;



    });


    $app->post("/kardex",function() use($db,$app){
        header("Content-type: application/json; charset=utf-8");
        $json = $app->request->getBody();
        $j = json_decode($json,true);
        $data = json_decode($j['json'],true);

        $arraymeses=array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
        $arraynros=array('01','02','03','04','05','06','07','08','09','10','11','12');
        $mes1=substr($data['inicio'], 0,3);
        $mes2=substr($data['fin'], 0,3);
        $dia1=substr($data['inicio'], 3,2);
        $dia2=substr($data['fin'], 3,2);
        $ano1=substr($data['inicio'], 5,4);
        $ano2=substr($data['fin'], 5,4);
        $fmes1=str_replace($arraymeses,$arraynros,$mes1);
        $fmes2=str_replace($arraymeses,$arraynros,$mes2);
        $ini=$ano1.'-'.$fmes1.'-'.$dia1.' 00:00:01';
        $fin=$ano2.'-'.$fmes2.'-'.$dia2.' 23:59:59';





$sql1="SELECT p.id,p.nombre,p.categoria from movimiento_articulos m, productos p where m.codigo_prod=p.id and m.fecha_registro between '{$ini}' and '{$fin}'";

if(isset($data["producto"]) &&  $data["producto"]!=""){
$sql1.=" and m.codigo_prod='{$data['producto']}' ";
}


$sql1.="group by 1 order by id asc";



        $resultado = $db->query($sql1);
       $prods=array();
       $detalle=array();
           while ($fila = $resultado->fetch_array()) {
            $fila['detalle'];
            $fila['promedio'];
            $fila['stock'];
            $fila['sql']=$sql1;


           $sql="SELECT @i := @i + 1 as contador ,`movimiento_articulos`.`id`,
           `movimiento_articulos`.`tipo_movimiento`,
           s.nombre as almacen,
           `movimiento_articulos`.`id_compra`,
           `movimiento_articulos`.`id_venta`,
           `movimiento_articulos`.`cantidad_acumulada`,
              u.nombre as unidad,
           `movimiento_articulos`.`cantidad_movimiento`,
           round(`movimiento_articulos`.`cantidad_acumulada`*`movimiento_articulos`.`promedio`,2) as p_total,
           `movimiento_articulos`.`cantidad_ingreso`,
           `movimiento_articulos`.`cantidad_salida`,
           `movimiento_articulos`.`precio`,
           `movimiento_articulos`.`promedio`,
           ROUND(`movimiento_articulos`.`cantidad_acumulada`*`movimiento_articulos`.`precio`,2) as costo,
           `movimiento_articulos`.`comentario`,

           DATE_FORMAT(`movimiento_articulos`.`fecha_registro`,'%d-%m-%Y') AS fecha_registro from movimiento_articulos, sucursales s,productos p ,unidad u
       cross join (select @i := 0) r where s.id=id_sucursal and p.unidad=u.codigo  and `movimiento_articulos`.codigo_prod=p.id  and `movimiento_articulos`.precio<>0 and codigo_prod={$fila['id']}";



                       if(isset($data["sucursal"]) && $data["sucursal"]!="0"){
                           $sql.=" and id_almacen={$data['sucursal']} ";
                           }
                if(isset($data["movimiento"])  && $data["movimiento"]!="0"){
                    $sql.=" and movimiento_articulos.tipo_movimiento='{$data['movimiento']}'";
                  }

                  if(isset($data["compra"]) &&  $data["compra"]!=""){
                    $sql.=" and movimiento_articulos.id_compra='{$data['compra']}' ";
                    }


                if(isset($data["venta"]) &&  $data["venta"]!=""){
                    $sql.=" and movimiento_articulos.id_venta='{$data['venta']}' ";
                }


                $sql.=" order by id desc";


            $resul_detalle = $db->query($sql);
            while ($filadet = $resul_detalle->fetch_array()) {
                $fila['detalle'][]=$filadet;
            }

//$sql_promedio="SELECT codigo_prod, ROUND(sum(cantidad_ingreso*precio)/sum(cantidad_ingreso),2) promedio from movimiento_articulos m where codigo_prod={$fila['id']} order by id desc";
            $sql_promedio="SELECT promedio,cantidad_acumulada,u.nombre as unidad from movimiento_articulos m,productos p, unidad u
            where  m.codigo_prod=p.id and p.unidad=u.codigo and  codigo_prod={$fila['id']} ";

if(isset($data["sucursal"]) && $data["sucursal"]!="0"){
    $sql_promedio.=" and m.id_almacen={$data['sucursal']} ";
    }

    $sql_promedio.=" order by m.id desc limit 1";

            $resul_promedio = $db->query($sql_promedio);
            while ($filaprod = $resul_promedio->fetch_array()) {
                $fila['promedio'][]=$filaprod;
            }

            $sql_costo_venta="SELECT sum(cantidad_movimiento*precio) costo from movimiento_articulos where precio<>0 and tipo_movimiento='Ingreso' and codigo_prod={$fila['id']} order by id limit 1";


            $resul_cv = $db->query($sql_costo_venta);
            while ($filacv = $resul_cv->fetch_array()) {
                $fila['costo_venta'][]=$filacv;
            }

            $sql_stock="SELECT codigo_prod,sum(cantidad_ingreso) cantidad FROM aprendea_erp.movimiento_articulos where codigo_prod={$fila['id']} ";
            if(isset($data['sucursal']) && $data['sucursal']!="0"){

                $sql_stock.=" and id_almacen={$data['sucursal']}";
            }

            $sql_stock.=" group by 1";



            $resul_stock = $db->query($sql_stock)->fetch_array();
            $fila['stock']=$resul_stock;
             $prods[]=$fila;
            }
           $respuesta=json_encode($prods);
           echo  $respuesta;
    });


$app->get("/movimientos",function() use($db,$app){
    header("Content-type: application/json; charset=utf-8");
    $resultado = $db->query("SELECT p.id,p.codigo,p.nombre,p.categoria from movimiento_articulos m, productos p where m.codigo_prod=p.id and (m.cantidad_ingreso>0 or cantidad_salida<0) group by 1 order by id asc limit 100;");
   $prods=array();
   $detalle=array();
       while ($fila = $resultado->fetch_array()) {
        $fila['detalle'];
        $fila['promedio'];
        $fila['stock'];
        $fila['total_entrada'];
        $fila['total_salida'];
        $fila['costo_venta'];


        $sql="SELECT @i := @i + 1 as contador ,`movimiento_articulos`.`id`,
        `movimiento_articulos`.`tipo_movimiento`,
        s.nombre as almacen,
        `movimiento_articulos`.`id_compra`,
        `movimiento_articulos`.`id_venta`,
        `movimiento_articulos`.`cantidad_acumulada`,
           u.nombre as unidad,
        `movimiento_articulos`.`cantidad_movimiento`,
        round(`movimiento_articulos`.`cantidad_acumulada`*`movimiento_articulos`.`promedio`,2) as p_total,
        `movimiento_articulos`.`cantidad_ingreso`,
        `movimiento_articulos`.`cantidad_salida`,
        `movimiento_articulos`.`precio`,
        `movimiento_articulos`.`promedio`,
        ROUND(`movimiento_articulos`.`cantidad_acumulada`*`movimiento_articulos`.`precio`,2) as costo,
        `movimiento_articulos`.`comentario`,

        DATE_FORMAT(`movimiento_articulos`.`fecha_registro`,'%d-%m-%Y') AS fecha_registro from movimiento_articulos, sucursales s,productos p ,unidad u
    cross join (select @i := 0) r where s.id=id_sucursal and p.unidad=u.codigo  and `movimiento_articulos`.codigo_prod=p.id  and `movimiento_articulos`.precio<>0 and codigo_prod={$fila['id']}  order by id asc;";

        $resul_detalle = $db->query($sql);
        while ($filadet = $resul_detalle->fetch_array()) {
            $fila['detalle'][]=$filadet;
        }


$sql_promedio="SELECT promedio,cantidad_acumulada,u.nombre as unidad from movimiento_articulos m,productos p, unidad u
where  m.codigo_prod=p.id and p.unidad=u.codigo and  codigo_prod={$fila['id']} order by m.id desc limit 1";

/*
$sql_promedio="SELECT * FROM
(SELECT
id,
tipo_movimiento,
cantidad_ingreso cantidad,
 precio,
if(costo_ingreso>0,costo_ingreso,'-') p_total,
 @acumulado_costo_ingreso := if( (tipo_movimiento)=(@tipo_movi)
                       , @acumulado_costo_ingreso
                       , ifnull( concat( @tipo_movi := tipo_movimiento , null), 0)) + costo_ingreso costo_acumulado_ingreso,
      @acumulado_ingreso := if( (tipo_movimiento)=(@tipo_movi)
                       , @acumulado_ingreso
                       , ifnull( concat( @tipo_movi := tipo_movimiento , null), 0)) + cantidad_ingreso acumulado_ingreso
                   , if(tipo_movimiento='Ingreso',round(@acumulado_costo_ingreso/@acumulado_ingreso,2),0) as promedio,
                   fecha_registro


  FROM (
SELECT id,tipo_movimiento,precio,cantidad_ingreso*precio as costo_ingreso,cantidad_ingreso,cantidad_salida*precio as costo_salida,cantidad_salida,fecha_registro from movimiento_articulos m where codigo_prod={$fila['id']} and tipo_movimiento in('Ingreso','Salida') order by fecha_registro asc) t
JOIN ( SELECT @acumulado_salida :=0,
				@acumulado_ingreso:=0,
                 @tipo_movi:= null

         ) vars )  as todo where todo.tipo_movimiento='Ingreso' order by ID DESC limit 1 ";

*/

        $resul_promedio = $db->query($sql_promedio);
        while ($filaprod = $resul_promedio->fetch_array()) {

        //    var_dump($filaprod['promedio']);
          //  var_dump((int)$filaprod['id']-1);
//            die();




            $fila['promedio'][]=$filaprod;

        }


        $sql_costo_venta="SELECT sum(cantidad_movimiento*precio) costo from movimiento_articulos where precio<>0 and tipo_movimiento='Ingreso' and codigo_prod={$fila['id']} order by id limit 1";


        $resul_cv = $db->query($sql_costo_venta);
        while ($filacv = $resul_cv->fetch_array()) {
            $fila['costo_venta'][]=$filacv;
        }

        $sql_te="SELECT sum(cantidad_ingreso*precio) total_salida from movimiento_articulos where tipo_movimiento='Ingreso' and codigo_prod={$fila['id']}";
        $resul_te = $db->query($sql_te);
        while ($filate = $resul_te->fetch_array()) {
            $fila['total_entrada'][]=$filate;
        }

        $sql_ts="SELECT SUM(cantidad_salida*m.precio) total_salida FROM aprendea_erp.movimiento_articulos m,sucursales s, productos p where  m.id_sucursal=s.id and m.codigo_prod=p.id and m.codigo_prod=p.id and p.id={$fila['id']}";
        $resul_ts = $db->query($sql_ts);
        while ($filats = $resul_ts->fetch_array()) {
            $fila['total_salida'][]=$filats;
        }


        $sql_cv="SELECT SUM((cantidad_salida*m.precio)-(cantidad_ingreso*m.precio)) costo_venta FROM aprendea_erp.movimiento_articulos m,sucursales s, productos p where  m.id_sucursal=s.id and m.codigo_prod=p.id and m.codigo_prod=p.id and p.id={$fila['id']}";
        $resul_cv = $db->query($sql_cv);
        while ($filacv = $resul_cv->fetch_array()) {
            $fila['costo_venta'][]=$filacv;
        }


        //$sql_stock="SELECT producto_id,sum(cantidad) cantidad from inventario where producto_id={$fila['id']} group by 1";
        $sql_stock="SELECT codigo_prod,sum(cantidad_ingreso)-sum(cantidad_salida) cantidad FROM aprendea_erp.movimiento_articulos where codigo_prod={$fila['id']}  group by 1";
        $resul_stock = $db->query($sql_stock)->fetch_array();
        $fila['stock']=$resul_stock;
         $prods[]=$fila;
        }


       $respuesta=json_encode($prods);
       echo  $respuesta;
});



/**listado compras */




$app->get("/compras",function() use($db,$app){



    header("Content-type: application/json; charset=utf-8");



     $resultado = $db->query("SELECT v.id,c.razon_social as cliente,u.nombre,v.tipoDoc,v.serie_documento,v.nro_documento,v.id_sucursal,DATE_FORMAT(v.fecha, '%d-%m-%Y') fecha,DATE_FORMAT(v.fecha_registro, '%d-%m-%Y') fechaPago,IF(v.pendientes=0,'No','Si') pendientes,v.igv,v.monto_igv,v.descuento,v.valor_neto,v.valor_total,v.monto_pendiente,v.observacion FROM compras v inner join proveedores c on v.id_proveedor=c.id inner join usuarios u on v.id_usuario=u.id order by 1 desc");



    $prods=array();



        while ($fila = $resultado->fetch_array()) {



            $prods[]=$fila;



        }



        $respuesta=json_encode($prods);



        echo  $respuesta;



});





/**listado ventas */

$app->get("/ventas",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");
     $resultado = $db->query("SELECT v.id,c.nombre as cliente,u.nombre,v.tipoDoc,v.id_vendedor,v.id_sucursal,DATE_FORMAT(v.fecha_registro, '%d-%m-%Y') fechaPago,IF(v.pendientes=0,'No','Si') pendientes,v.igv,v.monto_igv,v.descuento,v.valor_neto,v.valor_total,v.monto_pendiente, CASE WHEN v.estado ='1' THEN 'Registrado' WHEN v.estado = '2' THEN 'Anulado' END estado,v.observacion FROM ventas v inner join clientes c on v.id_cliente=c.id inner join usuarios u on v.id_usuario=u.id order by 1 desc");
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



    $app->post("/agregar-inventario",function() use($db,$app){

        header("Content-type: application/json; charset=utf-8");
        $json = $app->request->getBody();
        $j = json_decode($json,true);
        $data = json_decode($j['json']);

        $resultado = $db->query("SELECT * FROM aprendea_erp.movimiento_articulos where codigo_prod={$data->id_producto} and id_sucursal={$data->id_sucursal}  order by id desc limit 1");

        $inv = $resultado->fetch_array();

    if($data->operacion=='Ingreso'){

            if($inv["precio"]=="0.00" || $inv["cantidad_acumulada"]=="0.00"){

            if($inv["cantidad_acumulada"]=="0.00")  {
                $promedio=$data->precio;
            } else{
                $promedio=$data->cantidad/$data->precio;
            }

        $sql="INSERT INTO movimiento_articulos  (`codigo_prod`,`tipo_movimiento`,`id_almacen`,`comentario`,`cantidad_movimiento`,`cantidad_ingreso`,`cantidad_acumulada`,`precio`,`promedio`,`total`,`id_sucursal`,`usuario`)
 VALUES({$data->id_producto},'{$data->operacion}',{$data->id_sucursal},'{$data->comentario}',{$data->cantidad},{$data->cantidad},{$data->cantidad},{$data->precio},{$promedio},{$data->cantidad}*{$data->precio}, $data->id_sucursal,'{$data->usuario}');";
         }else{

            $cantidad_ingreso=$data->cantidad+floatval($inv['cantidad_ingreso']);
            $total=round(($data->cantidad*$data->precio)+$inv['total'],2);

            if(floatval($inv['cantidad_acumulada'])<=0){

                $promedio=(floatval($inv["total"])+($data->cantidad*$data->precio))/(floatval($inv['cantidad_acumulada'])+$data->cantidad);

            var_dump("ibtotal",floatval($inv["total"]));
            var_dump("cantidad x precio ingresado",$data->cantidad*$data->precio);
            var_dump("inv cantidad acumuladao",floatval($inv['cantidad_acumulada']));
            var_dump("cantidad acumulada antigua mas nueva",(floatval($inv['cantidad_acumulada'])+$data->cantidad));
            $cantidad_acumulada=(floatval($inv['cantidad_acumulada'])+$data->cantidad);
            }
            else{
                var_dump(floatval($inv["total"]));
                var_dump($data->cantidad*$data->precio);
                var_dump($data->cantidad);
                var_dump(floatval($inv['cantidad_acumulada']));
                var_dump((floatval($inv['cantidad_acumulada'])+$data->cantidad));
                var_dump("promedio2");


            $promedio=(floatval($inv["total"])+($data->cantidad*$data->precio))/($data->cantidad+floatval($inv['cantidad_acumulada']));

            $cantidad_ingreso=$data->cantidad;
            $cantidad_acumulada=$data->cantidad+floatval($inv['cantidad_acumulada']);
            }


        $sql="INSERT INTO movimiento_articulos  (`codigo_prod`,`tipo_movimiento`,`id_almacen`,`comentario`,`cantidad_movimiento`,`cantidad_ingreso`,`cantidad_acumulada`,`precio`,`promedio`,`total`,`id_sucursal`,`usuario`)
            VALUES({$data->id_producto},'{$data->operacion}',{$data->id_sucursal},'{$data->comentario}',$data->cantidad,{$cantidad_ingreso},{$cantidad_acumulada},{$data->precio},{$promedio},{$total}, $data->id_sucursal,'{$data->usuario}')";

        }


        $stmt2 = mysqli_prepare($db,$sql);
        mysqli_stmt_execute($stmt2);
        $stmt2->close();

        /*atualiza tabla inventarios*/
        $sql2="UPDATE inventario  SET cantidad = cantidad+{$data->cantidad},fecha_actualizacion=now() WHERE  producto_id={$data->id_producto} and id_almacen={$data->id_sucursal}";
        $db->query($sql2);
        /*fin inventario*/
        }
        if($data->operacion=='Salida'){

            if($inv["precio"]!="0.00"){

            $total=number_format($inv["total"]-($data->cantidad*$inv["promedio"]),2, '.', '');

                    $cantidad_acumulada=$inv["cantidad_acumulada"]-$data->cantidad;

            $sql="INSERT INTO movimiento_articulos  (`codigo_prod`,`tipo_movimiento`,`id_almacen`,`comentario`,`cantidad_movimiento`,`cantidad_salida`,`cantidad_acumulada`,`precio`,`promedio`,`total`,`id_sucursal`,`usuario`)
         VALUES({$data->id_producto},'{$data->operacion}',{$data->id_sucursal},'{$data->comentario}',{$data->cantidad},-{$data->cantidad},{$cantidad_acumulada},{$inv["promedio"]},{$inv["promedio"]},$total,$data->id_sucursal,'{$data->usuario}');";

            }

            $sql2="UPDATE inventario  SET cantidad = cantidad-{$data->cantidad},fecha_actualizacion=now() WHERE  producto_id={$data->id_producto} and id_almacen={$data->id_sucursal}";
            $db->query($sql2);


            $stmt3 = mysqli_prepare($db,$sql);
            mysqli_stmt_execute($stmt3);
            $stmt3->close();
        }


        $result = array("STATUS"=>true,"messaje"=>"Inventario registrado correctamente","consulta"=>$sql);

        echo  json_encode($result);



    });




    $app->post("/anular",function() use($db,$app){
        header("Content-type: application/json; charset=utf-8");
        $json = $app->request->getBody();
        $j = json_decode($json,true);
        $data = json_decode($j['json']);
        if($data->datos->estado!='Anulado'){
        $sql="UPDATE ventas set estado=2 where id={$data->datos->id}";
        $stmt2 = mysqli_prepare($db,$sql);
         mysqli_stmt_execute($stmt2);
        $stmt2->close();

        $sql2="SELECT * FROM venta_detalle where id={$data->datos->id}";
        $resultado = $db->query($sql2);
        $prods=array();
        while ($fila = $resultado->fetch_array()) {
            $sqla="INSERT INTO movimiento_articulos  (`codigo_prod`,`id_venta`,`tipo_movimiento`,`id_almacen`,`cantidad_ingreso`,`precio`,`comentario`,`id_sucursal`,`usuario`)
            VALUES({$fila['id_producto']},{$fila['id_venta']},'Ingreso',{$fila['id_inventario']},{$fila['cantidad']},{$fila['precio']},'Venta anulada',{$data->datos->id_sucursal},'admin');";



        $sqlb="UPDATE inventario  SET cantidad = cantidad+{$fila['cantidad']},fecha_actualizacion=now() WHERE  producto_id={$fila['id_producto']} and id_almacen={$data->datos->id_sucursal}";
           $stmt2 = mysqli_prepare($db,$sqla);
           $stmt3 = mysqli_prepare($db,$sqlb);
           mysqli_stmt_execute($stmt2);
           mysqli_stmt_execute($stmt3);
            }



        $result = array("STATUS"=>true,"messaje"=>"Ticket nro ".$data->datos->id . " fue anulado correctamente");
    }else{
        $result = array("STATUS"=>true,"messaje"=>"El ticket ".$data->datos->id." Ya esta anulado");
    }

        echo  json_encode($result);
    });



    $app->post("/facturar",function() use($db,$app){
        header("Content-type: application/json; charset=utf-8");
        $json = $app->request->getBody();
        $j = json_decode($json,true);
        $data = json_decode($j['json']);
        if($data->datos->tipoDoc!='Factura'){
        $sql="UPDATE ventas set valor_neto=(valor_total/1.18), igv=monto_igv=(valor_total/1.18)*0.18, monto_igv=(valor_total/1.18)*0.18,tipoDoc='Factura' where id={$data->datos->id}";
        $stmt2 = mysqli_prepare($db,$sql);
         mysqli_stmt_execute($stmt2);
        $stmt2->close();
    }else{
        $result = array("STATUS"=>true,"messaje"=>"Ya es una Factura ");
    }
        $result = array("STATUS"=>true,"messaje"=>"Venta Factura correctamente");
        echo  json_encode($result);
    });


/**Guardar Compra */
$app->post("/compra",function() use($db,$app){

    header("Content-type: application/json; charset=utf-8");
    $json = $app->request->getBody();
    $j = json_decode($json,true);
    $data = json_decode($j['json']);
    $detalle = json_decode($j['detalle']);
    $fecha=substr($data->fecha,0,10);
    $valor_total=0;
            try {
                $almacen=$data->sucursal;
               $sql="call p_compra('{$data->usuario}','{$data->seriedoc}','{$data->nrodocumento}','{$fecha}','{$data->proveedor}',{$data->sucursal},'{$data->entrega}','{$data->tipoDoc}',{$data->neto},{$data->total},{$data->montopendiente},{$data->total}-{$data->neto},'{$data->comentario}')";

               $stmt = mysqli_prepare($db,$sql);
                mysqli_stmt_execute($stmt);
                $datos=$db->query("SELECT max(id) ultimo_id FROM compras");
                $ultimo_id=array();
                while ($d = $datos->fetch_object()) {
                 $ultimo_id=$d;
                 }
                 foreach($data->pagos as $pago){
                $procP="call p_compra_pago({$ultimo_id->ultimo_id},'{$pago->tipoPago}','{$pago->numero}','{$pago->cuentaPago}',{$data->total},{$data->montopendiente})";
                $stmtP = mysqli_prepare($db,$procP);
                mysqli_stmt_execute($stmtP);
                 }

                foreach($detalle as $item){
                /*inserta detalla*/

                $proc="call p_compra_detalle({$ultimo_id->ultimo_id},{$item->id},{$item->id},'',{$item->cantidad},{$item->pendiente},{$item->descuento},{$item->precio})";
                $stmt = mysqli_prepare($db,$proc);
                mysqli_stmt_execute($stmt);
                $stmt->close();


                //$sql="INSERT INTO movimiento_articulos  (`codigo_prod`,`id_compra`,`tipo_movimiento`,`id_almacen`,`cantidad_ingreso`,`precio`,`comentario`,`id_sucursal`,`usuario`)
                //VALUES({$item->id},{$ultimo_id->ultimo_id},'Ingreso',{$data->almacen},{$item->cantidad}-{$item->pendiente},$item->precio,'{$data->comentario}',$data->sucursal,'{$data->usuario}');";

                $resultado = $db->query("SELECT * FROM aprendea_erp.movimiento_articulos where codigo_prod={$item->id} and id_sucursal={$almacen}  order by id desc limit 1");
                $inv = $resultado->fetch_array();



                if($inv["precio"]=="0.00" || $inv["cantidad_acumulada"]=="0.00"){

                    if($inv["cantidad_acumulada"]=="0.00")  {
                        $promedio=$item->precio;
                    } else{
                        $promedio=$item->cantidad/$item->precio;
                    }

                $sql="INSERT INTO movimiento_articulos  (`codigo_prod`,`id_compra`,`tipo_movimiento`,`id_almacen`,`comentario`,`cantidad_movimiento`,`cantidad_ingreso`,`cantidad_acumulada`,`precio`,`promedio`,`total`,`id_sucursal`,`usuario`)
         VALUES({$item->id},{$ultimo_id->ultimo_id},'Ingreso',{$almacen},CONCAT('compra nro:',$ultimo_id->ultimo_id),{$item->cantidad},{$item->cantidad},{$item->cantidad},{$item->precio},{$promedio},{$item->cantidad}*{$item->precio}, $almacen,'{$data->usuario}');";
                 }else{

                    $cantidad_ingreso=$item->cantidad+floatval($inv['cantidad_ingreso']);
                    $total=round(($item->cantidad*$item->precio)+$inv['total'],2);

                    if(floatval($inv['cantidad_acumulada'])<=0){

                        $promedio=(floatval($inv["total"])+($item->cantidad*$item->precio))/(floatval($inv['cantidad_acumulada'])+$item->cantidad);
/*
                    var_dump("ibtotal",floatval($inv["total"]));
                    var_dump("cantidad x precio ingresado",$data->item*$item->precio);
                    var_dump("inv cantidad acumuladao",floatval($inv['cantidad_acumulada']));
                    var_dump("cantidad acumulada antigua mas nueva",(floatval($inv['cantidad_acumulada'])+$item->cantidad));
                    $cantidad_acumulada=(floatval($inv['cantidad_acumulada'])+$item->cantidad);
                    */
                    }
                    else{
                       /* var_dump(floatval($inv["total"]));
                        var_dump($data->cantidad*$item->precio);
                        var_dump($item->cantidad);
                        var_dump(floatval($inv['cantidad_acumulada']));
                        var_dump((floatval($inv['cantidad_acumulada'])+$item->cantidad));
                        var_dump("promedio2");
*/

                    $promedio=(floatval($inv["total"])+($item->cantidad*$item->precio))/($item->cantidad+floatval($inv['cantidad_acumulada']));

                    $cantidad_ingreso=$item->cantidad;
                    $cantidad_acumulada=$item->cantidad+floatval($inv['cantidad_acumulada']);
                    }


                $sql="INSERT INTO movimiento_articulos  (`codigo_prod`,`id_compra`,`tipo_movimiento`,`id_almacen`,`comentario`,`cantidad_movimiento`,`cantidad_ingreso`,`cantidad_acumulada`,`precio`,`promedio`,`total`,`id_sucursal`,`usuario`)
                    VALUES({$item->id},{$ultimo_id->ultimo_id},'Ingreso',{$almacen},CONCAT('compra nro:',$ultimo_id->ultimo_id),$item->cantidad,{$cantidad_ingreso},{$cantidad_acumulada},{$item->precio},{$promedio},{$total}, $almacen,'{$data->usuario}')";

                }


                $sql2="UPDATE inventario  SET cantidad = cantidad+{$item->cantidad}-{$item->pendiente},fecha_actualizacion=now() WHERE  producto_id={$item->id} and id_almacen={$almacen}";



                $stmt2 = mysqli_prepare($db,$sql);
                $stmt3 = mysqli_prepare($db,$sql2);
                mysqli_stmt_execute($stmt2);
                mysqli_stmt_execute($stmt3);
                $stmt2->close();
                $stmt3->close();
                //$actualiza="call p_actualiza_inventario({$valor->codProductob->id},{$valor->codProducto},{$valor->cantidad},{$valor->peso},'{$valor->unidadmedida}')";
                //$stmtb = mysqli_prepare($db,$actualiza);
                //mysqli_stmt_execute($stmtb);
                //$stmtb->close();
                }





                   $result = array("STATUS"=>true,"messaje"=>"Compra registrada correctamente con el número: ".$ultimo_id->ultimo_id);



                }
                 catch(PDOException $e) {
                $result = array("STATUS"=>false,"messaje"=>$e->getMessage());

            }

        echo  json_encode($result);



});







/**guardar venta */



    $app->post("/venta",function() use($db,$app){



        header("Content-type: application/json; charset=utf-8");
        $json = $app->request->getBody();
        $j = json_decode($json,true);
        $data = json_decode($j['json']);
        $detalle = json_decode($j['detalle']);
        $valor_total=0;
                try {
                   $sql="call p_venta('{$data->usuario}','{$data->vendedor}','{$data->cliente}',{$data->sucursal},'{$data->entrega}','{$data->tipoDoc}',{$data->neto},{$data->total},{$data->montopendiente},{$data->total}-{$data->neto},'{$data->comentario}')";
                   $stmt = mysqli_prepare($db,$sql);
                    mysqli_stmt_execute($stmt);
                    $datos=$db->query("SELECT max(id) ultimo_id FROM ventas");
                    $ultimo_id=array();
                    while ($d = $datos->fetch_object()) {
                     $ultimo_id=$d;
                     }
                     foreach($data->pagos as $pago){
                    $procP="call p_venta_pago({$ultimo_id->ultimo_id},'{$pago->tipoPago}','{$pago->numero}','{$pago->cuentaPago}',{$data->total},{$data->montopendiente})";
                    $stmtP = mysqli_prepare($db,$procP);
                    mysqli_stmt_execute($stmtP);
                     }

                    foreach($detalle as $item){
                    /*inserta detalla*/
                    $proc="call p_venta_detalle({$ultimo_id->ultimo_id},{$item->id},{$item->id},'{$item->codigo}','',{$item->cantidad},{$item->pendiente},{$item->descuento},{$item->precio})";
                    $stmt = mysqli_prepare($db,$proc);
                    mysqli_stmt_execute($stmt);
                    $stmt->close();


                     $resultado = $db->query("SELECT * FROM aprendea_erp.movimiento_articulos where codigo_prod={$item->id} and id_sucursal={$data->sucursal}  order by id desc limit 1");
                     $inv = $resultado->fetch_array();


                     if($inv["precio"]!="0.00"){

                        $total=number_format($inv["total"]-($item->cantidad*$inv["promedio"]),2, '.', '');

                                $cantidad_acumulada=$inv["cantidad_acumulada"]-$item->cantidad;

                        $sql="INSERT INTO movimiento_articulos  (`codigo_prod`,`id_venta`,`tipo_movimiento`,`id_almacen`,`comentario`,`cantidad_movimiento`,`cantidad_salida`,`cantidad_acumulada`,`precio`,`promedio`,`total`,`id_sucursal`,`usuario`)
                        VALUES({$item->id},{$ultimo_id->ultimo_id},'Salida',{$data->sucursal},CONCAT('vta. nro: ',$ultimo_id->ultimo_id),{$item->cantidad},-{$item->cantidad},{$cantidad_acumulada},{$inv["promedio"]},{$inv["promedio"]},$total,$data->sucursal,'{$data->usuario}');";

                        }

                        if($inv["precio"]=="0.00" and $inv["promedio"]=="0.00"){

                            $total=number_format($item->cantidad*$item->precio,2, '.', '');

                                    $cantidad_acumulada=-$item->cantidad;

                            $sql="INSERT INTO movimiento_articulos  (`codigo_prod`,`id_venta`,`tipo_movimiento`,`id_almacen`,`comentario`,`cantidad_movimiento`,`cantidad_salida`,`cantidad_acumulada`,`precio`,`promedio`,`total`,`id_sucursal`,`usuario`)
                            VALUES({$item->id},{$ultimo_id->ultimo_id},'Salida',{$data->sucursal},CONCAT('vta. nro: ',$ultimo_id->ultimo_id),{$item->cantidad},-{$item->cantidad},{$cantidad_acumulada},{$item->precio},{$item->precio},$total,$data->sucursal,'{$data->usuario}');";

                            }

                        $sql2="UPDATE inventario  SET cantidad = cantidad-{$item->cantidad},fecha_actualizacion=now() WHERE  producto_id={$item->id} and id_almacen={$data->sucursal}";
                        $db->query($sql2);



                    $stmt2 = mysqli_prepare($db,$sql);
                      mysqli_stmt_execute($stmt2);
                                    $stmt2->close();

                    }


                       $result = array("STATUS"=>true,"numero"=>$ultimo_id->ultimo_id,"messaje"=>"Venta registrada correctamente con el número: ".$ultimo_id->ultimo_id);

                    }
                     catch(PDOException $e) {
                    $result = array("STATUS"=>false,"messaje"=>$e->getMessage());

                }
                echo  json_encode($result);


    });


    $app->post("/actualiza-monto-compra",function() use($db,$app){

        header("Content-type: application/json; charset=utf-8");
        $json = $app->request->getBody();
        $j = json_decode($json,true);
        $data = json_decode($j['json']);
        try {

            $qwmax=$db->query("SELECT monto_pendiente from compra_pagos where id_compra={$data->id_venta} order by id desc limit 1");

            $prods=array();
            while ($fila = $qwmax->fetch_array()) {
                $prods[]=$fila;

            }


            if($prods[0]["monto_pendiente"]>=$data->monto){

            $query ="INSERT INTO compra_pagos (`id_compra`,`tipoPago`,`numero_operacion`,`cuentaPago`,`monto`,`monto_pendiente`,`estado`)
             VALUES({$data->id_venta},{$data->tipo_pago},{$data->numero},{$data->cuenta_pago},{$data->monto},{$prods[0]["monto_pendiente"]}-{$data->monto},1)";
            $db->query($query);

             $query2 ="UPDATE compras SET monto_pendiente=({$prods[0]["monto_pendiente"]}-{$data->monto}) where id={$data->id_venta}";

            $db->query($query2);

            /*if($data->monto-$prods[0]["monto_pendiente"]==0){
                $query3 ="UPDATE compra_pagos SET monto_pendiente=0 where id={$data->id_venta}";
                $db->query($query3);

            }*/


            $result = array("STATUS"=>true,"messaje"=>"Monto Pendientes actualizados correctamente");
}else{

    if(($prods[0]["monto_pendiente"]-$data->monto)<0){

        $result = array("STATUS"=>true,"messaje"=>"La cantidad es mayor al saldo o ya fue pagado");
    }else{
        $result = array("STATUS"=>true,"messaje"=>"Ya no existe monto pendiente");
        }
}
    }



             catch(PDOException $e) {



            $result = array("STATUS"=>false,"messaje"=>$e->getMessage());



        }



            echo  json_encode($result);

    });





    $app->post("/actualiza-monto",function() use($db,$app){

        header("Content-type: application/json; charset=utf-8");
        $json = $app->request->getBody();
        $j = json_decode($json,true);
        $data = json_decode($j['json']);
        try {

            $qwmax=$db->query("SELECT monto_pendiente from venta_pagos where id_venta={$data->id_venta} order by id desc limit 1");

            $prods=array();
            while ($fila = $qwmax->fetch_array()) {
                $prods[]=$fila;

            }


            if($prods[0]["monto_pendiente"]>=$data->monto){

            $query ="INSERT INTO venta_pagos (`id_venta`,`tipoPago`,`numero_operacion`,`cuentaPago`,`monto`,`monto_pendiente`,`estado`)  VALUES({$data->id_venta},{$data->tipo_pago},{$data->numero},{$data->cuenta_pago},{$data->monto},{$prods[0]["monto_pendiente"]}-{$data->monto},1)";
            $db->query($query);

             $query2 ="UPDATE ventas SET monto_pendiente=({$prods[0]["monto_pendiente"]}-{$data->monto}) where id={$data->id_venta}";

            $db->query($query2);

            if($data->monto-$prods[0]["monto_pendiente"]==0){
                $query3 ="UPDATE venta_pagos SET monto_pendiente=0 where id={$data->id_venta}";
                $db->query($query3);

            }


            $result = array("STATUS"=>true,"messaje"=>"Monto Pendientes actualizados correctamente");
}else{

    if(($prods[0]["monto_pendiente"]-$data->monto)<0){

        $result = array("STATUS"=>true,"messaje"=>"La cantidad ingresada es mayor al saldo");
    }else{
        $result = array("STATUS"=>true,"messaje"=>"Ya no existe monto pendiente");
        }
}
    }



             catch(PDOException $e) {



            $result = array("STATUS"=>false,"messaje"=>$e->getMessage());



        }



            echo  json_encode($result);

    });





    $app->post("/actualiza-pendiente-compra",function() use($db,$app){

        header("Content-type: application/json; charset=utf-8");
        $json = $app->request->getBody();
        $j = json_decode($json,true);
        $data = json_decode($j['json']);
        $resultado = $db->query("SELECT  d.* FROM aprendea_erp.compra_detalle d where id={$data->id} and id_compra={$data->id_venta}");
        $prods=array();

    while ($fila = $resultado->fetch_array()) {
        $prods[]=$fila;
    }

    $sql="INSERT INTO salidas_articulos  (`codigo`,`id_venta`,`cantidad`,`id_sucursal`,`usuario`)  VALUES({$prods[0]['id_producto']},{$prods[0]['id_compra']},{$prods[0]['pendiente']},$data->sucursal,'{$data->usuario}')";

    $stmt2 = mysqli_prepare($db,$sql);
    mysqli_stmt_execute($stmt2);
    $stmt2->close();

    try {

        $query ="UPDATE compra_detalle SET pendiente={$data->cantidad} where id_compra={$data->id_venta} and id_producto={$data->id_producto}";

        $db->query($query);

        $result = array("STATUS"=>true,"messaje"=>"Pendientes actualizados correctamente");

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



/**pagos compra */



$app->get("/pagos-compra/:id",function($id) use($db,$app){



    header("Content-type: application/json; charset=utf-8");



    $resultado = $db->query("SELECT  p.*, tp.nombre, c.nombre as caja FROM aprendea_erp.compra_pagos p , tipoPago tp,cajas c   where p.tipoPago=tp.id and p.cuentaPago=c.id and  id_compra={$id}");

     $prods=array();



        while ($fila = $resultado->fetch_array()) {





            $prods[]=$fila;



        }



        $respuesta=json_encode($prods);



        echo  $respuesta;



});





/**pagos venta */

    $app->get("/pagos/:id",function($id) use($db,$app){



        header("Content-type: application/json; charset=utf-8");



        $resultado = $db->query("SELECT  p.*, tp.nombre, c.nombre as caja FROM aprendea_erp.venta_pagos p , tipoPago tp,cajas c   where p.tipoPago=tp.id and p.cuentaPago=c.id and  id_venta={$id}");

         $prods=array();



            while ($fila = $resultado->fetch_array()) {





                $prods[]=$fila;



            }



            $respuesta=json_encode($prods);



            echo  $respuesta;



    });





    $app->get("/venta/:id",function($id) use($db,$app){



        header("Content-type: application/json; charset=utf-8");



        $resultado = $db->query("SELECT a.nombre, d.* FROM aprendea_erp.venta_detalle d,productos a  where a.id=d.id_producto and id_venta={$id}");

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



    $resultado = $db->query("SELECT * FROM clientes order by nombre asc");



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



    $resultado = $db->query("SELECT `id`, `nombre`,`direccion`,`num_documento` FROM `clientes` where id = {$criterio}");

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
        $resultado = $db->query("SELECT u.*,s.id id_sucursal, s.nombre sucursal,s.direccion,s.telefono  FROM usuarios u, permisos p, sucursales s where u.id=p.id_usuario and p.id_sucursal=s.id and p.estado=1 and u.nombre='".$data['usuario']."' and u.contrasena='".$data['password']."'");
        $usuario=array();
        while ($fila = $resultado->fetch_object()) {
        $usuario[]=$fila;
        }



        if(count($usuario)>=1){



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