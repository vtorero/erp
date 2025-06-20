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
//$db = new mysqli("localhost","root","","aprendea_erp");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

//mysqli_set_charset($db, 'utf8');
$db->set_charset("utf8mb4");
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


    $ingreso=$db->query("SELECT sum(valor_total) total  FROM ventas where fecha_registro between '".$ini." 00:00:00'  and '".$fin." 23:59:00'");
       $infoboleta=array();
    while ($row = $ingreso->fetch_array()) {
            $infoboleta[]=$row;
        }

    $pendiente=$db->query("SELECT sum(monto_pendiente) pendiente  FROM ventas where fecha_registro between '".$ini." 00:00:00' and '".$fin." 23:59:00'");
    $infopendiente=array();
    while ($row = $pendiente->fetch_array()) {
            $infopendiente[]=$row;
        }

        $gasto=$db->query("SELECT TRUNCATE(sum(cantidad*precio),2) gasto FROM compras c, compra_detalle d  where c.id=d.id_compra and c.fecha_registro between '".$ini."  00:00:00' and '".$fin." 23:59:00'");
        $infogasto=array();
        while ($row = $gasto->fetch_array()) {
                $infogasto[]=$row;
            }



        $productos=$db->query("SELECT p.nombre,count(id_producto) total from venta_detalle vd, productos p where vd.id_producto=p.id AND  vd.fecha_registro between '".$ini." 00:00:00' and '".$fin." 23:59:00' GROUP by 1 order by 2 desc limit 5");
        $infoproducto=array();
        while ($row = $productos->fetch_array()) {
                $infoproducto[]=$row;
            }



            $clientes=$db->query("SELECT c.nombre,sum(v.valor_total) total,sum(v.monto_pendiente) pendiente, count(id_cliente) pedidos from ventas v, clientes c where v.id_cliente=c.id AND  v.fecha_registro between '".$ini." 00:00:00' and '".$fin." 23:59:00' group by 1 order by 2 desc limit 5");
            $infoclientes=array();
            while ($row = $clientes->fetch_array()) {
                    $infoclientes[]=$row;
                }


            $clientes_tabla=$db->query("SELECT c.id,c.nombre,sum(v.valor_total) total,sum(v.monto_pendiente) pendiente, count(id_cliente) pedidos from ventas v, clientes c where v.id_cliente=c.id AND  v.fecha_registro between '".$ini." 00:00:00' and '".$fin." 23:59:00' group by 1,2 order by 3 desc");
            $infoclientestabla=array();
            while ($row = $clientes_tabla->fetch_array()) {
                    $infoclientestabla[]=$row;
                }




            $sucursales=$db->query("SELECT s.nombre,count(id_sucursal) total from ventas v, sucursales s where v.id_sucursal=s.id AND v.fecha_registro between '".$ini." 00:00:00'  and '".$fin." 23:59:00' group by 1 order by 2 desc limit 5");
            $infosucursales=array();
            while ($row = $sucursales->fetch_array()) {
                    $infosucursales[]=$row;
            }



                $compras=$db->query("SELECT sum(cantidad*precio)gasto,DATE_FORMAT(c.fecha_registro, '%Y-%m-%d') fecha FROM compras c, compra_detalle d  where c.id=d.id_compra AND c.fecha_registro between '".$ini." 00:00:00' and '".$fin." 23:59:00' group by 2 order by 2");
                $infocompras=array();
                while ($row = $compras->fetch_array()) {
                        $infocompras[]=$row;
                }


                $ventas=$db->query("SELECT sum(cantidad*precio)venta,DATE_FORMAT(v.fecha_registro, '%Y-%m-%d') fecha FROM ventas v, venta_detalle d  where v.id=d.id_venta  AND v.fecha_registro between '".$ini." 00:00:00' and '".$fin." 23:59:00' group by 2 order by 2");
                $infoventas=array();
                while ($row = $ventas->fetch_array()) {
                        $infoventas[]=$row;
                }


