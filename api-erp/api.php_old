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
$app = new Slim\Slim();
$db = new mysqli("localhost","marife","libido16","frdash");

//mysqli_set_charset($db, 'utf8');
if (mysqli_connect_errno()) {
    printf("Conexiónes fallida: %s\n", mysqli_connect_error());
    exit();
}
$data=array();

/*Productos*/
$app->get("/productos",function() use($db,$app){
    header("Content-type: application/json; charset=utf-8");
    $resultado = $db->query("SELECT codigo,p.nombre,c.nombre nombrecategoria,costo,IGV,precio_sugerido,c.id id_categoria FROM  productos p, categorias c WHERE p.id_categoria=c.id");  
    $prods=array();
        while ($fila = $resultado->fetch_array()) {
            
            $prods[]=$fila;
        }
        $respuesta=json_encode($prods);
        echo  $respuesta;
        
    });

$app->get("/categorias",function() use($db,$app){
        header("Content-type: application/json; charset=utf-8");
        $resultado = $db->query("SELECT id,nombre  FROM  categorias order by id");  
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


    $app->post("/producto",function() use($db,$app){
        header("Content-type: application/json; charset=utf-8");
           $json = $app->request->getBody();
           $j = json_decode($json,true);
           $data = json_decode($j['json']);
    
           $codigo=(is_array($data->codigo))? array_shift($data->codigo): $data->codigo;
            $nombre=(is_array($data->nombre))? array_shift($data->nombre): $data->nombre;
            $costo=(is_array($data->costo))? array_shift($data->costo): $data->costo;
            $precio=(is_array($data->precio_sugerido))? array_shift($data->precio_sugerido): $data->precio_sugerido;
            $categoria=(is_array($data->id_categoria))? array_shift($data->id_categoria): $data->id_categoria;
    
        
            $query ="INSERT INTO productos (codigo,nombre,costo,precio_sugerido,id_categoria) VALUES ("
          ."'{$codigo}',"
          ."'{$nombre}',"
          ."{$costo},"
          ."{$precio},"
          ."{$categoria}".")";
       
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
            $costo=(is_array($data->costo))? array_shift($data->costo): $data->costo;
            $precio=(is_array($data->precio_sugerido))? array_shift($data->precio_sugerido): $data->precio_sugerido;
            $categoria=(is_array($data->id_categoria))? array_shift($data->id_categoria): $data->id_categoria;

            $sql = "UPDATE productos SET nombre='".$nombre."',costo=".$costo.", precio_sugerido=".$precio.",id_categoria=".$categoria." WHERE codigo=".$codigo;
            try { 
            $db->query($sql);
             $result = array("STATUS"=>true,"messaje"=>"Producto actualizado correctamente","string"=>$sql);
             echo  json_encode($result);
            }
             catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
                               
        
         });
 
/*Proveedores*/
$app->get("/proveedores",function() use($db,$app){
            header("Content-type: application/json; charset=utf-8");
            $resultado = $db->query("SELECT `id`, `razon_social`,`num_documento`, `direccion`,`departamento`,`provincia`,`distrito` FROM `proveedores`");  
            $prods=array();
                while ($fila = $resultado->fetch_array()) {
                    
                    $prods[]=$fila;
                }
                $respuesta=json_encode($prods);
                echo  $respuesta;
                
});

$app->get("/proveedores/:criterio",function($criterio) use($db,$app){
    header("Content-type: application/json; charset=utf-8");
    $resultado = $db->query("SELECT `id`, `razon_social`,`num_documento`, `direccion`,`departamento`,`provincia`,`distrito` FROM `proveedores` where razon_social like '%".$criterio."%'");  
    $prods=array();
        while ($fila = $resultado->fetch_array()) {
            
            $prods[]=$fila;
        }
        $respuesta=json_encode($prods);
        echo  $respuesta;
        
});

     
$app->post("/proveedor",function() use($db,$app){
    header("Content-type: application/json; charset=utf-8");
       $json = $app->request->getBody();
       $j = json_decode($json,true);
       $data = json_decode($j['json']);

        $ruc=(is_array($data->num_documento))? array_shift($data->num_documento): $data->num_documento;
        $razon_social=(is_array($data->razon_social))? array_shift($data->razon_social): $data->razon_social;
        $direccion=(is_array($data->direccion))? array_shift($data->direccion): $data->direccion;
        $departamento=(is_array($data->departamento))? array_shift($data->departamento): $data->departamento;
        $provincia=(is_array($data->provincia))? array_shift($data->provincia): $data->provincia;
        $distrito=(is_array($data->distrito))? array_shift($data->distrito): $data->distrito;
        $num_documento=(is_array($data->num_documento))? array_shift($data->num_documento): $data->num_documento;

        
        $query ="INSERT INTO proveedores (razon_social, direccion, num_documento, departamento,provincia,distrito) VALUES ("
      ."'{$razon_social}',"
      ."'{$direccion}',"
      ."'{$ruc}',"
      ."'{$departamento}',"
      ."'{$provincia}',"
      ."'{$distrito}'".")";
   
      $insert=$db->query($query);
               
       $result = array("STATUS"=>true,"messaje"=>"Proveedor registrado correctamente","string"=>$query);
        echo  json_encode($result);
    });

/**Compras */

$app->get("/compras",function() use($db,$app){
    header("Content-type: application/json; charset=utf-8");
    $resultado = $db->query("SELECT c.`id`, `comprobante`, `num_comprobante`, `descripcion`, `fecha`, c.`id_proveedor`,p.razon_social, `id_usuario` FROM `compras` c, proveedores p where c.id_proveedor=p.id");  
    $prods=array();
        while ($fila = $resultado->fetch_array()) {
            $prods[]=$fila;
        }
        $respuesta=json_encode($prods);
        echo  $respuesta;    
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

    $app->post("/skoda",function() use($db,$app){
        $query ="INSERT INTO skoda (source,origen,nombres,apellidos,rut,telefono,correo,marca,modelo,concesionario,dispositivo)  VALUES ("
        ."'{$app->request->post("source")}',"
        ."'{$app->request->post("origen")}',"
         ."'{$app->request->post("nombres")}',"
         ."'{$app->request->post("apellidos")}',"
         ."'{$app->request->post("rut")}',"
         ."'{$app->request->post("telefono")}',"
         ."'{$app->request->post("correo")}',"
         ."'{$app->request->post("marca")}',"
         ."'{$app->request->post("modelo")}',"
         ."'{$app->request->post("concesionario")}',"
         ."'{$app->request->post("dispositivo")}'"
         .")";

         $insert= $db->query($query);
          if($insert){
          $result = array("STATUS"=>true,"messaje"=>"Skoda registrado correctamente");
           }else{
           $result = array("STATUS"=>false,"messaje"=>"Skoda no creado");
           }
            echo json_encode($result);
           }); 


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

$app->run();