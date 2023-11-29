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
require_once 'vendor/regression.php';
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


$app = new Slim\Slim();
$db = new mysqli("localhost","aprendea_erp","erp2023*","aprendea_erp");
//$db = new mysqli("localhost","marife","libido16","frdash_dev");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

mysqli_set_charset($db, 'utf8');
if (mysqli_connect_errno()) {
    printf("Conexiónes fallida: %s\n", mysqli_connect_error());
    exit();
}


$app->get("/inventario/:id",function($id) use($db,$app){
    header("Content-type: application/json; charset=utf-8");
    $resultado = $db->query("SELECT p.id,p.codigo,s.nombre categoria,p.nombre producto,sum(i.granel) granel,sum(i.merma) merma,sum(i.cantidad) cantidad,FORMAT(sum(i.cantidad*p.peso)/1000,2) peso FROM frdash.inventario i, productos p,categorias c,sub_categorias s WHERE p.id_subcategoria={$id} and p.id_categoria=c.id and p.id_subcategoria=s.id and p.id_categoria=c.id and i.cantidad> 0 and i.id_producto=p.id group by 1,2,3,4;");
    $prods=array();
        while ($fila = $resultado->fetch_array()) {
            $prods[]=$fila;
      }

      $totales = $db->query("SELECT p.id_subcategoria,sum(i.granel) granel,sum(i.merma) merma,sum(i.cantidad) cantidad,FORMAT(sum(i.cantidad*p.peso)/1000,2) peso FROM frdash.inventario i, productos p where p.id_subcategoria={$id} and i.id_producto=p.id group by 1;");
    $tot=array();
        while ($fila = $totales->fetch_array()) {
            $tot[]=$fila;
      }



      $respuesta=json_encode(array("status"=>200,"data"=>$prods,"total"=>$tot));
    echo  $respuesta;

});