$sql_r="SELECT v.id,c.num_documento,c.nombre,p.codigo, p.nombre as producto,vd.cantidad,p.unidad,vd.precio,(vd.cantidad*vd.precio) valor_total,'Ingreso',u.nombre usuario, s.nombre sucursal,
concat(date_format(vd.fecha_registro, '%Y-%m-%d'),'-T0',s.id,v.id) responsable,v.fecha_registro
FROM aprendea_erp.venta_detalle vd,ventas v,usuarios u,sucursales s, productos p,clientes c where vd.id_producto=p.id and  v.id_sucursal=s.id and v.id=vd.id_venta and v.id_usuario=u.id and v.id_cliente=c.id and v.estado=1
and v.fecha_registro  between '{$ini} 00:00:01' and '{$fin} 23:59:59'
union all
SELECT v.id,c.num_documento,c.nombre,p.codigo,p.nombre as producto,vp.cantidad,p.unidad,vp.precio,(vp.cantidad*vp.precio) valor_total,'Salida',u.nombre usuario ,s.nombre sucursal,concat(date_format(vp.fecha_registro, '%Y-%m-%d'),'-T0',s.id,v.id) responsable,
vp.fecha_registro
FROM compra_detalle vp,compras v,usuarios u,sucursales s,productos p,clientes c where vp.id_producto=p.id and v.id_sucursal=s.id and v.id=vp.id_compra and v.id_usuario=u.id 
and vp.fecha_registro  between '{$ini} 00:00:01' and '{$fin} 23:59:59' order by fecha_registro desc";

