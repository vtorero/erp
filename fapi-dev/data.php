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
$db = new mysqli("localhost","marife","libido16","frdash2");
//$db = new mysqli("localhost","marife","libido16","frdash_dev");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

mysqli_set_charset($db, 'utf8');
if (mysqli_connect_errno()) {
    printf("Conexiónes fallida: %s\n", mysqli_connect_error());
    exit();
}

$app->get("/api",function() use($db,$app){
    header("Content-type: application/json; charset=utf-8");
    $json = $app->request->getBody();
    $dat = json_decode($json, true);
    $arraymeses=array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
    $arraynros=array('01','02','03','04','05','06','07','08','09','10','11','12');


    $query=$db->query("SELECT sc.id, sc.nombre,count(*) total    from ventas v, venta_detalle vd, productos pd,categorias cat,sub_categorias sc
    where v.id=vd.id_venta and vd.id_producto=pd.id and pd.id_categoria=cat.id and pd.id_subcategoria=sc.id
    and v.fecha between '2022-03-01' and '2022-03-31' group by 1 order by 3 desc limit 1;");
       $mes1=array();

    while ($row = $query->fetch_array()) {
            $mes1=array('id'=>$row['id'],'nombre'=>$row['nombre']);
        }

        $query2=$db->query("SELECT sc.id,sc.nombre,count(*) total    from ventas v, venta_detalle vd, productos pd,categorias cat,sub_categorias sc
        where v.id=vd.id_venta and vd.id_producto=pd.id and pd.id_categoria=cat.id and pd.id_subcategoria=sc.id
        and v.fecha between '2022-04-01' and '2022-04-31' group by 1 order by 3 desc limit 1;");
           $mes2=array();
           while ($row = $query2->fetch_array()) {
            $mes2=array('id'=>$row['id'],'nombre'=>$row['nombre']);
        }


        $query3=$db->query("SELECT sc.id,sc.nombre,count(*) total    from ventas v, venta_detalle vd, productos pd,categorias cat,sub_categorias sc
        where v.id=vd.id_venta and vd.id_producto=pd.id and pd.id_categoria=cat.id and pd.id_subcategoria=sc.id
        and v.fecha between '2022-05-01' and '2022-05-31' group by 1 order by 3 desc limit 1;");
           $mes3=array();
           while ($row = $query3->fetch_array()) {
            $mes3=array('id'=>$row['id'],'nombre'=>$row['nombre']);
        }


        $query4=$db->query("SELECT sc.id, sc.nombre,count(*) total    from ventas v, venta_detalle vd, productos pd,categorias cat,sub_categorias sc
        where v.id=vd.id_venta and vd.id_producto=pd.id and pd.id_categoria=cat.id and pd.id_subcategoria=sc.id
        and v.fecha between '2022-06-01' and '2022-06-31' group by 1 order by 3 desc limit 1;");
           $mes4=array();
           while ($row = $query4->fetch_array()) {
            $mes4=array('id'=>$row['id'],'nombre'=>$row['nombre']);
        }

        $query5=$db->query("SELECT sc.id, sc.nombre,count(*) total    from ventas v, venta_detalle vd, productos pd,categorias cat,sub_categorias sc
        where v.id=vd.id_venta and vd.id_producto=pd.id and pd.id_categoria=cat.id and pd.id_subcategoria=sc.id
        and v.fecha between '2022-07-01' and '2022-07-31' group by 1 order by 3 desc limit 1;");
           $mes5=array();
           while ($row = $query5->fetch_array()) {
            $mes5=array('id'=>$row['id'],'nombre'=>$row['nombre']);
        }

        $query6=$db->query("SELECT sc.id,sc.nombre,count(*) total    from ventas v, venta_detalle vd, productos pd,categorias cat,sub_categorias sc
        where v.id=vd.id_venta and vd.id_producto=pd.id and pd.id_categoria=cat.id and pd.id_subcategoria=sc.id
        and v.fecha between '2022-08-01' and '2022-08-31' group by 1 order by 3 desc limit 1;");
           $mes6=array();
           while ($row = $query6->fetch_array()) {
            $mes6=array('id'=>$row['id'],'nombre'=>$row['nombre']);
        }


        $query7=$db->query("SELECT sc.id,sc.nombre,count(*) total    from ventas v, venta_detalle vd, productos pd,categorias cat,sub_categorias sc
        where v.id=vd.id_venta and vd.id_producto=pd.id and pd.id_categoria=cat.id and pd.id_subcategoria=sc.id
        and v.fecha between '2022-09-01' and '2022-09-31' group by 1 order by 3 desc limit 1;");
           $mes7=array();
           while ($row = $query7->fetch_array()) {
            $mes7=array('id'=>$row['id'],'nombre'=>$row['nombre']);
        }


        $query8=$db->query("SELECT sc.id, sc.nombre,count(*) total    from ventas v, venta_detalle vd, productos pd,categorias cat,sub_categorias sc
        where v.id=vd.id_venta and vd.id_producto=pd.id and pd.id_categoria=cat.id and pd.id_subcategoria=sc.id
        and v.fecha between '2022-10-01' and '2022-10-31' group by 1 order by 3 desc limit 1;");
           $mes8=array();
           while ($row = $query8->fetch_array()) {
            $mes8=array('id'=>$row['id'],'nombre'=>$row['nombre']);
        }

        $query9=$db->query("SELECT sc.id, sc.nombre,count(*) total    from ventas v, venta_detalle vd, productos pd,categorias cat,sub_categorias sc
        where v.id=vd.id_venta and vd.id_producto=pd.id and pd.id_categoria=cat.id and pd.id_subcategoria=sc.id
        and v.fecha between '2022-11-01' and '2022-11-31' group by 1 order by 3 desc limit 1;");
           $mes9=array();
           while ($row = $query9->fetch_array()) {
            $mes9=array('id'=>$row['id'],'nombre'=>$row['nombre']);
        }

        $query10=$db->query("SELECT sc.id,sc.nombre,count(*) total    from ventas v, venta_detalle vd, productos pd,categorias cat,sub_categorias sc
        where v.id=vd.id_venta and vd.id_producto=pd.id and pd.id_categoria=cat.id and pd.id_subcategoria=sc.id
        and v.fecha between '2022-12-01' and '2022-12-31' group by 1 order by 3 desc limit 1;");
           $mes10=array();
           while ($row = $query10->fetch_array()) {
            $mes10=array('id'=>$row['id'],'nombre'=>$row['nombre']);
        }

        $query11=$db->query("SELECT sc.id,sc.nombre,count(*) total    from ventas v, venta_detalle vd, productos pd,categorias cat,sub_categorias sc
        where v.id=vd.id_venta and vd.id_producto=pd.id and pd.id_categoria=cat.id and pd.id_subcategoria=sc.id
        and v.fecha between '2023-1-01' and '2023-1-31' group by 1 order by 3 desc limit 1;");
           $mes11=array();
           while ($row = $query11->fetch_array()) {
            $mes11=array('id'=>$row['id'],'nombre'=>$row['nombre']);
        }

        $query12=$db->query("SELECT sc.id,sc.nombre,count(*) total    from ventas v, venta_detalle vd, productos pd,categorias cat,sub_categorias sc
        where v.id=vd.id_venta and vd.id_producto=pd.id and pd.id_categoria=cat.id and pd.id_subcategoria=sc.id
        and v.fecha between '2023-2-01' and '2023-2-29' group by 1 order by 3 desc limit 1;");
           $mes12=array();
           while ($row = $query12->fetch_array()) {
            $mes12=array('id'=>$row['id'],'nombre'=>$row['nombre']);
        }

        $query13=$db->query("SELECT sc.id, sc.nombre,count(*) total    from ventas v, venta_detalle vd, productos pd,categorias cat,sub_categorias sc
        where v.id=vd.id_venta and vd.id_producto=pd.id and pd.id_categoria=cat.id and pd.id_subcategoria=sc.id
        and v.fecha between '2023-3-01' and '2023-3-31' group by 1 order by 3 desc limit 1;");
           $mes13=array();
           while ($row = $query13->fetch_array()) {
            $mes13=array('id'=>$row['id'],'nombre'=>$row['nombre']);
        }
/*productos*/

$query=$db->query("SELECT pd.id, pd.nombre,count(*) total
from ventas v, venta_detalle vd, productos pd,categorias cat,sub_categorias sc
where v.id=vd.id_venta and vd.id_producto=pd.id and pd.id_categoria=cat.id and pd.id_subcategoria=sc.id
and v.fecha between '2022-03-01' and '2022-03-31' group by 1 order by 3 desc limit 1;");
   $prod1=array();

while ($row = $query->fetch_array()) {
        $prod1=array('id'=>$row['id'],'nombre'=>$row['nombre']);
    }

    $query=$db->query("SELECT pd.id, pd.nombre,count(*) total
from ventas v, venta_detalle vd, productos pd,categorias cat,sub_categorias sc
where v.id=vd.id_venta and vd.id_producto=pd.id and pd.id_categoria=cat.id and pd.id_subcategoria=sc.id
and v.fecha between '2022-04-01' and '2022-04-31' group by 1 order by 3 desc limit 1;");
   $prod2=array();

while ($row = $query->fetch_array()) {
        $prod2=array('id'=>$row['id'],'nombre'=>$row['nombre']);
    }

    $query=$db->query("SELECT pd.id, pd.nombre,count(*) total
from ventas v, venta_detalle vd, productos pd,categorias cat,sub_categorias sc
where v.id=vd.id_venta and vd.id_producto=pd.id and pd.id_categoria=cat.id and pd.id_subcategoria=sc.id
and v.fecha between '2022-05-01' and '2022-05-31' group by 1 order by 3 desc limit 1;");
   $prod3=array();

while ($row = $query->fetch_array()) {
        $prod3=array('id'=>$row['id'],'nombre'=>$row['nombre']);
    }

    $query=$db->query("SELECT pd.id, pd.nombre,count(*) total
from ventas v, venta_detalle vd, productos pd,categorias cat,sub_categorias sc
where v.id=vd.id_venta and vd.id_producto=pd.id and pd.id_categoria=cat.id and pd.id_subcategoria=sc.id
and v.fecha between '2022-06-01' and '2022-06-31' group by 1 order by 3 desc limit 1;");
   $prod4=array();

while ($row = $query->fetch_array()) {
        $prod4=array('id'=>$row['id'],'nombre'=>$row['nombre']);
    }

    $query=$db->query("SELECT pd.id, pd.nombre,count(*) total
from ventas v, venta_detalle vd, productos pd,categorias cat,sub_categorias sc
where v.id=vd.id_venta and vd.id_producto=pd.id and pd.id_categoria=cat.id and pd.id_subcategoria=sc.id
and v.fecha between '2022-07-01' and '2022-07-31' group by 1 order by 3 desc limit 1;");
   $prod5=array();

while ($row = $query->fetch_array()) {
        $prod5=array('id'=>$row['id'],'nombre'=>$row['nombre']);
    }


    $query=$db->query("SELECT pd.id, pd.nombre,count(*) total
from ventas v, venta_detalle vd, productos pd,categorias cat,sub_categorias sc
where v.id=vd.id_venta and vd.id_producto=pd.id and pd.id_categoria=cat.id and pd.id_subcategoria=sc.id
and v.fecha between '2022-08-01' and '2022-08-31' group by 1 order by 3 desc limit 1;");
   $prod6=array();

while ($row = $query->fetch_array()) {
        $prod6=array('id'=>$row['id'],'nombre'=>$row['nombre']);
    }



    $query=$db->query("SELECT pd.id, pd.nombre,count(*) total
from ventas v, venta_detalle vd, productos pd,categorias cat,sub_categorias sc
where v.id=vd.id_venta and vd.id_producto=pd.id and pd.id_categoria=cat.id and pd.id_subcategoria=sc.id
and v.fecha between '2022-09-01' and '2022-09-31' group by 1 order by 3 desc limit 1;");
   $prod7=array();

while ($row = $query->fetch_array()) {
        $prod7=array('id'=>$row['id'],'nombre'=>$row['nombre']);
    }

    $query=$db->query("SELECT pd.id, pd.nombre,count(*) total
from ventas v, venta_detalle vd, productos pd,categorias cat,sub_categorias sc
where v.id=vd.id_venta and vd.id_producto=pd.id and pd.id_categoria=cat.id and pd.id_subcategoria=sc.id
and v.fecha between '2022-10-01' and '2022-10-31' group by 1 order by 3 desc limit 1;");
   $prod8=array();

while ($row = $query->fetch_array()) {
        $prod8=array('id'=>$row['id'],'nombre'=>$row['nombre']);
    }


    $query=$db->query("SELECT pd.id, pd.nombre,count(*) total
from ventas v, venta_detalle vd, productos pd,categorias cat,sub_categorias sc
where v.id=vd.id_venta and vd.id_producto=pd.id and pd.id_categoria=cat.id and pd.id_subcategoria=sc.id
and v.fecha between '2022-11-01' and '2022-11-31' group by 1 order by 3 desc limit 1;");
   $prod9=array();

while ($row = $query->fetch_array()) {
        $prod9=array('id'=>$row['id'],'nombre'=>$row['nombre']);
    }



    $query=$db->query("SELECT pd.id, pd.nombre,count(*) total
from ventas v, venta_detalle vd, productos pd,categorias cat,sub_categorias sc
where v.id=vd.id_venta and vd.id_producto=pd.id and pd.id_categoria=cat.id and pd.id_subcategoria=sc.id
and v.fecha between '2022-12-01' and '2022-12-31' group by 1 order by 3 desc limit 1;");
   $prod10=array();

while ($row = $query->fetch_array()) {
        $prod10=array('id'=>$row['id'],'nombre'=>$row['nombre']);
    }


    $query=$db->query("SELECT pd.id, pd.nombre,count(*) total
from ventas v, venta_detalle vd, productos pd,categorias cat,sub_categorias sc
where v.id=vd.id_venta and vd.id_producto=pd.id and pd.id_categoria=cat.id and pd.id_subcategoria=sc.id
and v.fecha between '2023-1-01' and '2023-1-31' group by 1 order by 3 desc limit 1;");
   $prod11=array();

while ($row = $query->fetch_array()) {
        $prod11=array('id'=>$row['id'],'nombre'=>$row['nombre']);
    }

    $query=$db->query("SELECT pd.id, pd.nombre,count(*) total
from ventas v, venta_detalle vd, productos pd,categorias cat,sub_categorias sc
where v.id=vd.id_venta and vd.id_producto=pd.id and pd.id_categoria=cat.id and pd.id_subcategoria=sc.id
and v.fecha between '2023-2-01' and '2023-2-31' group by 1 order by 3 desc limit 1;");
   $prod12=array();

while ($row = $query->fetch_array()) {
        $prod12=array('id'=>$row['id'],'nombre'=>$row['nombre']);
    }



    $query=$db->query("SELECT pd.id, pd.nombre,count(*) total
from ventas v, venta_detalle vd, productos pd,categorias cat,sub_categorias sc
where v.id=vd.id_venta and vd.id_producto=pd.id and pd.id_categoria=cat.id and pd.id_subcategoria=sc.id
and v.fecha between '2023-3-01' and '2023-3-31' group by 1 order by 3 desc limit 1;");
   $prod13=array();

while ($row = $query->fetch_array()) {
        $prod13=array('id'=>$row['id'],'nombre'=>$row['nombre']);
    }


        $data = array("status"=>200,
        "categorias"=>array(array('id'=>1,'nombre'=>'charcuteria'),array('id'=>1,'nombre'=>'charcuteria'),array('id'=>1,'nombre'=>'charcuteria'),array('id'=>1,'nombre'=>'charcuteria'),array('id'=>1,'nombre'=>'charcuteria'),array('id'=>1,'nombre'=>'charcuteria'),array('id'=>1,'nombre'=>'charcuteria'),array('id'=>1,'nombre'=>'charcuteria'),array('id'=>1,'nombre'=>'charcuteria'),array('id'=>1,'nombre'=>'charcuteria'),array('id'=>1,'nombre'=>'charcuteria'),array('id'=>1,'nombre'=>'charcuteria'),array('id'=>1,'nombre'=>'charcuteria')),
        "sub_categorias"=>array($mes1,$mes2,$mes3,$mes4,$mes5,$mes6,$mes7,$mes8,$mes9,$mes10,$mes11,$mes12,$mes13),
        "kilosFlag"=>array(1, 1, 1, 0, 1, 1, 1, 0, 1, 0,1,1,1),
        "productos" =>array($prod1,$prod2,$prod3,$prod4,$prod5,$prod6,$prod7,$prod8,$prod9,$prod10,$prod11,$prod12,$prod13)

        );
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