$app->post("/reporte",function() use($db,$app){
    header("Content-type: application/json; charset=utf-8");
    $json = $app->request->getBody();
    $dat = json_decode($json, true);
    $arraymeses=array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
    $arraynros=array('01','02','03','04','05','06','07','08','09','10','11','12');
    $mes1=substr($dat['ini'], 3,3);
    $mes2=substr($dat['fin'], 3,3);
    $dia1=substr($dat['ini'], 0,2);
    $dia2=substr($dat['fin'], 0,2);
    $ano1=substr($dat['ini'], 7,4);
    $ano2=substr($dat['fin'], 7,4);
    $fmes1=str_replace($arraymeses,$arraynros,$mes1);
    $fmes2=str_replace($arraymeses,$arraynros,$mes2);
    $ini=$ano1.'-'.$fmes1.'-'.$dia1;
    $fin=$ano2.'-'.$fmes2.'-'.$dia2;
    $inicio=$dia1.'/'.$fmes1;
    $final=$dia2.'/'.$fmes2;


    $ingreso=$db->query("SELECT sum(valor_total) total  FROM ventas where fecha_registro between '".$ini."' and '".$fin."'");
       $infoboleta=array();
    while ($row = $ingreso->fetch_array()) {
            $infoboleta[]=$row;
        }

    $pendiente=$db->query("SELECT sum(monto_pendiente) pendiente  FROM ventas where fecha_registro between '".$ini."' and '".$fin."'");
    $infopendiente=array();
    while ($row = $pendiente->fetch_array()) {
            $infopendiente[]=$row;
        }

        $gasto=$db->query("SELECT TRUNCATE(sum(cantidad*precio),2) gasto FROM compras c, compra_detalle d  where c.id=d.id_compra and c.fecha_registro between '".$ini."' and '".$fin."'");
        $infogasto=array();
        while ($row = $gasto->fetch_array()) {
                $infogasto[]=$row;
            }



        $productos=$db->query("SELECT p.nombre,count(id_producto) total from venta_detalle vd, productos p where vd.id_producto=p.id AND  vd.fecha_registro between '".$ini."' and '".$fin."' GROUP by 1 order by 2 desc limit 5");
        $infoproducto=array();
        while ($row = $productos->fetch_array()) {
                $infoproducto[]=$row;
            }



            $clientes=$db->query("SELECT c.nombre,count(id_cliente) total from ventas v, clientes c where v.id_cliente=c.id AND  v.fecha_registro between '".$ini."' and '".$fin."' group by 1 order by 2 desc limit 5");
            $infoclientes=array();
            while ($row = $clientes->fetch_array()) {
                    $infoclientes[]=$row;
                }





            $sucursales=$db->query("SELECT s.nombre,count(id_sucursal) total from ventas v, sucursales s where v.id_sucursal=s.id AND v.fecha_registro between '".$ini."' and '".$fin."' group by 1 order by 2 desc limit 5");
            $infosucursales=array();
            while ($row = $sucursales->fetch_array()) {
                    $infosucursales[]=$row;
            }



                $compras=$db->query("SELECT sum(cantidad*precio)gasto,DATE_FORMAT(c.fecha_registro, '%Y-%m-%d') fecha FROM compras c, compra_detalle d  where c.id=d.id_compra AND c.fecha_registro between '".$ini."' and '".$fin."' group by 2 order by 2");
                $infocompras=array();
                while ($row = $compras->fetch_array()) {
                        $infocompras[]=$row;
                }


                $ventas=$db->query("SELECT sum(cantidad*precio)venta,DATE_FORMAT(v.fecha_registro, '%Y-%m-%d') fecha FROM ventas v, venta_detalle d  where v.id=d.id_venta  AND v.fecha_registro between '".$ini."' and '".$fin."' group by 2 order by 2");
                $infoventas=array();
                while ($row = $ventas->fetch_array()) {
                        $infoventas[]=$row;
                }

      /*  $factura=$db->query("SELECT v.id,v.tipoDoc, v.id_usuario,case  v.estado when '1' then 'Enviada' when '3' then 'Anulada' end as estado,u.nombre usuario,ve.id id_vendedor,concat(ve.nombre,' ',ve.apellidos) vendedor,c.id id_cliente,c.num_documento,c.direccion,concat(c.razon_social) cliente,igv,monto_igv,valor_neto,valor_total,  comprobante,nro_comprobante,DATE_FORMAT(v.fecha, '%Y-%m-%d') fecha,observacion FROM ventas v,usuarios u,empresas c,vendedor ve where v.estado=1 and v.id_vendedor=ve.id and v.id_cliente=c.id and v.id_usuario=u.id and v.comprobante='Factura' and v.fecha  between  '".$ini."' and '".$fin."' order by v.id desc");
        $infofactura=array();
    while ($row = $factura->fetch_array()) {
            $infofactura[]=$row;
        }

        $totales=$db->query("SELECT sum(valor_neto) valor_neto,sum(monto_igv) monto_igv,sum(valor_total) valor_total  FROM ventas v,usuarios u,vendedor ve where v.estado=1 and v.id_vendedor=ve.id and v.id_usuario=u.id and v.comprobante in('Boleta','Factura') and fecha  between '".$ini."' and '".$fin."'");
        $infototal=array();
        while ($row = $totales->fetch_array()) {
                $infototal[]=$row;
            }

            $totalboleta=$db->query("SELECT sum(valor_neto) valor_neto,sum(monto_igv) monto_igv,sum(valor_total) valor_total  FROM ventas v,usuarios u,clientes c,vendedor ve where v.estado=1 and v.id_vendedor=ve.id and v.id_cliente=c.id and v.id_usuario=u.id and v.comprobante in('Boleta') and fecha  between '".$ini."' and '".$fin."'");
        $totalbo=array();
        while ($row = $totalboleta->fetch_array()) {
                $totalbo[]=$row;
            }

            $totalfactura=$db->query("SELECT sum(valor_neto) valor_neto,sum(monto_igv) monto_igv,sum(valor_total) valor_total FROM ventas v,usuarios u,empresas c,vendedor ve where  v.estado=1  and v.id_vendedor=ve.id and v.id_cliente=c.id and v.id_usuario=u.id and v.comprobante='Factura' and v.fecha  between '".$ini."' and '".$fin."'");
            $totalfac=array();
            while ($row = $totalfactura->fetch_array()) {
                    $totalfac[]=$row;
                }



                $totalxdia=$db->query("SELECT DATE_FORMAT(v.fecha, '%d-%m-%y') fecha,sum(valor_neto) valor_neto,sum(monto_igv) monto_igv,sum(valor_total) valor_total  FROM ventas v,usuarios u,vendedor ve where v.estado=1 and v.id_vendedor=ve.id and v.id_usuario=u.id and v.comprobante in('Boleta','Factura') and fecha  between '".$ini."' and '".$fin."' group by 1");
                $totaldias=array();
                while ($row = $totalxdia->fetch_array()) {
                        $totaldias[]=$row;
            }



            $notas=$db->query("SELECT v.id,v.codMotivo,if(v.tipDocAfectado='01','Factura','Boleta') tipDocAfectado,v.desMotivo,v.id_usuario,u.nombre usuario,c.id id_cliente,c.num_documento,c.direccion,concat(c.nombre,' ',c.apellido) cliente,igv,monto_igv,valor_neto,valor_total, estado, tipoDoc , if(tipoDoc= '07','Nota Credito','Nota Debito') NombreDoc,comprobante,nro_nota,nro_comprobante numDocfectado, DATE_FORMAT(v.fecha, '%d-%m-%y') fecha FROM notas v,usuarios u,clientes c where v.id_cliente=c.id and v.id_usuario=u.id and v.comprobante='Boleta' and v.fecha between '".$ini."' and '".$fin."'  union all SELECT v.id,v.codMotivo,if(v.tipDocAfectado='01','Factura','Boleta') tipDocAfectado,v.desMotivo,v.id_usuario,u.nombre usuario,c.id id_cliente,c.num_documento,c.direccion,concat(c.razon_social) cliente,igv,monto_igv,valor_neto,valor_total, v.estado,tipoDoc,if(tipoDoc= '07','Nota Credito','Nota Debito') NombreDoc ,comprobante,nro_nota,nro_comprobante numDocfectado,DATE_FORMAT(v.fecha, '%Y-%m-%d') fecha FROM notas v,usuarios u,empresas c where v.id_cliente=c.id and v.id_usuario=u.id and v.comprobante='Factura' and v.fecha between '".$ini."' and '".$fin."' order by id desc;");
            $infonotas=array();
            while ($row = $notas->fetch_array()) {
                    $infonotas[]=$row;
        }

        $totalnotas=$db->query("SELECT sum(monto_igv) monto_igv,sum(valor_neto) valor_neto,sum(valor_total) valor_total FROM notas where fecha between '".$ini."' and '".$fin."'");
        $totalnot=array();
        while ($row = $totalnotas->fetch_array()) {
                $totalnot[]=$row;
            }

      $data = array("status"=>200,
        "boletas"=>$infoboleta,
        "facturas"=>$infofactura,
        "notas"=>$infonotas,
        "totales"=>$infototal,
        "totalboleta"=>$totalbo,
        "totalfactura"=>$totalfac,
        "totalnotas"=>$totalnot,
        "totaldias"=>$totaldias,
        "inicio"=>$ini,"final"=>$fin);


*/
        $data = array("status"=>200,
        "boletas"=>$infoboleta,
        "pendiente"=>$infopendiente,
        "gasto"=>$infogasto,
        "productos"=>$infoproducto,
        "clientes"=>$infoclientes,
        "sucursales"=>$infosucursales,
        "compras"=>$infocompras,
        "ventas"=>$infoventas,
        "inicio"=>$ini,"final"=>$fin);

        echo json_encode($data);


     });


     $app->post("/predecir",function() use($db,$app){
        header("Content-type: application/json; charset=utf-8");
        $json = $app->request->getBody();
        $dat = json_decode($json, true);
        $arraymeses=array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
        $arraynros=array('01','02','03','04','05','06','07','08','09','10','11','12');
        $mes1=substr($dat['ini'], 3,3);
        $mes2=substr($dat['fin'], 3,3);
        $dia1=substr($dat['ini'], 0,2);
        $dia2=substr($dat['fin'], 0,2);
        $ano1=substr($dat['ini'], 7,4);
        $ano2=substr($dat['fin'], 7,4);
        $fmes1=str_replace($arraymeses,$arraynros,$mes1);
        $fmes2=str_replace($arraymeses,$arraynros,$mes2);
        $ini=$ano1.'-'.$fmes1.'-'.$dia1;
        $fin=$ano2.'-'.$fmes2.'-'.$dia2;
        $rango=getRangeDate($ini,$fin);
        $venta_real=array();
        $sql="SELECT sum(valor_total) venta,DATE_FORMAT(v.fecha, '%Y-%m-%d') fecha FROM ventas v where fecha_registro between '".$ini."' and '".$fin."' group by 2";
        $datos=$db->query($sql);
        $info=array();
        $dias=array();
        $dia=1;

        while ($data = $datos->fetch_array()) {
            array_push($info,$data['venta']);
            array_push($venta_real,$data['venta']);
            array_push($dias,$dia);
            $dia++;
        }

      $ia= new IAphp();
       $prediccionVentas= $ia->regresionLineal($dias,$info);
        $w=$prediccionVentas["w"];
        $b=$prediccionVentas["b"];
        $datosPrediccion=array();
        for ($i=0; $i < count($rango); $i++) {
            $venta=$w*($i+1)+$b;
            array_push($datosPrediccion,(string) round($venta,2));
          }


         $data = array("status"=>200,
        "b"=>$b,
        "w"=>$w,
        "fechas_pred"=>$rango,
        "dias"=>$dias,
        "datosPrediccion"=>$datosPrediccion,
        "venta_real"=>$venta_real,
        "inicio"=>$ini,
        "final"=>$fin,
        "query"=>$sql
    );

        echo json_encode($data);


    });

    $app->post("/predecir-mermas",function() use($db,$app){
        header("Content-type: application/json; charset=utf-8");
        $json = $app->request->getBody();
        $dat = json_decode($json, true);
        $arraymeses=array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
        $arraynros=array('01','02','03','04','05','06','07','08','09','10','11','12');
        $mes1=substr($dat['ini'], 3,3);
        $mes2=substr($dat['fin'], 3,3);
        $dia1=substr($dat['ini'], 0,2);
        $dia2=substr($dat['fin'], 0,2);
        $ano1=substr($dat['ini'], 7,4);
        $ano2=substr($dat['fin'], 7,4);
        $fmes1=str_replace($arraymeses,$arraynros,$mes1);
        $fmes2=str_replace($arraymeses,$arraynros,$mes2);
        $ini=$ano1.'-'.$fmes1.'-'.$dia1;
        $fin=$ano2.'-'.$fmes2.'-'.$dia2;
        $rango=getRangeDate($ini,$fin);
        $venta_real=array();
        $sql="SELECT sum(cantidad*precio)merma,DATE_FORMAT(fecha_registro, '%Y-%m-%d') fecha FROM nota_detalle where fecha_registro between  '".$ini."' and '".$fin."'   group by 2";
        //$sql="SELECT sum(valor_total) venta,DATE_FORMAT(v.fecha, '%Y-%m-%d') fecha FROM ventas v where fecha_registro between '".$ini."' and '".$fin."' group by 2";
        $datos=$db->query($sql);
        $info=array();
        $dias=array();
        $dia=1;

        while ($data = $datos->fetch_array()) {
            array_push($info,$data['merma']);
            array_push($venta_real,$data['merma']);
            array_push($dias,$dia);
            $dia++;
        }

      $ia= new IAphp();
       $prediccionVentas= $ia->regresionLineal($dias,$info);
        $w=$prediccionVentas["w"];
        $b=$prediccionVentas["b"];
        $datosPrediccion=array();
        for ($i=0; $i < count($rango); $i++) {
            $venta=$w*($i+1)+$b;
            array_push($datosPrediccion,(string) round($venta,2));
          }


         $data = array("status"=>200,
        "b"=>$b,
        "w"=>$w,
        "fechas_pred"=>$rango,
        "dias"=>$dias,
        "datosPrediccion"=>$datosPrediccion,
        "venta_real"=>$venta_real,
        "inicio"=>$ini,
        "final"=>$fin,
        "query"=>$sql
    );

        echo json_encode($data);


    });


    $app->post("/predecir-compras",function() use($db,$app){
        header("Content-type: application/json; charset=utf-8");
        $json = $app->request->getBody();
        $dat = json_decode($json, true);
        $arraymeses=array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
        $arraynros=array('01','02','03','04','05','06','07','08','09','10','11','12');
        $mes1=substr($dat['ini'], 3,3);
        $mes2=substr($dat['fin'], 3,3);
        $dia1=substr($dat['ini'], 0,2);
        $dia2=substr($dat['fin'], 0,2);
        $ano1=substr($dat['ini'], 7,4);
        $ano2=substr($dat['fin'], 7,4);
        $fmes1=str_replace($arraymeses,$arraynros,$mes1);
        $fmes2=str_replace($arraymeses,$arraynros,$mes2);
        $ini=$ano1.'-'.$fmes1.'-'.$dia1;
        $fin=$ano2.'-'.$fmes2.'-'.$dia2;
        $rango=getRangeDate($ini,$fin);
        $venta_real=array();
        $sql="SELECT sum(cantidad*precio)gasto,DATE_FORMAT(c.fecha_registro, '%Y-%m-%d') fecha FROM compras c, detalle_compras d  where c.id=d.id_compra and fecha between  '".$ini."' and '".$fin."'  group by 2";
        $datos=$db->query($sql);
        $info=array();
        $dias=array();
        $dia=1;

        while ($data = $datos->fetch_array()) {
            array_push($info,$data['gasto']);
            array_push($venta_real,$data['gasto']);
            array_push($dias,$dia);
            $dia++;
        }

      $ia= new IAphp();
       $prediccionVentas= $ia->regresionLineal($dias,$info);
        $w=$prediccionVentas["w"];
        $b=$prediccionVentas["b"];
        $datosPrediccion=array();
        for ($i=0; $i < count($rango); $i++) {
            $venta=$w*($i+1)+$b;
            array_push($datosPrediccion,(string) round($venta,2));
          }


         $data = array("status"=>200,
        "b"=>$b,
        "w"=>$w,
        "fechas_pred"=>$rango,
        "dias"=>$dias,
        "datosPrediccion"=>$datosPrediccion,
        "venta_real"=>$venta_real,
        "inicio"=>$ini,
        "final"=>$fin,
        "query"=>$sql
    );

        echo json_encode($data);


    });

     $app->post("/exportar",function() use($db,$app){
        //header("Content-type: application/json; charset=utf-8");
        $json = $app->request->getBody();
        $dat = json_decode($json, true);
        $arraymeses=array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
        $arraynros=array('01','02','03','04','05','06','07','08','09','10','11','12');
        $mes1=substr($dat['ini'], 3,3);
        $mes2=substr($dat['fin'], 3,3);
        $dia1=substr($dat['ini'], 0,2);
        $dia2=substr($dat['fin'], 0,2);
        $ano1=substr($dat['ini'], 7,4);
        $ano2=substr($dat['fin'], 7,4);
        $fmes1=str_replace($arraymeses,$arraynros,$mes1);
        $fmes2=str_replace($arraymeses,$arraynros,$mes2);
        $ini=$ano1.'-'.$fmes1.'-'.$dia1;
        $fin=$ano2.'-'.$fmes2.'-'.$dia2;


        $fileName = "members-data_" . date('Y-m-d') . ".xls";
        $lineData =array();
        $fields = array('');
        $excelData = implode("\t", array_values($fields)) . "\n";
        // Fetch records from database
        $sql="SELECT v.id, v.tipoDoc,v.id_usuario,case v.estado when '1' then 'Enviada' when '3' then 'Anulada' end as estado,u.nombre usuario,ve.id id_vendedor,concat(ve.nombre,' ',ve.apellidos) vendedor,c.id id_cliente,c.num_documento,c.direccion,concat(c.nombre,' ',c.apellido) cliente,igv,monto_igv,valor_neto,valor_total,  comprobante,nro_comprobante, DATE_FORMAT(v.fecha, '%Y-%m-%d') fecha,formaPago,DATE_FORMAT(v.fechaPago, '%Y-%m-%d') fechaPago ,observacion FROM ventas v,usuarios u,clientes c,vendedor ve where v.id_vendedor=ve.id and v.id_cliente=c.id and v.id_usuario=u.id and v.comprobante in('Boleta') and fecha BETWEEN '".$ini."' and '".$fin."'  union all SELECT v.id,v.tipoDoc, v.id_usuario,case  v.estado when '1' then 'Enviada' when '3' then 'Anulada' end as estado,u.nombre usuario,ve.id id_vendedor,concat(ve.nombre,' ',ve.apellidos) vendedor,c.id id_cliente,c.num_documento,c.direccion,concat(c.razon_social) cliente,igv,monto_igv,valor_neto,valor_total,  comprobante,nro_comprobante,DATE_FORMAT(v.fecha, '%Y-%m-%d') fecha,formaPago,DATE_FORMAT(v.fechaPago, '%Y-%m-%d') fechaPago ,observacion FROM ventas v,usuarios u,empresas c,vendedor ve where v.id_vendedor=ve.id and v.id_cliente=c.id and v.id_usuario=u.id and v.comprobante in('Factura','Factura Gratuita') and fecha BETWEEN '".$ini."' and '".$fin."'  order by id desc";

         $query = $db->query($sql);
        if($query->num_rows > 0){
            // Output each row of the data
                 while($row = $query->fetch_assoc()){
                $fields = array('FACTURA', 'TIPO','FECHA EMISION', 'CLIENTE','RUC','FORMA PAGO' ,'FECHA PAGO');
                $excelData.= implode("\t", array_values($fields)) . "\n";
                $lineData  = array($row['nro_comprobante'],$row['comprobante'], $row['fecha'],$row['cliente'],$row['num_documento'],$row['formaPago'],$row['fechaPago']);
                array_walk($lineData,'filterData');
                $excelData .= implode("\t", array_values($lineData)) . "\n";
                $sqlrow="SELECT v.`id`, `id_producto`,p.codigo,p.`nombre`,`unidad_medida` ,`cantidad`,v.`peso` ,`precio`, `subtotal` FROM `venta_detalle` v ,productos p where v.id_producto=p.id and id_venta={$row['id']}";
                $query2 = $db->query($sqlrow);
                $fields2 = array('NRO','CODIGO','PRODUCTO','UNIDAD','CANTIDAD','PRECIO','SUBTOTAL');
            $excelData.= implode("\t", array_values($fields2)) . "\n";
            $nro=1;
                while($row2 = $query2->fetch_assoc()){
                    $lineData1 = array($nro, $row2['codigo'],$row2['nombre'],$row2['unidad_medida'],$row2['cantidad'],$row2['precio'],$row2['subtotal']);
                    array_walk($lineData1, 'filterData');
                    $excelData.= implode("\t", array_values($lineData1)) . "\n";
                    $nro++;
                }
                $fields3 = array('','','','','','OP. Gravadas',$row['valor_neto']);
                $excelData.= implode("\t", array_values($fields3)) . "\n";
                $fields4 = array('','','','','','I.G.V',$row['monto_igv']);
                $excelData.= implode("\t", array_values($fields4)) . "\n";
                $fields5 = array('','','','','','TOTAL',$row['valor_total']);
                $excelData.= implode("\t", array_values($fields5)) . "\n";

            }
        }else{
            $excelData .= 'No hay resultados de la consulta...'. "\n";
        }

        // Headers for download
        header("Content-Type: application/vnd.ms-excel");
       header("Content-Disposition: attachment; filename=\"$fileName\"");

        // Render excel data
       echo $excelData;

     });

     function filterData(&$str){
        $str = preg_replace("/\t/", "\\t", $str);
        $str = preg_replace("/\r?\n/", "\\n", $str);
        if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
    }

    function getRangeDate($date_ini, $date_end)
    {
     $data=array();
        for($i=$date_ini;$i<=$date_end;$i = date("Y-m-d", strtotime($i ."+ 1 days"))){

            array_push( $data, $i);

        }

        return $data;

    }

$app->run();