$sql_reporte_caja="SELECT v.id,v.fecha,vp.fecha_registro,'Ingreso',u.nombre usuario, s.nombre sucursal, tp.nombre tipopago,c.nombre, valor_total,vp.monto, vp.monto_pendiente 
FROM aprendea_erp.venta_pagos vp,ventas v,usuarios u,sucursales s,tipoPago tp,cajas c where vp.tipoPago=tp.id and vp.cuentaPago=c.id and v.id_sucursal=s.id 
and v.id=vp.id_venta and vp.usuario=u.id and vp.fecha_registro between '{$ini} 00:00:01' and '{$fin} 23:59:59' and vp.monto>0 
union all 
SELECT v.id,v.fecha,vp.fecha_registro,'Salida',u.nombre usuario ,s.nombre sucursal, tp.nombre tipopago,c.nombre,valor_total,vp.monto, vp.monto_pendiente 
FROM aprendea_erp.compra_pagos vp,compras v,usuarios u,sucursales s,tipoPago tp,cajas c where vp.tipoPago=tp.id and vp.cuentaPago=c.id and v.id_sucursal=s.id 
and v.id=vp.id_compra and vp.usuario=u.id and vp.fecha_registro between '{$ini} 00:00:01' and '{$fin} 23:59:59' and vp.monto>0 ORDER BY `Ingreso` DESC";

                $ventas_reporte=$db->query($sql_r);
                $infoventas_reporte=array();
                while ($row = $ventas_reporte->fetch_array()) {
                 $infoventas_reporte[]=$row;
                }
                $reporte_caja=$db->query($sql_reporte_caja);
                $info_reporte_caja=array();
                while ($row = $reporte_caja->fetch_array()) {
                    $info_reporte_caja[]=$row;
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
        "clientes_tabla"=>$infoclientestabla,
        "sucursales"=>$infosucursales,
        "compras"=>$infocompras,
        "ventas"=>$infoventas,
        "reporte_caja"=>$info_reporte_caja,
        "reporte"=>$infoventas_reporte,
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

    /*reporte compras*/

    $app->post("/compras",function() use($db,$app){
        $json = $app->request->getBody();
        $dat = json_decode($json, true);
        $arraymeses=array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
        $arraynros=array('01','02','03','04','05','06','07','08','09','10','11','12');
        $mes1=substr($dat['fechaincio'], 0,3);
        $mes2=substr($dat['fechafin'], 0,3);
        $dia1=substr($dat['fechaincio'], 3,2);
        $dia2=substr($dat['fechafin'], 3,2);
        $ano1=substr($dat['fechaincio'], 5,4);
        $ano2=substr($dat['fechafin'], 5,4);
        $fmes1=str_replace($arraymeses,$arraynros,$mes1);
        $fmes2=str_replace($arraymeses,$arraynros,$mes2);
        $ini=$ano1.'-'.$fmes1.'-'.$dia1;
        $fin=$ano2.'-'.$fmes2.'-'.$dia2;



        $fileName = "members-data_" . date('Y-m-d') . ".xls";
        $lineData =array();
        $fields = array('');
        $excelData = implode("\t", array_values($fields)) . "\n";


        $sql="SELECT cd.id_compra,pr.razon_social,pr.num_documento,c.serie_documento,c.nro_documento, p.codigo,p.nombre,p.unidad,cd.cantidad,cd.precio,cd.subtotal,c.fecha,c.fecha_registro,u.nombre usuario FROM compra_detalle cd,productos p, compras c,usuarios u,proveedores pr where cd.id_producto=p.id and cd.id_compra=c.id and c.id_proveedor=pr.id and c.id_usuario=u.id and c.fecha between '{$ini} 00:00:00' and '{$fin} 23:59:59' order by c.fecha_registro desc";


        $query = $db->query($sql);

        if($query->num_rows > 0){
            // Output each row of the data
    $fields = array('ID COMPRA','PROVEEDOR','RUC','NRO_SERIE','NRO_DOC','CODIGO','PRODUCTO','UNIDAD','CANTIDAD','PRECIO','SUBTOTAL','FECHA DE COMPRA','FECHA REGISTRO','USUARIO');
                $excelData.= implode("\t", array_values($fields)) . "\n";
                 while($row = $query->fetch_assoc()){
                    $lineData  = array($row['id_compra'],$row['razon_social'],$row['num_documento'],$row['serie_documento'],
                    $row['nro_documento'],$row['codigo'],$row['nombre'],$row['unidad'],$row['cantidad'],$row['precio'],$row['subtotal'],$row['fecha'],$row['fecha_registro'],$row['usuario']);
                array_walk($lineData,'filterData');
                $excelData .= implode("\t", array_values($lineData)) . "\n";
                 }


        }else{
            $excelData .= 'No hay resultados de la consulta...'. "\n";
        }


       echo $excelData;


    });



    /**reporte productos */

    $app->post("/productos",function() use($db,$app){
        $fileName = "members-data_" . date('Y-m-d') . ".xls";
        $lineData =array();
        $fields = array('');
        $excelData = implode("\t", array_values($fields)) . "\n";


        $sql="SELECT p.id,p.codigo,p.nombre,c.nombre categoria,sc.nombre subcategoria,fa.nombre familia, p.unidad,p.precio,p.imagen FROM productos p LEFT join categorias c on p.id_categoria=c.id LEFT join sub_categorias sc on p.id_subcategoria=sc.id LEFT join sub_sub_categorias fa on p.id_sub_sub_categoria=fa.id order by id desc";
        $query = $db->query($sql);

        if($query->num_rows > 0){
            // Output each row of the data
                $fields = array('ID','CODIGO','DESCRIPCION','CATEGORIA','SUBCATEGORIA','FAMILIA','UNIDAD' ,'PRECIO');
                $excelData.= implode("\t", array_values($fields)) . "\n";
                 while($row = $query->fetch_assoc()){
                    $lineData  = array($row['id'],$row['codigo'],limpiarCadena($row['nombre']),$row['categoria'],$row['subcategoria'],$row['familia'],$row['unidad'],$row['precio']);
                array_walk($lineData,'filterData');
                $excelData .= implode("\t", array_values($lineData)) . "\n";
                 }


        }else{
            $excelData .= 'No hay resultados de la consulta...'. "\n";
        }

        // Headers for download
       // header("Content-Type: application/vnd.ms-excel");
       //header("Content-Disposition: attachment; filename=\"$fileName\"");

        // Render excel data
       echo $excelData;


    });

/*
    $app->post("/productos", function() use($db, $app) {
        $fileName = "productos_" . date('Y-m-d') . ".xls";
        $excelData = "";
    
        // Encabezados de la tabla
        $fields = array('ID', 'CODIGO', 'DESCRIPCION', 'CATEGORIA', 'SUBCATEGORIA', 'FAMILIA', 'UNIDAD', 'PRECIO');
        $excelData .= implode("\t", array_values($fields)) . "\n";
    
        // Consulta a la base de datos
        $sql = "SELECT p.id, p.codigo, p.nombre, c.nombre AS categoria, 
                       sc.nombre AS subcategoria, fa.nombre AS familia, 
                       p.unidad, p.precio 
                FROM productos p 
                LEFT JOIN categorias c ON p.id_categoria = c.id 
                LEFT JOIN sub_categorias sc ON p.id_subcategoria = sc.id 
                LEFT JOIN sub_sub_categorias fa ON p.id_sub_sub_categoria = fa.id 
                ORDER BY p.id DESC";
        $query = $db->query($sql);
    
        if ($query->num_rows > 0) {
            while ($row = $query->fetch_assoc()) {
                // Convertir los datos a UTF-8 para evitar errores de codificación
                $row = array_map(function($value) {
                    return mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
                }, $row);
    
                $lineData = array($row['id'], $row['codigo'],iconv('ISO-8859-1', 'UTF-8', $row['nombre']) , 
                $row['categoria'], $row['subcategoria'], 
                $row['familia'], $row['unidad'], $row['precio']);
                $excelData .= implode("\t", array_values($lineData)) . "\n";
            }
        } else {
            $excelData .= "No hay resultados de la consulta...\n";
        }
    
        // Configurar la descarga como Excel con codificación UTF-8
  
        //echo "\xEF\xBB\xBF"; // BOM para que Excel detecte UTF-8
    
        // Imprimir el contenido del archivo
        echo $excelData;
    });
    */
    function limpiarCadena($cadena) {
        // Eliminar acentos y caracteres especiales
        $acentos = array(
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n',
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ñ' => 'N',
            'ä' => 'a', 'ë' => 'e', 'ï' => 'i', 'ö' => 'o', 'ü' => 'u', 'ÿ' => 'y',
            'Ä' => 'A', 'Ë' => 'E', 'Ï' => 'I', 'Ö' => 'O', 'Ü' => 'U', 'Ÿ' => 'Y',
            // Agregar otros caracteres especiales que desees reemplazar
        );
    
        // Reemplazar los caracteres acentuados
        $cadena = strtr($cadena, $acentos);
        
        // Eliminar caracteres no alfanuméricos (excepto espacios)
        $cadena = preg_replace("[^a-zA-Z0-9\s]", "", $cadena);
    
        return $cadena;
    }
    


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

        $sql="SELECT v.id,c.num_documento,c.nombre,p.codigo, p.nombre as producto,vd.cantidad,p.unidad,vd.precio,(vd.cantidad*vd.precio) valor_total,'Ingreso',u.nombre usuario, s.nombre sucursal,
        concat(date_format(vd.fecha_registro, '%Y-%m-%d'),'-T0',s.id,v.id) responsable,v.fecha_registro
        FROM aprendea_erp.venta_detalle vd,ventas v,usuarios u,sucursales s, productos p,clientes c where vd.id_producto=p.id and  v.id_sucursal=s.id and v.id=vd.id_venta and v.id_usuario=u.id and v.id_cliente=c.id and v.estado=1
        and v.fecha_registro  between '{$ini} 00:00:01' and '{$fin} 23:59:59'
        union all
        SELECT v.id,c.num_documento,c.nombre,p.codigo,p.nombre as producto,vp.cantidad,p.unidad,vp.precio,(vp.cantidad*vp.precio) valor_total,'Salida',u.nombre usuario ,s.nombre sucursal,concat(date_format(vp.fecha_registro, '%Y-%m-%d'),'-T0',s.id,v.id) responsable,
        vp.fecha_registro
        FROM compra_detalle vp,compras v,usuarios u,sucursales s,productos p,clientes c where vp.id_producto=p.id and v.id_sucursal=s.id and v.id=vp.id_compra and v.id_usuario=u.id
        and vp.fecha_registro  between '{$ini} 00:00:01' and '{$fin} 23:59:59' order by fecha_registro desc";

                $query = $db->query($sql);
        if($query->num_rows > 0){

            // Output each row of the data
                $fields = array('ID','Fecha','Documento','Razon Social','Producto','Cantidad','Unidad','Precio','Total','Movimiento','Usuario','Sucursal');
                $excelData.= implode("\t", array_values($fields)) . "\n";
                 while($row = $query->fetch_assoc()){
                    $lineData  = array($row['id'],$row['fecha_registro'],$row['num_documento'],$row['nombre'],$row['producto'],$row['cantidad'],$row['unidad'],$row['precio'],$row['valor_total'],$row['Ingreso'], $row['usuario'],$row['sucursal']);
                array_walk($lineData,'filterData');
                $excelData .= implode("\t", array_values($lineData)) . "\n";


            }
        }else{
            $excelData .= 'No hay resultados de la consulta...'. "\n";
        }

        // Headers for download
       // header("Content-Type: application/vnd.ms-excel");
       //header("Content-Disposition: attachment; filename=\"$fileName\"");

        // Render excel data
       echo $excelData;

     });

      $app->post("/exportarclientes",function() use($db,$app){
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

        $sql="SELECT c.id,c.nombre,sum(v.valor_total) total,sum(v.monto_pendiente) pendiente, count(id_cliente) pedidos from ventas v, clientes c where v.id_cliente=c.id AND  v.fecha_registro between '".$ini." 00:00:00' and '".$fin." 23:59:00' group by 1,2 order by 3 desc";

                $query = $db->query($sql);
        if($query->num_rows > 0){

            // Output each row of the data
                $fields = array('ID','Nombre','Total','Pendiente','Pedidos');
                $excelData.= implode("\t", array_values($fields)) . "\n";
                 while($row = $query->fetch_assoc()){
                    $lineData  = array($row['id'],$row['nombre'],$row['total'],$row['pendiente'],$row['pedidos']);
                array_walk($lineData,'filterData');
                $excelData .= implode("\t", array_values($lineData)) . "\n";


            }
        }else{
            $excelData .= 'No hay resultados de la consulta...'. "\n";
        }

        // Headers for download
       // header("Content-Type: application/vnd.ms-excel");
       //header("Content-Disposition: attachment; filename=\"$fileName\"");

        // Render excel data
       echo $excelData;

     });

     $app->post("/exportarcaja",function() use($db,$app){
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


        $fileName = "members-data_" . date('Y-m-d') . ".xls";
        $lineData =array();
        $fields = array('');
        $excelData = implode("\t", array_values($fields)) . "\n";

        $sql="SELECT v.id,v.fecha,vp.fecha_registro,'Ingreso',u.nombre usuario, s.nombre sucursal,
        tp.nombre tipopago,c.nombre as cuenta,valor_total,vp.monto, vp.monto_pendiente
        FROM aprendea_erp.venta_pagos vp,ventas v,usuarios u,sucursales s,tipoPago tp,cajas c where vp.tipoPago=tp.id and vp.cuentaPago=c.id and  v.id_sucursal=s.id and v.id=vp.id_venta and vp.usuario=u.id and v.estado=1
        and vp.fecha_registro  between '{$ini} 00:00:01' and '{$fin} 23:59:59' and vp.monto>0 union all
        SELECT v.id,v.fecha,vp.fecha_registro,'Salida',u.nombre usuario ,s.nombre sucursal,
        tp.nombre tipopago,c.nombre as cuenta,valor_total,vp.monto, vp.monto_pendiente
        FROM aprendea_erp.compra_pagos vp,compras v,usuarios u,sucursales s,tipoPago tp,cajas c
        where vp.tipoPago=tp.id and vp.cuentaPago=c.id and v.id_sucursal=s.id and v.id=vp.id_compra and vp.usuario=u.id and v.estado=1
        and vp.fecha_registro  between '{$ini} 00:00:01' and '{$fin} 23:59:59' and vp.monto>0 order by id,fecha_registro asc;";

                $query = $db->query($sql);
        if($query->num_rows > 0){

            // Output each row of the data
                $fields = array('ID','Fecha','Fecha Registro','Movimiento','Usuario','Sucursal','Medio pago','Cuenta','Monto','Monto Pendiente');
                $excelData.= implode("\t", array_values($fields)) . "\n";
                 while($row = $query->fetch_assoc()){
                    $lineData  = array($row['id'],$row['fecha'],$row['fecha_registro'],$row['Ingreso'],$row['usuario'],$row['sucursal'], $row['tipopago'],$row['cuenta'],$row['monto'],$row['monto_pendiente']);
                array_walk($lineData,'filterData');
                $excelData .= implode("\t", array_values($lineData)) . "\n";


            }
        }else{
            $excelData .= 'No hay resultados de la consulta...'. "\n";
        }

        // Headers for download
       // header("Content-Type: application/vnd.ms-excel");
       //header("Content-Disposition: attachment; filename=\"$fileName\"");

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