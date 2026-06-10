<?php
header('Access-Control-Allow-Origin:*');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
    die();
}


require (__DIR__ .'/vendor/autoload.php');
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Dompdf\Dompdf;
use Slim\Factory\AppFactory;

$app = AppFactory::create();
/*Produccion
$dsn = "mysql:host=lh-cjm.com;dbname=aprendea_erp;port=3306;charset=utf8";
$usuario="aprendea_erp";
$clave="erp2023*";*/

/*Local dev*/
$dsn = "mysql:host=localhost;dbname=erp;port=3306;charset=utf8";
$usuario="root";
$clave= "";


try {
    $pdo = new PDO($dsn, $usuario, $clave, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    ]);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$pdo->exec("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");



// 🔥 SOLUCIÓN
$app->setBasePath('/slim/reportes.php');

$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$app->post('/reporte', function (Request $request, Response $response) use ($pdo) {

    $data = json_decode($request->getBody()->getContents(), true);

    $arraymeses = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    $arraynros  = ['01','02','03','04','05','06','07','08','09','10','11','12'];

    $mes1 = substr($data['ini'], 3,3);
    $mes2 = substr($data['fin'], 3,3);
    $dia1 = substr($data['ini'], 0,2);
    $dia2 = substr($data['fin'], 0,2);
    $ano1 = substr($data['ini'], 7,4);
    $ano2 = substr($data['fin'], 7,4);

    $fmes1 = str_replace($arraymeses,$arraynros,$mes1);
    $fmes2 = str_replace($arraymeses,$arraynros,$mes2);

    $ini = "$ano1-$fmes1-$dia1";
    $fin = "$ano2-$fmes2-$dia2";

    $params = [
        ':ini1' => "$ini 00:00:00",
        ':fin1' => "$fin 23:59:59"
    ];

    // 🔹 Función helper
    $run = function($sql, $params) use ($pdo) {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    };

    // 🔹 Consultas
    $infoboleta = $run("SELECT SUM(valor_total) total FROM ventas WHERE estado=1 AND fecha_registro BETWEEN :ini1 AND :fin1", $params);

    $infopendiente = $run("SELECT SUM(monto_pendiente) pendiente FROM ventas WHERE estado=1 AND fecha_registro BETWEEN :ini1 AND :fin1", $params);

    $infogasto = $run("SELECT TRUNCATE(SUM(cantidad*precio),2) gasto
        FROM compras c JOIN compra_detalle d ON c.id=d.id_compra
        WHERE c.fecha_registro BETWEEN :ini1 AND :fin1", $params);

    $infoproducto = $run("SELECT p.nombre, COUNT(id_producto) total
        FROM ventas v
        JOIN venta_detalle vd ON v.id=vd.id_venta
        JOIN productos p ON vd.id_producto=p.id
        WHERE v.estado=1 AND vd.fecha_registro BETWEEN :ini1 AND :fin1
        GROUP BY p.nombre ORDER BY total DESC LIMIT 5", $params);

    $infoclientes = $run("SELECT c.nombre,
        SUM(v.valor_total) total,
        SUM(v.monto_pendiente) pendiente,
        COUNT(id_cliente) pedidos
        FROM ventas v
        JOIN clientes c ON v.id_cliente=c.id
        WHERE v.estado=1 AND v.fecha_registro BETWEEN :ini1 AND :fin1
        GROUP BY c.nombre ORDER BY total DESC LIMIT 5", $params);

    $infoclientestabla = $run("SELECT c.id,c.nombre,
        SUM(v.valor_total) total,
        SUM(v.monto_pendiente) pendiente,
        COUNT(id_cliente) pedidos
        FROM ventas v
        JOIN clientes c ON v.id_cliente=c.id
        WHERE v.estado=1 AND v.fecha_registro BETWEEN :ini1 AND :fin1
        GROUP BY c.id,c.nombre ORDER BY total DESC", $params);

    $infosucursales = $run("SELECT s.nombre, COUNT(id_sucursal) total
        FROM ventas v
        JOIN sucursales s ON v.id_sucursal=s.id
        WHERE v.estado=1 AND v.fecha_registro BETWEEN :ini1 AND :fin1
        GROUP BY s.nombre ORDER BY total DESC LIMIT 5", $params);

    $infocompras = $run("SELECT SUM(cantidad*precio) gasto,
        DATE_FORMAT(c.fecha_registro,'%Y-%m-%d') fecha
        FROM compras c
        JOIN compra_detalle d ON c.id=d.id_compra
        WHERE c.fecha_registro BETWEEN :ini1 AND :fin1
        GROUP BY fecha ORDER BY fecha", $params);

    $infoventas = $run("SELECT SUM(cantidad*precio) venta,
        DATE_FORMAT(v.fecha_registro,'%Y-%m-%d') fecha
        FROM ventas v
        JOIN venta_detalle d ON v.id=d.id_venta
        WHERE v.estado=1 AND v.fecha_registro BETWEEN :ini1 AND :fin1
        GROUP BY fecha ORDER BY fecha", $params);

    // 🔹 SQL grandes (solo cambiamos fechas dinámicas)
    $sql_reporte = str_replace(
        ["{$ini}","{$fin}"],
        [$ini,$fin],
        "SELECT v.id,cl.num_documento,v.fecha,vp.fecha_registro,'Venta' AS tipo_movimiento,u.nombre usuario,cl.nombre as cliente,cl.direccion,cl.telefono, s.nombre sucursal, c.nombre, valor_total,vp.monto, vp.monto_pendiente,v.observacion,vd.cantidad,p.nombre producto,p.codigo,p.unidad unidad_medida,vd.precio
FROM venta_pagos vp,ventas v,venta_detalle vd,usuarios u,sucursales s,cajas c ,clientes cl,productos p where  vp.cuentaPago=c.id and v.id_sucursal=s.id and vd.id_producto=p.id
and v.id=vp.id_venta and v.id_cliente=cl.id and vp.usuario=u.id and v.id=vd.id_venta and vp.fecha_registro between '{$ini} 00:00:01' and '{$fin} 23:59:59' and vp.monto>=0 and v.estado='1'
union all
SELECT v.id,cl.num_documento,v.fecha,vp.fecha_registro,'Compra'as tipo_movimiento,u.nombre usuario ,cl.razon_social as cliente,cl.direccion,cl.telefono, s.nombre sucursal, c.nombre,valor_total,vp.monto, vp.monto_pendiente,v.observacion,vd.cantidad,p.nombre producto,p.codigo,p.unidad unidad_medida,vd.precio
FROM compra_pagos vp,compras v,compra_detalle vd,usuarios u,sucursales s,cajas c,proveedores cl,productos p where  vp.cuentaPago=c.id and v.id_sucursal=s.id and vd.id_producto=p.id
and v.id=vp.id_compra and v.id_proveedor=cl.id and vp.usuario=u.id and v.id=vd.id_compra and  vp.fecha_registro between '{$ini} 00:00:01' and '{$fin} 23:59:59' and vp.monto>=0 ORDER BY `fecha_registro` DESC"
    );

    $infoventas_reporte = $pdo->query($sql_reporte)->fetchAll();

    $sql_caja = str_replace(
        ["{$ini}","{$fin}"],
        [$ini,$fin],
        "SELECT v.id,cl.num_documento,v.fecha,vp.fecha_registro,'Venta' AS tipo_movimiento,u.nombre usuario,cl.nombre as cliente,cl.direccion,cl.telefono, s.nombre sucursal, c.nombre, valor_total,vp.monto, vp.monto_pendiente,v.observacion
FROM venta_pagos vp,ventas v,usuarios u,sucursales s,cajas c ,clientes cl where vp.cuentaPago=c.id and v.id_sucursal=s.id
and v.id=vp.id_venta and v.id_cliente=cl.id and vp.usuario=u.id and vp.fecha_registro between '{$ini} 00:00:01' and '{$fin} 23:59:59' and vp.monto>=0 and v.estado='1'
union all
SELECT v.id,cl.num_documento,v.fecha,vp.fecha_registro,'Compra'as tipo_movimiento,u.nombre usuario ,cl.razon_social as cliente,cl.direccion,cl.telefono, s.nombre sucursal, c.nombre,valor_total,vp.monto, vp.monto_pendiente,v.observacion
FROM compra_pagos vp,compras v,usuarios u,sucursales s,cajas c,proveedores cl where  vp.cuentaPago=c.id and v.id_sucursal=s.id
and v.id=vp.id_compra and v.id_proveedor=cl.id and vp.usuario=u.id and vp.fecha_registro between '{$ini} 00:00:01' and '{$fin} 23:59:59' and vp.monto>=0 ORDER BY `fecha_registro` DESC"
    );

    $info_reporte_caja = $pdo->query($sql_caja)->fetchAll();

    // 🔹 Respuesta final
    $resp = [
        "status"=>200,
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
        "inicio"=>$ini,
        "final"=>$fin
    ];

    $response->getBody()->write(json_encode($resp));

    return $response->withHeader('Content-Type','application/json');
});


$app->post('/enviarboletas', function (Request $request, Response $response) use ($pdo) {

    $body = $request->getBody()->getContents();
    $dat = json_decode($body, true);

    if (!isset($dat['ids']) || !is_array($dat['ids'])) {
        $response->getBody()->write("IDs inválidos");
        return $response->withStatus(400);
    }

    // 🔒 Sanitizar IDs
    $ids = array_map('intval', $dat['ids']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));


    $sqlUpdate = "UPDATE ventas v
JOIN clientes c ON v.id_cliente = c.id
SET v.tipoDoc = CASE
    WHEN c.num_documento = '00000000000' OR LENGTH(c.num_documento) < 11 THEN 'Boleta'
    ELSE 'Factura'
END
WHERE v.id IN ($placeholders)";

$stmtUpdate = $pdo->prepare($sqlUpdate);
$stmtUpdate->execute($ids);


    $sql = "SELECT v.id, c.num_documento,
         v.id AS Procura,
        ROW_NUMBER() OVER(PARTITION BY v.id ORDER BY d.id) AS Item,
        p.nombre AS Producto, d.cantidad AS Cantidad,
        d.precio AS precio_unitario,
        p.codigo AS codigo_producto,
        p.unidad AS codigo_unidad,
        CASE
            WHEN c.num_documento = '00000000000' OR LENGTH(c.num_documento) < 11 THEN 'Boleta'
            ELSE 'Factura'
        END AS tipo_documento
    FROM ventas v
    JOIN venta_detalle d ON v.id = d.id_venta
    JOIN productos p ON d.id_producto = p.id
    JOIN clientes c ON v.id_cliente = c.id
    WHERE v.id IN ($placeholders)
    ORDER BY v.id, Item";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($ids);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 📊 Crear Excel
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // 🧾 Cabeceras
    $headers = [
        'RUC CLIENTE',
        'Procura',
        'Item',
        'Producto',
        'Cantidad',
        'PRECIO UNITARIO',
        'CODIGO PRODUCTO',
        'CODIGO UNIAD',
        'TIPO DOCUMENTO'
    ];

    $sheet->fromArray($headers, null, 'A1');

    // 🧾 Datos
    $rowIndex = 2;

    foreach ($rows as $row) {
        if($row['tipo_documento']=='Boleta'){
        $doc = str_pad($row['num_documento'], 8, "0", STR_PAD_LEFT);
        $tipo='B';

        }
        else{
        $doc = str_pad($row['num_documento'], 11, "0", STR_PAD_LEFT);
        $tipo='F';
        }

        $sheet->setCellValueExplicit("A{$rowIndex}", $doc, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue("B{$rowIndex}", $row['Procura']);
        $sheet->setCellValue("C{$rowIndex}", $row['Item']);
        $sheet->setCellValue("D{$rowIndex}", limpiarCadena($row['Producto']));
        $sheet->setCellValue("E{$rowIndex}", $row['Cantidad']);
        $sheet->setCellValue("F{$rowIndex}", $row['precio_unitario']);
        $sheet->setCellValue("G{$rowIndex}", $row['codigo_producto']);
        $sheet->setCellValue("H{$rowIndex}", $row['codigo_unidad']);
        $sheet->setCellValue("I{$rowIndex}", $tipo);

        $rowIndex++;
    }

    // 🎨 (Opcional) Auto tamaño columnas
    foreach (range('A','I') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // 📁 Crear archivo temporal
    $fileName = "boletas_" . date('Y-m-d') . ".xlsx";
    $tempFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $fileName;

    $writer = new Xlsx($spreadsheet);
    $writer->save($tempFile);

    // 📤 Enviar al navegador
    $stream = new \Slim\Psr7\Stream(fopen($tempFile, 'rb'));

    return $response
        ->withBody($stream)
        ->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
        ->withHeader('Content-Disposition', "attachment; filename=\"$fileName\"")
        ->withHeader('Cache-Control', 'max-age=0');
});


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




$app->post('/exportar', function (Request $request, Response $response) use ($pdo) {

    $dat = json_decode($request->getBody()->getContents(), true);

    // ---- Formateo de fechas ----
    $arraymeses = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    $arraynros  = ['01','02','03','04','05','06','07','08','09','10','11','12'];

    $mes1 = substr($dat['ini'], 3, 3);
    $mes2 = substr($dat['fin'], 3, 3);
    $dia1 = substr($dat['ini'], 0, 2);
    $dia2 = substr($dat['fin'], 0, 2);
    $ano1 = substr($dat['ini'], 7, 4);
    $ano2 = substr($dat['fin'], 7, 4);

    $ini = $ano1 . '-' . str_replace($arraymeses, $arraynros, $mes1) . '-' . $dia1 . " 00:00:00";
    $fin = $ano2 . '-' . str_replace($arraymeses, $arraynros, $mes2) . '-' . $dia2 . " 23:59:00";

    try {

        // ---- Consulta ----
        $sql = "SELECT v.id,
                       cl.num_documento,
                       cl.nombre,
                       p.codigo,
                       p.nombre AS producto,
                       c.nombre categoria,
                       sc.nombre subcategoria,
                       fa.nombre familia,
                       vd.cantidad,
                       p.unidad,
                       vd.precio,
                       (vd.cantidad * vd.precio) AS valor_total,
                       'Venta' AS movimiento,
                       u.nombre AS usuario,
                       s.nombre AS sucursal,
                       v.fecha_registro,
                       v.observacion
                FROM venta_detalle vd
                INNER JOIN ventas v ON v.id = vd.id_venta
                INNER JOIN usuarios u ON v.id_usuario = u.id
                INNER JOIN sucursales s ON v.id_sucursal = s.id
                INNER JOIN clientes cl ON v.id_cliente = cl.id
                INNER JOIN productos p ON vd.id_producto = p.id
                JOIN categorias c ON p.id_categoria = c.id
                JOIN sub_categorias sc ON p.id_subcategoria = sc.id
                JOIN sub_sub_categorias fa ON p.id_sub_sub_categoria = fa.id
                WHERE v.estado = 1
                AND v.fecha_registro BETWEEN :ini AND :fin

                UNION ALL

                SELECT v.id,
                       cl.num_documento,
                       cl.nombre,
                       p.codigo,
                       p.nombre,
                       c.nombre,
                       sc.nombre,
                       fa.nombre,
                       vp.cantidad,
                       p.unidad,
                       vp.precio,
                       (vp.cantidad * vp.precio),
                       'Compra',
                       u.nombre,
                       s.nombre,
                       vp.fecha_registro,
                       v.observacion
                FROM compra_detalle vp
                INNER JOIN compras v ON v.id = vp.id_compra
                INNER JOIN usuarios u ON v.id_usuario = u.id
                INNER JOIN sucursales s ON v.id_sucursal = s.id
                LEFT JOIN clientes cl ON v.id_proveedor = cl.id
                INNER JOIN productos p ON vp.id_producto = p.id
                JOIN categorias c ON p.id_categoria = c.id
                JOIN sub_categorias sc ON p.id_subcategoria = sc.id
                JOIN sub_sub_categorias fa ON p.id_sub_sub_categoria = fa.id
                WHERE vp.fecha_registro BETWEEN :ini AND :fin

                ORDER BY fecha_registro DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'ini' => $ini,
            'fin' => $fin
        ]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ---- Crear Excel ----
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $headers = ['ID','Fecha','Documento','Razon Social','Codigo','Producto','Categoria','Subcategoria','Familia','Cantidad','Unidad','Precio','Total','Movimiento','Usuario','Sucursal','Observacion'];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        // Datos
        $rowIndex = 2;

        foreach ($rows as $row) {

            $doc = preg_replace('/\D/', '', $row['num_documento']);

            // 🔥 Ajuste según tipo (si luego agregas tipo_documento)
            if (strlen($doc) <= 8) {
                $doc = str_pad($doc, 8, "0", STR_PAD_LEFT);
            } else {
                $doc = str_pad($doc, 11, "0", STR_PAD_LEFT);
            }


            $sheet->setCellValueExplicit("A{$rowIndex}", $row['id'], DataType::TYPE_NUMERIC);
            $sheet->setCellValue("B{$rowIndex}", $row['fecha_registro']);
            $sheet->setCellValueExplicit("C{$rowIndex}", $doc, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("D{$rowIndex}", $row['nombre']);
            $sheet->setCellValue("E{$rowIndex}", $row['codigo']);
            $sheet->setCellValue("F{$rowIndex}", $row['producto']);
            $sheet->setCellValue("G{$rowIndex}", $row['categoria']);
            $sheet->setCellValue("H{$rowIndex}", $row['subcategoria']);
            $sheet->setCellValue("I{$rowIndex}", $row['familia']);
            $sheet->setCellValue("J{$rowIndex}", $row['cantidad']);
            $sheet->setCellValue("K{$rowIndex}", $row['unidad']);
            $sheet->setCellValue("L{$rowIndex}", $row['precio']);
            $sheet->setCellValue("M{$rowIndex}", $row['valor_total']);
            $sheet->setCellValue("N{$rowIndex}", $row['movimiento']);
            $sheet->setCellValue("O{$rowIndex}", $row['usuario']);
            $sheet->setCellValue("P{$rowIndex}", $row['sucursal']);
            $sheet->setCellValue("Q{$rowIndex}", $row['observacion']);

            $rowIndex++;
        }

        // ---- Output ----
        $writer = new Xlsx($spreadsheet);

        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        $response->getBody()->write($excelOutput);

        return $response
            ->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->withHeader('Content-Disposition', 'attachment; filename="reporte.xlsx"');

    } catch (Exception $e) {

        $response->getBody()->write(json_encode([
            "STATUS" => false,
            "message" => $e->getMessage()
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }
});



$app->post('/exportarcaja', function (Request $request, Response $response) use ($pdo) {

    $dat = json_decode($request->getBody()->getContents(), true);

    // ---- Fechas ----
    $arraymeses = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    $arraynros  = ['01','02','03','04','05','06','07','08','09','10','11','12'];

    $mes1 = substr($dat['ini'], 3, 3);
    $mes2 = substr($dat['fin'], 3, 3);
    $dia1 = substr($dat['ini'], 0, 2);
    $dia2 = substr($dat['fin'], 0, 2);
    $ano1 = substr($dat['ini'], 7, 4);
    $ano2 = substr($dat['fin'], 7, 4);

    $ini = $ano1 . '-' . str_replace($arraymeses, $arraynros, $mes1) . '-' . $dia1 . " 00:00:01";
    $fin = $ano2 . '-' . str_replace($arraymeses, $arraynros, $mes2) . '-' . $dia2 . " 23:59:59";

    try {

        // ---- SQL seguro ----
        $sql = "SELECT
                    vp.id_venta as id,
                    vp.fecha_registro,
                    v.fecha,
                    cl.num_documento AS documento,
                    cl.nombre AS cliente,
                    'VENTA' AS movimiento,
                    u.nombre AS usuario,
                    s.nombre AS sucursal,
                    tp.nombre AS tipopago,
                    c.nombre AS cuenta,
                    vp.numero_operacion,
                    vp.monto,
                    vp.monto_pendiente,
                    v.observacion
                FROM venta_pagos vp
                JOIN ventas v ON v.id = vp.id_venta AND v.estado=1
                JOIN clientes cl ON cl.id = v.id_cliente
                JOIN sucursales s ON s.id = v.id_sucursal
                JOIN usuarios u ON u.id = vp.usuario
                JOIN tipoPago tp ON tp.id = vp.tipoPago
                JOIN cajas c ON c.id = vp.cuentaPago
                WHERE vp.fecha_registro BETWEEN :ini AND :fin

                UNION ALL

                SELECT
                    vp.id_compra,
                    vp.fecha_registro,
                    v.fecha,
                    cl.num_documento,
                    cl.razon_social,
                    'COMPRA',
                    u.nombre,
                    s.nombre,
                    tp.nombre,
                    c.nombre,
                    vp.numero_operacion,
                    vp.monto,
                    vp.monto_pendiente,
                    v.observacion
                FROM compra_pagos vp
                JOIN compras v ON v.id = vp.id_compra AND v.estado=1
                JOIN proveedores cl ON cl.id = v.id_proveedor
                JOIN sucursales s ON s.id = v.id_sucursal
                JOIN usuarios u ON u.id = vp.usuario
                JOIN tipoPago tp ON tp.id = vp.tipoPago
                JOIN cajas c ON c.id = vp.cuentaPago
                WHERE vp.fecha_registro BETWEEN :ini AND :fin

                ORDER BY fecha_registro DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'ini' => $ini,
            'fin' => $fin
        ]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ---- Excel ----
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['ID','Fecha','Fecha Registro','Documento','Cli/Prov','Movimiento','Usuario','Sucursal','Medio pago','Cuenta','Operacion','Monto','Monto Pendiente','Observacion'];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        $rowIndex = 2;

        foreach ($rows as $row) {

            $doc = preg_replace('/\D/', '', $row['documento']);

            // DNI o RUC
            if (strlen($doc) <= 8) {
                $doc = str_pad($doc, 8, "0", STR_PAD_LEFT);
            } else {
                $doc = str_pad($doc, 11, "0", STR_PAD_LEFT);
            }

            $sheet->setCellValue("A{$rowIndex}", $row['id']);
            $sheet->setCellValue("B{$rowIndex}", $row['fecha']);
            $sheet->setCellValue("C{$rowIndex}", $row['fecha_registro']);

            // 🔥 IMPORTANTE: string para no perder ceros
            $sheet->setCellValueExplicit("D{$rowIndex}", $doc, DataType::TYPE_STRING);

            $sheet->setCellValue("E{$rowIndex}", $row['cliente']);
            $sheet->setCellValue("F{$rowIndex}", $row['movimiento']);
            $sheet->setCellValue("G{$rowIndex}", $row['usuario']);
            $sheet->setCellValue("H{$rowIndex}", $row['sucursal']);
            $sheet->setCellValue("I{$rowIndex}", $row['tipopago']);
            $sheet->setCellValue("J{$rowIndex}", $row['cuenta']);
            $sheet->setCellValue("K{$rowIndex}", $row['numero_operacion']);
            $sheet->setCellValue("L{$rowIndex}", $row['monto']);
            $sheet->setCellValue("M{$rowIndex}", $row['monto_pendiente']);
            $sheet->setCellValue("N{$rowIndex}", $row['observacion']);

            $rowIndex++;
        }

        // ---- Salida ----
        $writer = new Xlsx($spreadsheet);

        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        $response->getBody()->write($excelOutput);

        return $response
            ->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->withHeader('Content-Disposition', 'attachment; filename="reporte_caja.xlsx"');

    } catch (Exception $e) {

        $response->getBody()->write(json_encode([
            "STATUS" => false,
            "message" => $e->getMessage()
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }
});




$app->post('/exportarclientes', function (Request $request, Response $response) use ($pdo) {

    $dat = json_decode($request->getBody()->getContents(), true);

    // ---- Fechas ----
    $arraymeses = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    $arraynros  = ['01','02','03','04','05','06','07','08','09','10','11','12'];

    $mes1 = substr($dat['ini'], 3, 3);
    $mes2 = substr($dat['fin'], 3, 3);
    $dia1 = substr($dat['ini'], 0, 2);
    $dia2 = substr($dat['fin'], 0, 2);
    $ano1 = substr($dat['ini'], 7, 4);
    $ano2 = substr($dat['fin'], 7, 4);

    $ini = $ano1 . '-' . str_replace($arraymeses, $arraynros, $mes1) . '-' . $dia1 . " 00:00:00";
    $fin = $ano2 . '-' . str_replace($arraymeses, $arraynros, $mes2) . '-' . $dia2 . " 23:59:00";

    try {

        // ---- SQL seguro ----
        $sql = "SELECT
                    c.id,
                    c.nombre,
                    SUM(v.valor_total) AS total,
                    SUM(v.monto_pendiente) AS pendiente,
                    COUNT(v.id_cliente) AS pedidos
                FROM ventas v
                INNER JOIN clientes c ON v.id_cliente = c.id
                WHERE v.fecha_registro BETWEEN :ini AND :fin
                GROUP BY c.id, c.nombre
                ORDER BY total DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'ini' => $ini,
            'fin' => $fin
        ]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ---- Crear Excel ----
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['ID','Nombre','Total','Pendiente','Pedidos'];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        $rowIndex = 2;

        foreach ($rows as $row) {

            $sheet->setCellValue("A{$rowIndex}", $row['id']);
            $sheet->setCellValue("B{$rowIndex}", $row['nombre']);
            $sheet->setCellValue("C{$rowIndex}", $row['total']);
            $sheet->setCellValue("D{$rowIndex}", $row['pendiente']);
            $sheet->setCellValue("E{$rowIndex}", $row['pedidos']);

            $rowIndex++;
        }

        // ---- Exportar ----
        $writer = new Xlsx($spreadsheet);

        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        $response->getBody()->write($excelOutput);

        return $response
            ->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->withHeader('Content-Disposition', 'attachment; filename="reporte_clientes.xlsx"');

    } catch (Exception $e) {

        $response->getBody()->write(json_encode([
            "STATUS" => false,
            "message" => $e->getMessage()
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }
});




$app->get('/boleta/{id}', function (Request $request, Response $response, $args) use ($pdo) {

    $id = $args['id'];

    $sql = "SELECT a.nombre,a.unidad, d.*,c.nombre as cliente,c.num_documento,
                   v.*,pa.*,tp.tipo,s.direccion,s.email,
                   s.nombre as local,s.telefono
            FROM venta_detalle d
            INNER JOIN productos a ON a.id=d.id_producto
            INNER JOIN ventas v ON d.id_venta=v.id
            INNER JOIN clientes c ON v.id_cliente=c.id
            INNER JOIN venta_pagos pa ON v.id=pa.id_venta
            INNER JOIN tipoPago tp ON pa.tipoPago=tp.id
            INNER JOIN sucursales s ON s.id=v.id_sucursal
            WHERE v.id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    $prods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$prods) {
        $response->getBody()->write("No hay datos");
        return $response->withStatus(404);
    }

    // ================= PDF =================
    $pdf = new \FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','',12);

    $pageWidth = $pdf->GetPageWidth();
    $imgWidth = 90;
    $x = ($pageWidth - $imgWidth) / 2;

    $pdf->Image('logo.png', $x, 10, $imgWidth);
    $pdf->Ln(34);

    $pdf->SetFont('Arial','B',16);

    if($prods[0]['local']!="C.J.M"){
        $pdf->Cell(0,6,'FERRETERIA Y MATERIALES DE CONSTRUCCION LAS',0,1,'C');
        $pdf->Cell(0,10,'HERMANITAS E.I.R.L.',0,1,'C');
    }else{
        $pdf->Cell(0,6,$prods[0]['local'],0,1,'C');
    }

    $pdf->SetFont('Arial','',17);
    $pdf->Cell(0,8,'Whatsap/Telefono: '.$prods[0]['telefono'],0,1,'C');
    $pdf->Cell(0,8,$prods[0]['email'],0,1,'C');
    $pdf->Cell(0,8,$prods[0]['direccion'],0,1,'C');
    $pdf->Cell(0,8,'- Lima - Lima',0,1,'C');

    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(0,8,'RUC: 20537929520',0,1,'C');
    $pdf->Cell(0,8,'TICKET NRO:'.$prods[0]['id_venta'],0,1,'C');

    $pdf->Ln(10);
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(0,8,'ADQUIRIENTE',0,1,'L');

    $pdf->SetFont('Arial','',15);
    $pdf->Cell(0,8,$prods[0]['cliente'],0,1,'L');
    $pdf->Cell(0,8,'DOC:'.$prods[0]['num_documento'],0,1,'L');

    $pdf->Ln(10);
    $pdf->SetFont('Arial','B',17);
    $pdf->Cell(0,8,'FECHA:'.date('d/m/Y H:i:s', strtotime($prods[0]['fecha_registro'])),0,1,'L');

    $pdf->SetFont('Arial','',17);
    $pdf->Cell(0,8,'FORMA PAGO:'.strtoupper($prods[0]['tipo']),0,1,'L');

    // ENCABEZADO
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(85,10,'DESCRIPCION',0,0,'L');
    $pdf->Cell(30,10,'CANTIDAD',0,0,'C');
    $pdf->Cell(20,10,'U.M',0,0,'C');
    $pdf->Cell(30,10,'PRECIO',0,0,'C');
    $pdf->Cell(30,10,'IMPORTE',0,1,'C');

    $pdf->Cell(150,2,str_repeat('-',100),0,1);
    $pdf->Ln();

    // DETALLE
    $pdf->SetFont('Arial','',13);

    foreach ($prods as $prod) {
        $pdf->Cell(130,6,$prod['nombre'],0,1);
        $pdf->Cell(95,6,'',0,0);
        $pdf->Cell(30,6,round($prod['cantidad'],2),0,0);
        $pdf->Cell(20,6,$prod['unidad'],0,0);
        $pdf->Cell(28,6,round($prod['precio'],2),0,0);
        $pdf->Cell(28,6,round($prod['subtotal'],2),0,1);
    }

    $pdf->Ln();
    $pdf->Cell(150,2,str_repeat('-',100),0,1);
    $pdf->Ln();

    // TOTALES
    $pdf->Cell(115);
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(40,8,'OP. AGRAVADAS S/',0,0,'R');

    $pdf->SetFont('Arial','',15);
    $pdf->Cell(30,8,$prods[0]['valor_total'],0,1,'R');

    $pdf->Cell(115);
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(40,8,'TOTAL S/',0,0,'R');

    $pdf->SetFont('Arial','',14);
    $pdf->Cell(30,8,$prods[0]['valor_total'],0,1,'R');

    $pdf->Cell(150,2,str_repeat('-',100),0,1);

    $cantidad = numeroALetras($prods[0]['valor_total']);

    $pdf->SetFont('Arial','',17);
    $pdf->Cell(180,14,$cantidad,0,1,'C');

    $pdf->Cell(150,2,str_repeat('-',100),0,1);

    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(35,14,strtoupper($prods[0]['tipo']),0,0,'L');

    $pdf->SetFont('Arial','',14);
    $pdf->Cell(20,14,'S/ '.$prods[0]['valor_total'],0,1,'L');

    $pdf->Cell(150,2,str_repeat('-',100),0,1);

    // 🔥 RESPUESTA CORRECTA
    $pdfContent = $pdf->Output('S');

    $response->getBody()->write($pdfContent);

    return $response
        ->withHeader('Content-Type', 'application/pdf')
        ->withHeader('Content-Disposition', 'inline; filename="reporte.pdf"');
});

function numeroALetras($numero) {
    $unidad = [
        '', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO',
        'SEIS', 'SIETE', 'OCHO', 'NUEVE', 'DIEZ',
        'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE',
        'DIECISÉIS', 'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE', 'VEINTE'
    ];

    $decenas = [
        20 => 'VEINTE', 30 => 'TREINTA', 40 => 'CUARENTA',
        50 => 'CINCUENTA', 60 => 'SESENTA',
        70 => 'SETENTA', 80 => 'OCHENTA', 90 => 'NOVENTA'
    ];

    $centenas = [
        100 => 'CIEN', 200 => 'DOSCIENTOS', 300 => 'TRESCIENTOS',
        400 => 'CUATROCIENTOS', 500 => 'QUINIENTOS',
        600 => 'SEISCIENTOS', 700 => 'SETECIENTOS',
        800 => 'OCHOCIENTOS', 900 => 'NOVECIENTOS'
    ];

    $numero = number_format($numero, 2, '.', '');
    list($entero, $decimal) = explode('.', $numero);

    $entero = (int)$entero;
    $decimal = (int)$decimal;

    $convertir = function($n) use (&$convertir, $unidad, $decenas, $centenas) {
        if ($n == 0) return 'CERO';
        if ($n <= 20) return $unidad[$n];
        if ($n < 100) {
            $d = (int) (floor($n / 10) * 10);
            $r = $n % 10;
            return $decenas[$d] . ($r > 0 ? ' Y ' . $convertir($r) : '');
        }
        if ($n < 1000) {
            $c = (int) (floor($n / 100) * 100);
            $r = $n % 100;
            if ($n == 100) return 'CIEN';
            return $centenas[$c] . ($r > 0 ? ' ' . $convertir($r) : '');
        }
        if ($n < 1000000) {
            $m = floor($n / 1000);
            $r = $n % 1000;
            $mil = ($m == 1) ? 'MIL' : $convertir($m) . ' MIL';
            return $mil . ($r > 0 ? ' ' . $convertir($r) : '');
        }
        if ($n < 1000000000) {
            $mi = floor($n / 1000000);
            $r = $n % 1000000;
            $millones = ($mi == 1) ? 'UN MILLÓN' : $convertir($mi) . ' MILLONES';
            return $millones . ($r > 0 ? ' ' . $convertir($r) : '');
        }
        return '';
    };

    $texto = $convertir($entero);
    if ($decimal > 0) {
        $texto .= ' CON ' . str_pad($decimal, 2, '0', STR_PAD_LEFT) . '/100';
    }
    return "SON: " . $texto . " SOLES";
}




$app->post('/productos', function (Request $request, Response $response) use ($pdo) {

    try {

        $sql = "SELECT
                    p.id,
                    p.codigo,
                    p.nombre,
                    c.nombre AS categoria,
                    sc.nombre AS subcategoria,
                    fa.nombre AS familia,
                    p.unidad,
                    p.precio,
                    p.precio_compra,
                    p.imagen
                FROM productos p
                LEFT JOIN categorias c
                    ON p.id_categoria = c.id
                LEFT JOIN sub_categorias sc
                    ON p.id_subcategoria = sc.id
                LEFT JOIN sub_sub_categorias fa
                    ON p.id_sub_sub_categoria = fa.id
                ORDER BY p.id DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ---- Crear Excel ----
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ---- Cabeceras ----
        $headers = [
            'ID',
            'CODIGO',
            'DESCRIPCION',
            'CATEGORIA',
            'SUBCATEGORIA',
            'FAMILIA',
            'UNIDAD',
            'PRECIO',
            'PRECIO_COMPRA'
        ];

        $col = 'A';

        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        // ---- Datos ----
        $rowIndex = 2;

        foreach ($rows as $row) {

            $sheet->setCellValue("A{$rowIndex}", $row['id']);
            $sheet->setCellValue("B{$rowIndex}", $row['codigo']);
            $sheet->setCellValue("C{$rowIndex}", limpiarCadena($row['nombre']));
            $sheet->setCellValue("D{$rowIndex}", $row['categoria']);
            $sheet->setCellValue("E{$rowIndex}", $row['subcategoria']);
            $sheet->setCellValue("F{$rowIndex}", $row['familia']);
            $sheet->setCellValue("G{$rowIndex}", $row['unidad']);
            $sheet->setCellValue("H{$rowIndex}", $row['precio']);
            $sheet->setCellValue("I{$rowIndex}", $row['precio_compra']);

            $rowIndex++;
        }

        // ---- Exportar ----
        $writer = new Xlsx($spreadsheet);

        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        $fileName = "productos_" . date('Y-m-d') . ".xls";

        $response->getBody()->write($excelOutput);

        return $response
            ->withHeader(
                'Content-Type',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            )
            ->withHeader(
                'Content-Disposition',
                'attachment; filename="' . $fileName . '"'
            );

    } catch (Exception $e) {

        $response->getBody()->write(json_encode([
            "STATUS" => false,
            "message" => $e->getMessage()
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
});


$app->run();