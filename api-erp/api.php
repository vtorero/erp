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
use Slim\Factory\AppFactory;

$app = AppFactory::create();
/*Produccion
$dsn = "mysql:host=lh-cjm.com;dbname=aprendea_erp;port=3306;charset=utf8";
$usuario="aprendea_erp";
$clave="erp2023*";
*/
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
$app->setBasePath('/slim/api.php');

$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$app->get('/ventas', function (Request $request, Response $response) use ($pdo) {

    $sql = "SELECT
                v.id,
                c.num_documento,
                c.telefono,
                c.direccion,
                c.id as id_cliente,
                c.nombre as cliente,
                u.nombre,
                v.tipoDoc,
                v.id_vendedor,
                v.id_sucursal,
                DATE_FORMAT(v.fecha_registro, '%d-%m-%Y') as fechaPago,
                IF(v.pendientes=0,'No','Si') as pendientes,
                v.igv,
                v.monto_igv,
                v.descuento,
                v.valor_neto,
                v.valor_total,
                v.monto_pendiente,
                CASE
                    WHEN v.estado ='1' THEN 'Registrado'
                    WHEN v.estado = '2' THEN 'Anulado'
                END as estado,
                v.observacion
            FROM ventas v
            INNER JOIN clientes c ON v.id_cliente = c.id AND v.estado = 1
            INNER JOIN usuarios u ON v.id_usuario = u.id
            WHERE DATE_FORMAT(v.fecha_registro, '%d-%m-%Y') = DATE_FORMAT(NOW(), '%d-%m-%Y')
            ORDER BY v.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $prods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $payload = json_encode($prods);

    $response->getBody()->write($payload);

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

$app->post('/consulta-ventas', function (Request $request, Response $response) use ($pdo) {
       $body = $request->getBody()->getContents();
    $j = json_decode($body, true);

    // En tu código original viene doble JSON
    $dat = json_decode($j['json']);

    $arraymeses = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    $arraynros  = ['01','02','03','04','05','06','07','08','09','10','11','12'];

    $mes1 = substr($dat->ini, 0, 3);
    $mes2 = substr($dat->fin, 0, 3);
    $dia1 = substr($dat->ini, 3, 2);
    $dia2 = substr($dat->fin, 3, 2);
    $ano1 = substr($dat->ini, 5, 4);
    $ano2 = substr($dat->fin, 5, 4);

    $fmes1 = str_replace($arraymeses, $arraynros, $mes1);
    $fmes2 = str_replace($arraymeses, $arraynros, $mes2);

    $ini = $ano1 . '-' . $fmes1 . '-' . $dia1;
    $fin = $ano2 . '-' . $fmes2 . '-' . $dia2;

    // 🔐 QUERY SEGURA CON PDO
    $sql = "SELECT
                v.id,
                v.estado,
                c.num_documento,
                c.telefono,
                c.direccion,
                c.id as id_cliente,
                c.nombre as cliente,
                u.nombre,
                v.tipoDoc,
                v.id_vendedor,
                v.id_sucursal,
                DATE_FORMAT(v.fecha_registro, '%d-%m-%Y') as fechaPago,
                IF(v.pendientes=0,'No','Si') as pendientes,
                v.igv,
                v.monto_igv,
                v.descuento,
                v.valor_neto,
                v.valor_total,
                v.monto_pendiente,
                CASE
                    WHEN v.estado ='1' THEN 'Registrado'
                    WHEN v.estado = '2' THEN 'Anulado'
                END as estado,
                v.observacion
            FROM ventas v
            INNER JOIN clientes c ON v.id_cliente = c.id
            INNER JOIN usuarios u ON v.id_usuario = u.id
            WHERE v.fecha_registro BETWEEN :ini AND :fin
              AND v.estado = :estado
            ORDER BY v.id DESC";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':ini'    => $ini . ' 00:00:01',
        ':fin'    => $fin . ' 23:59:59',
        ':estado' => $dat->estado
    ]);

    $prods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response->getBody()->write(json_encode($prods));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});


$app->post('/login', function (Request $request, Response $response) use ($pdo) {

    $data = json_decode($request->getBody()->getContents(), true);

    $sql = "SELECT u.*, s.id id_sucursal, s.nombre sucursal, s.direccion, s.telefono
            FROM usuarios u
            INNER JOIN permisos p ON u.id = p.id_usuario
            INNER JOIN sucursales s ON p.id_sucursal = s.id
            WHERE p.estado = 1
            AND u.nombre = :usuario
            AND u.contrasena = :password limit 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':usuario' => $data['usuario'],
        ':password' => $data['password']
    ]);

    $usuario = $stmt->fetchAll();

    $resp = (count($usuario) > 0)
        ? ["status" => true, "rows" => count($usuario), "data" => $usuario]
        : ["status" => false, "rows" => 0, "data" => null];

    $response->getBody()->write(json_encode($resp));

    return $response->withHeader('Content-Type', 'application/json');
});



$app->get('/articulos', function (Request $request, Response $response) use ($pdo) {

    $sql = "SELECT
                p.id,
                p.codigo,
                p.codigobarras,
                p.nombre,
                c.nombre AS categoria,
                sc.nombre AS subcategoria,
                fa.nombre AS familia,
                p.unidad,
                p.precio,
                p.precio_compra,
                p.imagen
            FROM productos p
            LEFT JOIN categorias c ON p.id_categoria = c.id
            LEFT JOIN sub_categorias sc ON p.id_subcategoria = sc.id
            LEFT JOIN sub_sub_categorias fa ON p.id_sub_sub_categoria = fa.id
            ORDER BY p.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $prods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response->getBody()->write(json_encode($prods));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

$app->get('/categorias', function (Request $request, Response $response) use ($pdo) {

    $sql = "SELECT id, nombre FROM categorias ORDER BY id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response->getBody()->write(json_encode($categorias));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

$app->post('/subcategoria', function (Request $request, Response $response) use ($pdo) {

    $body = $request->getBody()->getContents();
    $j = json_decode($body, true);

    // Tu doble JSON original
    $data = json_decode($j['json']);

    try {
        $sql = "INSERT INTO sub_categorias (id_categoria, nombre,usuario)
                VALUES (:id_categoria, :nombre,'admin')";

        $stmt = $pdo->prepare($sql);

        $proceso = $stmt->execute([
            ':id_categoria' => $data->id_categoria,
            ':nombre'       => $data->nombre
        ]);

        if ($proceso) {
            $result = [
                "STATUS"  => true,
                "messaje" => "Subcategoría creada correctamente"
            ];
        } else {
            $result = [
                "STATUS"  => false,
                "messaje" => "Ocurrió un error en la creación"
            ];
        }

    } catch (PDOException $e) {
        $result = [
            "STATUS"  => false,
            "messaje" => $e->getMessage()
        ];
    }

    $response->getBody()->write(json_encode($result));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

$app->post('/familia', function ($request, $response) use ($pdo) {

    header("Content-Type: application/json; charset=utf-8");

    $body = $request->getBody()->getContents();

    $j = json_decode($body, true);

    $data = json_decode($j['json']);

    try {

        $sql = "INSERT INTO sub_sub_categorias (
                    id_subcategoria,
                    nombre,
                    usuario
                ) VALUES (
                    :id_subcategoria,
                    :nombre,
                    'admin'
                )";

        $stmt = $pdo->prepare($sql);

        $proceso = $stmt->execute([
            ':id_subcategoria' => $data->id_subCategoria,
            ':nombre' => $data->nombre
        ]);

        if ($proceso) {

            $result = [
                "STATUS" => true,
                "messaje" => "Familia creada correctamente"
            ];

        } else {

            $result = [
                "STATUS" => false,
                "messaje" => "Ocurrió un error en la creación"
            ];
        }

    } catch (PDOException $e) {

        $result = [
            "STATUS" => false,
            "messaje" => $e->getMessage()
        ];
    }

    $response->getBody()->write(json_encode($result));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});


$app->get('/subcategoria/{criterio}', function (Request $request, Response $response, $args) use ($pdo) {

    $criterio = $args['criterio'];

    $sql = "SELECT
                sc.nombre,
                p.id_subcategoria AS id,
                p.id_categoria
            FROM productos p
            INNER JOIN sub_categorias sc ON p.id_subcategoria = sc.id
            WHERE p.id_categoria = :criterio
            GROUP BY sc.nombre, p.id_subcategoria, p.id_categoria
            ORDER BY sc.nombre ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':criterio' => $criterio
    ]);

    $prods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response->getBody()->write(json_encode($prods));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});


$app->post('/buscargeneral', function (Request $request, Response $response) use ($pdo) {

    $body = $request->getBody()->getContents();
    $j = json_decode($body, true);

    // Tu doble JSON original
    $data = json_decode($j['json']);

    try {

        $sql = "SELECT * FROM productos WHERE id_categoria = :cat";
        $params = [
            ':cat' => $data->cat
        ];

        if ($data->tipo === 'subcategoria') {
            $sql .= " AND id_subcategoria = :sub";
            $params[':sub'] = $data->sub;
        }

        if ($data->tipo === 'familia') {
            $sql .= " AND id_subcategoria = :sub AND id_sub_sub_categoria = :fam";
            $params[':sub'] = $data->sub;
            $params[':fam'] = $data->fam;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $prods = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($prods));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);

    } catch (PDOException $e) {

        $error = [
            "STATUS"  => false,
            "message" => $e->getMessage()
        ];

        $response->getBody()->write(json_encode($error));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
});

$app->get('/familia/{criterio}', function (Request $request, Response $response, $args) use ($pdo) {

    $criterio = $args['criterio'];

    $sql = "SELECT
                f.nombre,
                p.id_sub_sub_categoria AS id,
                p.id_subcategoria
            FROM productos p
            INNER JOIN sub_sub_categorias f ON p.id_sub_sub_categoria = f.id
            WHERE p.id_subcategoria = :criterio
            GROUP BY f.nombre, p.id_sub_sub_categoria, p.id_subcategoria
            ORDER BY f.nombre ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':criterio' => $criterio
    ]);

    $prods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response->getBody()->write(json_encode($prods));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

$app->post('/buscaarticulos', function (Request $request, Response $response) use ($pdo) {

    $body = $request->getBody()->getContents();
    $j = json_decode($body, true);

    // Tu doble JSON original
    $data = json_decode($j['json']);

    try {

        // Separar palabras de búsqueda
        $palabras = explode(" ", trim($data));

        $whereParts = [];
        $params = [];

        foreach ($palabras as $index => $palabra) {
            $key = ":palabra" . $index;
            $whereParts[] = "p.nombre LIKE $key";
            $params[$key] = "%" . $palabra . "%";
        }

        // Construcción dinámica segura
        $whereNombre = implode(" AND ", $whereParts);

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
                LEFT JOIN categorias c ON p.id_categoria = c.id
                LEFT JOIN sub_categorias sc ON p.id_subcategoria = sc.id
                LEFT JOIN sub_sub_categorias fa ON p.id_sub_sub_categoria = fa.id
                WHERE ($whereNombre)
                   OR p.codigo LIKE :codigo
                   OR p.codigobarras LIKE :codigobarras";

        $params[':codigo'] = "%" . $data . "%";
        $params[':codigobarras'] = "%" . $data . "%";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $prods = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($prods));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);

    } catch (PDOException $e) {

        $error = [
            "STATUS" => false,
            "message" => $e->getMessage()
        ];

        $response->getBody()->write(json_encode($error));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
});


$app->get('/clientes', function (Request $request, Response $response) use ($pdo) {

    $sql = "SELECT * FROM clientes ORDER BY nombre ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response->getBody()->write(json_encode($clientes));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});


$app->get('/compras', function (Request $request, Response $response) use ($pdo) {

    $sql = "SELECT
                v.id,
                c.id AS id_proveedor,
                c.telefono,
                c.num_documento,
                c.razon_social AS cliente,
                u.nombre,
                v.tipoDoc,
                v.serie_documento,
                v.nro_documento,
                v.id_sucursal,
                DATE_FORMAT(v.fecha, '%d-%m-%Y') AS fecha,
                DATE_FORMAT(v.fecha_registro, '%d-%m-%Y') AS fechaPago,
                IF(v.pendientes=0,'No','Si') AS pendientes,
                CASE
                    WHEN v.estado ='1' THEN 'Registrado'
                    WHEN v.estado = '2' THEN 'Anulado'
                END AS estado,
                v.igv,
                v.monto_igv,
                v.descuento,
                v.valor_neto,
                v.valor_total,
                v.monto_pendiente,
                v.observacion
            FROM compras v
            INNER JOIN proveedores c ON v.id_proveedor = c.id
            INNER JOIN usuarios u ON v.id_usuario = u.id AND v.estado = 1
            ORDER BY v.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $compras = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response->getBody()->write(json_encode($compras));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});


$app->get('/proveedores', function (Request $request, Response $response) use ($pdo) {

    $sql = "SELECT * FROM proveedores ORDER BY id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response->getBody()->write(json_encode($proveedores));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

$app->get('/inventario', function (Request $request, Response $response) use ($pdo) {

    $sql = "SELECT
                i.producto_id,
                a.nombre,
                a.codigo,
                i.id_almacen,
                s.nombre AS almacen,
                i.cantidad,
                i.fecha_actualizacion
            FROM inventario i
            INNER JOIN productos a ON a.id = i.producto_id
            INNER JOIN sucursales s ON i.id_almacen = s.id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $inventario = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response->getBody()->write(json_encode($inventario));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});


$app->get('/tabla/{tabla}', function (Request $request, Response $response, $args) use ($pdo) {

    $tabla = $args['tabla'];

    // ✅ LISTA BLANCA (obligatorio)
    $tablasPermitidas = [
        'clientes',
        'proveedores',
        'productos',
        'categorias',
        'sub_categorias',
        'sub_sub_categorias',
        'sucursales',
        'tipoPago',
        'cajas'
    ];

    if (!in_array($tabla, $tablasPermitidas)) {
        $error = [
            "STATUS" => false,
            "message" => "Tabla no permitida"
        ];

        $response->getBody()->write(json_encode($error));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
    }

    try {

        // ⚠️ Aquí NO se puede usar :tabla como parámetro
        $sql = "SELECT * FROM {$tabla} ORDER BY id ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($data));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);

    } catch (PDOException $e) {

        $error = [
            "STATUS" => false,
            "message" => $e->getMessage()
        ];

        $response->getBody()->write(json_encode($error));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
});


$app->get('/tabla/{tabla}/{id}', function (Request $request, Response $response, $args) use ($pdo) {

    $tabla = $args['tabla'];
    $id    = $args['id'];

    // ✅ Lista blanca de tablas permitidas
    $tablasPermitidas = [
        'clientes',
        'proveedores',
        'productos',
        'categorias',
        'sub_categorias',
        'sub_sub_categorias',
        'sucursales'
    ];

    if (!in_array($tabla, $tablasPermitidas)) {
        $error = [
            "STATUS"  => false,
            "message" => "Tabla no permitida"
        ];

        $response->getBody()->write(json_encode($error));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    // Validar ID (evita cosas raras tipo "1 OR 1=1")
    if (!is_numeric($id)) {
        $error = [
            "STATUS"  => false,
            "message" => "ID inválido"
        ];

        $response->getBody()->write(json_encode($error));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    try {

        // ⚠️ El nombre de tabla NO se puede parametrizar en PDO
        $sql = "SELECT * FROM {$tabla} WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id
        ]);

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($data));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);

    } catch (PDOException $e) {

        $error = [
            "STATUS"  => false,
            "message" => $e->getMessage()
        ];

        $response->getBody()->write(json_encode($error));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
});


$app->get('/movimientos', function (Request $request, Response $response) use ($pdo) {

    try {

        $sql = "SELECT
                    p.id,
                    p.codigo,
                    p.nombre,
                    p.categoria
                FROM movimiento_articulos m
                INNER JOIN productos p ON m.codigo_prod = p.id
                WHERE (m.cantidad_ingreso > 0 OR m.cantidad_salida < 0)
                GROUP BY p.id
                ORDER BY p.id DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $prods = [];

        foreach ($productos as $fila) {

            $id = $fila['id'];

            // 🔹 DETALLE
            $sqlDetalle = "SELECT
                                m.id,
                                m.tipo_movimiento,
                                s.nombre AS almacen,
                                m.id_compra,
                                m.id_venta,
                                m.cantidad_acumulada,
                                u.nombre AS unidad,
                                m.cantidad_movimiento,
                                ROUND(m.cantidad_acumulada * m.promedio, 2) AS p_total,
                                m.cantidad_ingreso,
                                m.cantidad_salida,
                                m.precio,
                                m.promedio,
                                ROUND(m.cantidad_acumulada * m.precio, 2) AS costo,
                                m.comentario,
                                DATE_FORMAT(m.fecha_registro,'%d-%m-%Y') AS fecha_registro
                            FROM movimiento_articulos m
                            INNER JOIN sucursales s ON s.id = m.id_sucursal
                            INNER JOIN productos p ON m.codigo_prod = p.id
                            INNER JOIN unidad u ON p.unidad = u.codigo
                            WHERE m.codigo_prod = :id
                              AND NOT (m.cantidad_ingreso = 0 AND m.cantidad_salida = 0)
                              AND m.precio <> 0
                            ORDER BY m.id DESC";

            $stmtDetalle = $pdo->prepare($sqlDetalle);
            $stmtDetalle->execute([':id' => $id]);
            $fila['detalle'] = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);

            // 🔹 PROMEDIO
            $sqlProm = "SELECT promedio, cantidad_acumulada, u.nombre AS unidad
                        FROM movimiento_articulos m
                        INNER JOIN productos p ON m.codigo_prod = p.id
                        INNER JOIN unidad u ON p.unidad = u.codigo
                        WHERE m.codigo_prod = :id
                        ORDER BY m.id DESC
                        LIMIT 1";

            $stmtProm = $pdo->prepare($sqlProm);
            $stmtProm->execute([':id' => $id]);
            $fila['promedio'] = $stmtProm->fetchAll(PDO::FETCH_ASSOC);

            // 🔹 STOCK
            $sqlStock = "SELECT
                            codigo_prod,
                            SUM(cantidad_ingreso) - SUM(cantidad_salida) AS cantidad
                         FROM movimiento_articulos
                         WHERE codigo_prod = :id
                         GROUP BY codigo_prod";

            $stmtStock = $pdo->prepare($sqlStock);
            $stmtStock->execute([':id' => $id]);
            $fila['stock'] = $stmtStock->fetch(PDO::FETCH_ASSOC);

            // 🔹 TOTALES
            $sqlTotales = "SELECT
                                SUM(cantidad_ingreso * precio) AS total_entrada,
                                SUM(cantidad_salida * precio) AS total_salida,
                                SUM((cantidad_salida * precio) - (cantidad_ingreso * precio)) AS costo_venta
                           FROM movimiento_articulos
                           WHERE codigo_prod = :id";

            $stmtTot = $pdo->prepare($sqlTotales);
            $stmtTot->execute([':id' => $id]);
            $totales = $stmtTot->fetch(PDO::FETCH_ASSOC);

            $fila['total_entrada'] = $totales['total_entrada'];
            $fila['total_salida']  = $totales['total_salida'];
            $fila['costo_venta']   = $totales['costo_venta'];

            $prods[] = $fila;
        }

        $response->getBody()->write(json_encode($prods));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);

    } catch (PDOException $e) {

        $error = [
            "STATUS" => false,
            "message" => $e->getMessage()
        ];

        $response->getBody()->write(json_encode($error));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});


$app->get('/buscarproducto/{criterio}', function (Request $request, Response $response, $args) use ($pdo) {

    $criterio = $args['criterio'];

    try {

        $sql = "SELECT *
                FROM productos
                WHERE nombre LIKE :criterio
                   OR id LIKE :criterio
                   OR codigo LIKE :criterio";

        $stmt = $pdo->prepare($sql);

        $like = "%" . $criterio . "%";

        $stmt->execute([
            ':criterio' => $like
        ]);

        $prods = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($prods));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);

    } catch (PDOException $e) {

        $error = [
            "STATUS" => false,
            "message" => $e->getMessage()
        ];

        $response->getBody()->write(json_encode($error));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
});


$app->get('/kardex', function (Request $request, Response $response) use ($pdo) {

    header("Content-Type: application/json; charset=utf-8");

    // Fecha actual
    $ini = date('Y-m-d') . ' 00:00:00';
    $fin = date('Y-m-d') . ' 23:59:59';

    try {

        /*
        |--------------------------------------------------------------------------
        | PRODUCTOS + STOCK + ÚLTIMO PROMEDIO
        |--------------------------------------------------------------------------
        */

        $sqlProductos = "
            SELECT DISTINCT
                p.id,
                p.codigo,
                p.nombre,
                p.categoria,

                (
                    SELECT ma.promedio
                    FROM movimiento_articulos ma
                    WHERE ma.codigo_prod = p.id
                    ORDER BY ma.id DESC
                    LIMIT 1
                ) AS promedio,

                (
                    SELECT ma.cantidad_acumulada
                    FROM movimiento_articulos ma
                    WHERE ma.codigo_prod = p.id
                    ORDER BY ma.id DESC
                    LIMIT 1
                ) AS cantidad_acumulada,

                (
                    SELECT COALESCE(SUM(ma.cantidad_ingreso) - SUM(ma.cantidad_salida),0)
                    FROM movimiento_articulos ma
                    WHERE ma.codigo_prod = p.id
                ) AS stock

            FROM movimiento_articulos m
            INNER JOIN productos p ON p.id = m.codigo_prod

            WHERE m.fecha_registro BETWEEN :ini AND :fin

            ORDER BY p.id DESC
        ";

        $stmt = $pdo->prepare($sqlProductos);
        $stmt->execute([
            ':ini' => $ini,
            ':fin' => $fin
        ]);

        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($productos)) {

            $response->getBody()->write(json_encode([]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        }

        $ids = array_column($productos, 'id');

        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $sqlDetalle = "
            SELECT
                m.id,
                m.codigo_prod,
                m.tipo_movimiento,
                s.nombre AS almacen,
                m.id_compra,
                m.id_venta,
                m.cantidad_acumulada,
                u.nombre AS unidad,
                m.cantidad_movimiento,
                ROUND(m.cantidad_acumulada * m.promedio,2) AS p_total,
                m.cantidad_ingreso,
                m.cantidad_salida,
                m.precio,
                m.promedio,
                ROUND(m.cantidad_acumulada * m.precio,2) AS costo,
                m.comentario,
                m.fecha_registro

            FROM movimiento_articulos m

            INNER JOIN sucursales s
                ON s.id = m.id_sucursal

            INNER JOIN productos p
                ON p.id = m.codigo_prod

            INNER JOIN unidad u
                ON u.codigo = p.unidad

            WHERE m.codigo_prod IN ($placeholders)

            AND m.fecha_registro BETWEEN ? AND ?

            AND NOT (
                m.cantidad_ingreso = 0
                AND m.cantidad_salida = 0
            )

            AND m.precio <> 0

            ORDER BY
                m.codigo_prod,
                m.id DESC";

        $detalleParams = $ids;
        $detalleParams[] = $ini;
        $detalleParams[] = $fin;

        $stmtDetalle = $pdo->prepare($sqlDetalle);
        $stmtDetalle->execute($detalleParams);

        $detalles = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);

        $detallePorProducto = [];

        foreach ($detalles as $detalle) {

            $detallePorProducto[$detalle['codigo_prod']][] = [
                'id'                 => $detalle['id'],
                'tipo_movimiento'    => $detalle['tipo_movimiento'],
                'almacen'            => $detalle['almacen'],
                'id_compra'          => $detalle['id_compra'],
                'id_venta'           => $detalle['id_venta'],
                'cantidad_acumulada' => $detalle['cantidad_acumulada'],
                'unidad'             => $detalle['unidad'],
                'cantidad_movimiento'=> $detalle['cantidad_movimiento'],
                'p_total'            => $detalle['p_total'],
                'cantidad_ingreso'   => $detalle['cantidad_ingreso'],
                'cantidad_salida'    => $detalle['cantidad_salida'],
                'precio'             => $detalle['precio'],
                'promedio'           => $detalle['promedio'],
                'costo'              => $detalle['costo'],
                'comentario'         => $detalle['comentario'],
                'fecha_registro'     => date(
                    'd-m-Y H:i:s',
                    strtotime($detalle['fecha_registro'])
                )
            ];
        }

        foreach ($productos as &$producto) {

            $producto['promedio'] = [
                'promedio' => $producto['promedio'],
                'cantidad_acumulada' => $producto['cantidad_acumulada']
            ];

            $producto['stock'] = [
                'cantidad' => $producto['stock']
            ];

            $producto['detalle'] =
                $detallePorProducto[$producto['id']] ?? [];
        }

        unset($producto);

        $response->getBody()->write(
            json_encode($productos, JSON_UNESCAPED_UNICODE)
        );

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);

    } catch (PDOException $e) {

        $response->getBody()->write(json_encode([
            'STATUS' => false,
            'message' => $e->getMessage()
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
});




$app->post('/kardex', function (Request $request, Response $response) use ($pdo) {

    header("Content-Type: application/json; charset=utf-8");

    $body = $request->getBody()->getContents();
    $j = json_decode($body, true);
    $data = json_decode($j['json'], true);

    $arraymeses = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    $arraynros  = ['01','02','03','04','05','06','07','08','09','10','11','12'];

    $mes1 = substr($data['inicio'], 0, 3);
    $mes2 = substr($data['fin'], 0, 3);

    $dia1 = substr($data['inicio'], 3, 2);
    $dia2 = substr($data['fin'], 3, 2);

    $ano1 = substr($data['inicio'], 5, 4);
    $ano2 = substr($data['fin'], 5, 4);

    $ini = $ano1 . '-' . str_replace($arraymeses, $arraynros, $mes1) . '-' . $dia1 . ' 00:00:01';
    $fin = $ano2 . '-' . str_replace($arraymeses, $arraynros, $mes2) . '-' . $dia2 . ' 23:59:59';

    try {

        /*
        |--------------------------------------------------------------------------
        | PRODUCTOS + STOCK + ÚLTIMO PROMEDIO
        |--------------------------------------------------------------------------
        */

        $sqlProductos = "
    SELECT
        p.id,
        p.codigo,
        p.nombre,
        p.categoria,

        ult.promedio,
        ult.cantidad_acumulada,

        ult.cantidad_acumulada AS stock

    FROM productos p

    INNER JOIN (

        SELECT
            m1.codigo_prod,
            m1.promedio,
            m1.cantidad_acumulada

        FROM movimiento_articulos m1

        INNER JOIN (

            SELECT
                codigo_prod,
                MAX(id) AS id

            FROM movimiento_articulos

            WHERE fecha_registro <= :fin

            GROUP BY codigo_prod

        ) mx ON mx.id = m1.id

    ) ult ON ult.codigo_prod = p.id

    WHERE EXISTS (


    SELECT 1
    FROM movimiento_articulos ma
    WHERE ma.codigo_prod = p.id
      AND fecha_registro BETWEEN :ini AND :fin


    )
";

        $params = [
            ':ini' => $ini,
            ':fin' => $fin
        ];

        if (!empty($data['producto'])) {
            $sqlProductos .= " AND codigo_prod = :producto";
            $params[':producto'] = $data['producto'];
        }

        $sqlProductos .= " ORDER BY p.id DESC";

        //echo $sqlProductos;
        //exit;

        $stmt = $pdo->prepare($sqlProductos);
        $stmt->execute($params);

        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($productos)) {

            $response->getBody()->write(json_encode([]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        }

        /*
        |--------------------------------------------------------------------------
        | IDs DE PRODUCTOS
        |--------------------------------------------------------------------------
        */

        $ids = array_column($productos, 'id');

        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        /*
        |--------------------------------------------------------------------------
        | DETALLES (UNA SOLA CONSULTA)
        |--------------------------------------------------------------------------
        */

        $sqlDetalle = "
            SELECT
                m.id,
                m.codigo_prod,
                m.tipo_movimiento,
                s.nombre AS almacen,
                m.id_compra,
                m.id_venta,
                m.cantidad_acumulada,
                u.nombre AS unidad,
                m.cantidad_movimiento,
                ROUND(m.cantidad_acumulada * m.promedio,2) AS p_total,
                m.cantidad_ingreso,
                m.cantidad_salida,
                m.precio,
                m.promedio,
                ROUND(m.cantidad_acumulada * m.precio,2) AS costo,
                m.comentario,
                m.fecha_registro

            FROM movimiento_articulos m

            INNER JOIN sucursales s
                ON s.id = m.id_sucursal

            INNER JOIN productos p
                ON p.id = m.codigo_prod

            INNER JOIN unidad u
                ON u.codigo = p.unidad

          WHERE m.codigo_prod IN ($placeholders)

            AND m.fecha_registro BETWEEN ? AND ?

            AND NOT (
                m.cantidad_ingreso = 0
                AND m.cantidad_salida = 0
                )

            AND m.precio <> 0
        ";

        $detalleParams = $ids;

        $detalleParams[] = $ini;
        $detalleParams[] = $fin;

        if (!empty($data['sucursal']) && $data['sucursal'] != "0") {
            $sqlDetalle .= " AND m.id_sucursal = ?";
            $detalleParams[] = $data['sucursal'];
        }

        if (!empty($data['movimiento']) && $data['movimiento'] != "0") {
            $sqlDetalle .= " AND m.tipo_movimiento = ?";
            $detalleParams[] = $data['movimiento'];
        }

        if (!empty($data['compra'])) {
            $sqlDetalle .= " AND m.id_compra = ?";
            $detalleParams[] = $data['compra'];
        }

        if (!empty($data['venta'])) {
            $sqlDetalle .= " AND m.id_venta = ?";
            $detalleParams[] = $data['venta'];
        }

        $sqlDetalle .= "
            ORDER BY
                m.codigo_prod,
                m.id DESC
        ";

        $stmtDetalle = $pdo->prepare($sqlDetalle);
        $stmtDetalle->execute($detalleParams);

        $detalles = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);

        /*
        |--------------------------------------------------------------------------
        | AGRUPAR DETALLES POR PRODUCTO
        |--------------------------------------------------------------------------
        */

        $detallePorProducto = [];

        foreach ($detalles as $detalle) {

            $detallePorProducto[$detalle['codigo_prod']][] = [
                'id'                 => $detalle['id'],
                'tipo_movimiento'    => $detalle['tipo_movimiento'],
                'almacen'            => $detalle['almacen'],
                'id_compra'          => $detalle['id_compra'],
                'id_venta'           => $detalle['id_venta'],
                'cantidad_acumulada' => $detalle['cantidad_acumulada'],
                'unidad'             => $detalle['unidad'],
                'cantidad_movimiento'=> $detalle['cantidad_movimiento'],
                'p_total'            => $detalle['p_total'],
                'cantidad_ingreso'   => $detalle['cantidad_ingreso'],
                'cantidad_salida'    => $detalle['cantidad_salida'],
                'precio'             => $detalle['precio'],
                'promedio'           => $detalle['promedio'],
                'costo'              => $detalle['costo'],
                'comentario'         => $detalle['comentario'],
                'fecha_registro'     => date(
                    'd-m-Y',
                    strtotime($detalle['fecha_registro'])
                )
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | ARMAR RESPUESTA
        |--------------------------------------------------------------------------
        */

        foreach ($productos as &$producto) {

            $producto['promedio'] = [
                'promedio' => $producto['promedio'],
                'cantidad_acumulada' => $producto['cantidad_acumulada']
            ];

            $producto['stock'] = [
                'cantidad' => $producto['stock']
            ];

            $producto['detalle'] =
                $detallePorProducto[$producto['id']] ?? [];
        }

        unset($producto);

        $response->getBody()->write(
            json_encode($productos, JSON_UNESCAPED_UNICODE)
        );

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);

    } catch (PDOException $e) {

        $response->getBody()->write(json_encode([
            'STATUS' => false,
            'message' => $e->getMessage()
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
});

$app->get('/vendedores', function (Request $request, Response $response) use ($pdo) {

    $sql = "SELECT * FROM vendedor ORDER BY id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $vendedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response->getBody()->write(json_encode($vendedores));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

$app->get('/permisos', function (Request $request, Response $response) use ($pdo) {

    $sql = "SELECT
                p.id,
                s.nombre AS sucursal,
                u.nombre,
                p.estado,
                p.usuario,
                p.fecha_registro
            FROM permisos p
            INNER JOIN sucursales s ON p.id_sucursal = s.id
            INNER JOIN usuarios u ON p.id_usuario = u.id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $permisos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response->getBody()->write(json_encode($permisos));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});


$app->get('/cajas/{uid}', function (Request $request, Response $response, $args) use ($pdo) {

    $uid = $args['uid'];

    try {

        $sql = "SELECT
                    c.id,
                    c.nombre,
                    c.tipo
                FROM cajas c
                INNER JOIN permisos_caja p ON c.id = p.id_caja
                WHERE p.id_usuario = :uid
                  AND c.estado = 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':uid' => $uid
        ]);

        $cajas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($cajas));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);

    } catch (PDOException $e) {

        $error = [
            "STATUS" => false,
            "message" => $e->getMessage()
        ];

        $response->getBody()->write(json_encode($error));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
});


$app->get('/usuarios', function (Request $request, Response $response) use ($pdo) {

    $sql = "SELECT * FROM usuarios ORDER BY id DESC";

    $stmt = $pdo->query($sql);
    $usuarios = $stmt->fetchAll();

    $response->getBody()->write(json_encode($usuarios));

    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/venta', function (Request $request, Response $response) use ($pdo) {

    $j = json_decode($request->getBody()->getContents(), true);

    $data = json_decode($j['json']);
    $detalle = json_decode($j['detalle']);

    $valor_total = 0;

    $pendiente = ($data->montopendiente < 0) ? 0 : $data->montopendiente;

    try {

        // 🔹 Iniciar transacción
        $pdo->beginTransaction();

        // 🔹 Ejecutar procedimiento venta
        $stmt = $pdo->prepare("CALL p_venta(?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $data->usuario,
            $data->vendedor,
            $data->cliente,
            $data->sucursal,
            $data->entrega,
            $data->tipoDoc,
            $data->neto,
            $data->total,
            $pendiente,
            ($data->total - $data->neto),
            $data->comentario
        ]);
        $stmt->closeCursor();

        // 🔹 Obtener último ID
        $ultimo_id = $pdo->query("SELECT MAX(id) as ultimo_id FROM ventas")->fetch();

        // 🔹 Pagos
        $valor_total = $data->total;

        foreach ($data->pagos as $pago) {

            $valor_total -= $pago->montoPago;

            if ($valor_total < 0) {
                $pago->montoPago += $data->montopendiente;
                $valor_total = 0;
            }

            $stmtP = $pdo->prepare("CALL p_venta_pago(?,?,?,?,?,?,?)");
            $stmtP->execute([
                $ultimo_id->ultimo_id,
                '',
                $pago->numero,
                $pago->cuentaPago,
                $pago->montoPago,
                (count($data->pagos) == 1) ? $pendiente : $valor_total,
                $data->usuario
            ]);
            $stmtP->closeCursor();
        }

        // 🔹 Detalle
        foreach ($detalle as $item) {

            // detalle venta
            $stmtD = $pdo->prepare("CALL p_venta_detalle(?,?,?,?,?,?,?,?,?,?)");
            $stmtD->execute([
                $ultimo_id->ultimo_id,
                $item->id,
                $item->id,
                $item->codigo,
                '',
                $item->cantidad,
                $item->pendiente,
                $item->descuento,
                $item->precio,
                $data->usuario
            ]);
            $stmtD->closeCursor();

            // actualizar inventario
            $stmtInv = $pdo->prepare("
                UPDATE inventario
                SET cantidad = cantidad - ?, fecha_actualizacion = NOW()
                WHERE producto_id = ? AND id_almacen = ?
            ");
            $stmtInv->execute([
                $item->despacho,
                $item->id,
                $data->sucursal
            ]);

            // movimiento
            $stmtMov = $pdo->prepare("CALL p_registrar_movimiento(?,?,?,?,?,?,?)");
            $stmtMov->execute([
                $item->id,
                $ultimo_id->ultimo_id,
                'Salida',
                $item->despacho,
                $item->precio,
                $data->usuario,
                $data->sucursal
            ]);
            $stmtMov->closeCursor();
        }

        // 🔹 Commit
        $pdo->commit();

        $result = [
            "STATUS" => true,
            "numero" => $ultimo_id->ultimo_id,
            "messaje" => "Venta registrada correctamente con el número: " . $ultimo_id->ultimo_id
        ];

    } catch (Exception $e) {

        $pdo->rollBack();

        $result = [
            "STATUS" => false,
            "messaje" => $e->getMessage()
        ];
    }

    $response->getBody()->write(json_encode($result));

    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/venta/{id}', function (Request $request, Response $response, $args) use ($pdo) {

    $id = $args['id'];

    $sql = "SELECT a.nombre, d.*
            FROM venta_detalle d
            INNER JOIN productos a ON a.id = d.id_producto
            WHERE d.id_venta = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    $prods = $stmt->fetchAll();

    $response->getBody()->write(json_encode($prods));

    return $response->withHeader('Content-Type', 'application/json');
});


$app->get('/pagos/{id}', function (Request $request, Response $response, $args) use ($pdo) {

    $id = $args['id'];

    $sql = "SELECT p.*, c.nombre AS caja
            FROM venta_pagos p
            /*INNER JOIN tipoPago tp ON p.tipoPago = tp.id*/
            INNER JOIN cajas c ON p.cuentaPago = c.id
            WHERE p.id_venta = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    $pagos = $stmt->fetchAll();

    $response->getBody()->write(json_encode($pagos));

    return $response->withHeader('Content-Type', 'application/json');
});


$app->post('/observacioncompra', function (Request $request, Response $response) use ($pdo) {

    $j = json_decode($request->getBody()->getContents(), true);

    try {

        $sql = "UPDATE compras SET observacion = :observacion WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':observacion' => $j['observacion'],
            ':id' => $j['id']
        ]);

        $result = [
            "STATUS" => true,
            "messaje" => "Observación registrada"
        ];

    } catch (PDOException $e) {

        $result = [
            "STATUS" => false,
            "messaje" => $e->getMessage()
        ];
    }

    $response->getBody()->write(json_encode($result));

    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/observacion', function (Request $request, Response $response) use ($pdo) {

    $j = json_decode($request->getBody()->getContents(), true);

    try {

        $sql = "UPDATE ventas SET observacion = :observacion WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':observacion' => $j['observacion'],
            ':id' => $j['id']
        ]);

        $result = [
            "STATUS" => true,
            "messaje" => "Observación registrada"
        ];

    } catch (PDOException $e) {

        $result = [
            "STATUS" => false,
            "messaje" => $e->getMessage()
        ];
    }

    $response->getBody()->write(json_encode($result));

    return $response->withHeader('Content-Type', 'application/json');
});


$app->get('/compra/{id}', function (Request $request, Response $response, $args) use ($pdo) {

    $id = $args['id'];

    $sql = "SELECT a.nombre, d.*
            FROM compra_detalle d
            INNER JOIN productos a ON a.id = d.id_producto
            WHERE d.id_compra = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    $prods = $stmt->fetchAll();

    $response->getBody()->write(json_encode($prods));

    return $response->withHeader('Content-Type', 'application/json');
});


$app->get('/pagos-compra/{id}', function (Request $request, Response $response, $args) use ($pdo) {

    $id = $args['id'];

    $sql = "SELECT p.*, tp.nombre, c.nombre AS caja
            FROM compra_pagos p
            INNER JOIN tipoPago tp ON p.tipoPago = tp.id
            INNER JOIN cajas c ON p.cuentaPago = c.id
            WHERE p.id_compra = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    $pagos = $stmt->fetchAll();

    $response->getBody()->write(json_encode($pagos));

    return $response->withHeader('Content-Type', 'application/json');
});


$app->get('/sucursalusuario/{id}', function (Request $request, Response $response, $args) use ($pdo) {

    $id = $args['id'];

    $sql = "SELECT s.*
            FROM permisos p
            INNER JOIN sucursales s ON p.id_sucursal = s.id
            INNER JOIN usuarios u ON p.id_usuario = u.id
            WHERE p.id_usuario = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    $sucursales = $stmt->fetchAll();

    $response->getBody()->write(json_encode($sucursales));

    return $response->withHeader('Content-Type', 'application/json');
});


$app->post('/compra', function (Request $request, Response $response) use ($pdo) {

    $j = json_decode($request->getBody()->getContents(), true);

    $data = json_decode($j['json']);
    $detalle = json_decode($j['detalle']);

    $fecha = substr($data->fecha, 0, 10);

    $pendiente = ($data->montopendiente < 0) ? 0 : $data->montopendiente;

    try {

        // 🔹 Transacción
        $pdo->beginTransaction();

        // 🔹 Registrar compra
        $stmt = $pdo->prepare("CALL p_compra(?,?,?,?,?,?,?,?,?,?,?, ?,?)");
        $stmt->execute([
            $data->usuario,
            $data->seriedoc,
            $data->nrodocumento,
            $fecha,
            $data->proveedor,
            $data->sucursal,
            $data->entrega,
            $data->tipoDoc,
            $data->neto,
            $data->total,
            $pendiente,
            ($data->total - $data->neto),
            $data->comentario
        ]);
        $stmt->closeCursor();

        // 🔹 Obtener ID (mejor si tu SP lo devuelve)
        $ultimo_id = $pdo->query("SELECT MAX(id) AS ultimo_id FROM compras")->fetch();

        // 🔹 Pagos
        $valor_total = $data->total;

        foreach ($data->pagos as $pago) {

            $valor_total -= $pago->montoPago;

            if ($valor_total < 0) {
                $pago->montoPago += $data->montopendiente;
                $valor_total = 0;
            }

            $stmtP = $pdo->prepare("CALL p_compra_pago(?,?,?,?,?,?,?)");
            $stmtP->execute([
                $ultimo_id->ultimo_id,
                $pago->tipoPago,
                $pago->numero,
                $pago->cuentaPago,
                $pago->montoPago,
                (count($data->pagos) == 1) ? $pendiente : $valor_total,
                $data->usuario
            ]);
            $stmtP->closeCursor();
        }

        // 🔹 Detalle + Inventario + Movimiento
        foreach ($detalle as $item) {

            // detalle
            $stmtD = $pdo->prepare("CALL p_compra_detalle(?,?,?,?,?,?,?,?,?)");
            $stmtD->execute([
                $ultimo_id->ultimo_id,
                $item->id,
                $item->id,
                $item->codigo,
                '',
                $item->cantidad,
                $item->pendiente,
                $item->descuento,
                $item->precio
            ]);
            $stmtD->closeCursor();

            // inventario
            $stmtInv = $pdo->prepare("
                UPDATE inventario
                SET cantidad = cantidad + (? - ?),
                    fecha_actualizacion = NOW()
                WHERE producto_id = ? AND id_almacen = ?
            ");
            $stmtInv->execute([
                $item->cantidad,
                $item->pendiente,
                $item->id,
                $data->sucursal
            ]);

            // movimiento
            $stmtMov = $pdo->prepare("CALL p_registrar_movimiento(?,?,?,?,?,?,?)");
            $stmtMov->execute([
                $item->id,
                $ultimo_id->ultimo_id,
                'Ingreso',
                ($item->cantidad - $item->pendiente),
                $item->precio,
                $data->usuario,
                $data->sucursal
            ]);
            $stmtMov->closeCursor();
        }

        // 🔹 Commit
        $pdo->commit();

        $result = [
            "STATUS" => true,
            "messaje" => "Compra registrada correctamente con el número: " . $ultimo_id->ultimo_id
        ];

    } catch (Exception $e) {

        $pdo->rollBack();

        $result = [
            "STATUS" => false,
            "messaje" => $e->getMessage()
        ];
    }

    $response->getBody()->write(json_encode($result));

    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/actualiza-monto', function (Request $request, Response $response) use ($pdo) {

    $data = json_decode($request->getBody()->getContents(), true);
    $data = json_decode($data['json']);

    try {

        // Obtener monto pendiente actual
        $stmt = $pdo->prepare("SELECT monto_pendiente
                             FROM venta_pagos
                             WHERE id_venta = :id_venta
                             ORDER BY id DESC
                             LIMIT 1");
        $stmt->execute(['id_venta' => $data->id_venta]);
        $prods = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$prods) {
            throw new Exception("No se encontró información de pagos");
        }

        $montoPendiente = $prods['monto_pendiente'];

        if ($montoPendiente >= $data->monto) {

            $nuevoMonto = $montoPendiente - $data->monto;

            // Insertar pago
            $stmtInsert = $pdo->prepare("INSERT INTO venta_pagos
                (id_venta, tipoPago, numero_operacion, cuentaPago, monto, monto_pendiente, estado, usuario)
                VALUES (:id_venta, :tipo_pago, :numero, :cuenta_pago, :monto, :monto_pendiente, 1, :usuario)");

            $stmtInsert->execute([
                'id_venta' => $data->id_venta,
                'tipo_pago' => $data->tipo_pago,
                'numero' => $data->numero,
                'cuenta_pago' => $data->cuenta_pago,
                'monto' => $data->monto,
                'monto_pendiente' => $nuevoMonto,
                'usuario' => $data->usuario
            ]);

            // Actualizar venta
            $stmtUpdate = $pdo->prepare("UPDATE ventas
                                       SET monto_pendiente = :monto
                                       WHERE id = :id_venta");

            $stmtUpdate->execute([
                'monto' => $nuevoMonto,
                'id_venta' => $data->id_venta
            ]);

            // Validar si quedó en 0
            if ($nuevoMonto == 0) {
                $stmtZero = $pdo->prepare("UPDATE venta_pagos
                                         SET monto_pendiente = 0
                                         WHERE id_venta = :id_venta");

                $stmtZero->execute([
                    'id_venta' => $data->id_venta
                ]);
            }

            $result = [
                "STATUS" => true,
                "messaje" => "Monto pendiente actualizado correctamente"
            ];

        } else {

            if (($montoPendiente - $data->monto) < 0) {
                $result = [
                    "STATUS" => false,
                    "messaje" => "La cantidad ingresada es mayor al saldo"
                ];
            } else {
                $result = [
                    "STATUS" => false,
                    "messaje" => "Ya no existe monto pendiente"
                ];
            }
        }

    } catch (Exception $e) {

        $result = [
            "STATUS" => false,
            "messaje" => $e->getMessage()
        ];
    }

    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');

});


$app->get('/buscarclientes/{criterio}', function (Request $request, Response $response, $args) use ($pdo) {

    $criterio = $args['criterio'];

    try {

        $stmt = $pdo->prepare("
            SELECT *
            FROM aprendea_erp.clientes
            WHERE nombre LIKE :criterio
               OR num_documento LIKE :criterio
        ");

        $like = "%{$criterio}%";

        $stmt->execute([
            'criterio' => $like
        ]);

        $prods = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $respuesta = json_encode($prods);

    } catch (PDOException $e) {

        $respuesta = json_encode([
            "status" => false,
            "message" => $e->getMessage()
        ]);
    }

    $response->getBody()->write($respuesta);

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});


$app->get('/buscarproveedor/{criterio}', function (Request $request, Response $response, $args) use ($pdo) {

    $criterio = $args['criterio'];

    try {

        $stmt = $pdo->prepare("
            SELECT *
            FROM aprendea_erp.proveedores
            WHERE razon_social LIKE :criterio
               OR num_documento LIKE :criterio
        ");

        $like = "%{$criterio}%";

        $stmt->execute([
            'criterio' => $like
        ]);

        $prods = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $respuesta = json_encode($prods);

    } catch (PDOException $e) {

        $respuesta = json_encode([
            "status" => false,
            "message" => $e->getMessage()
        ]);
    }

    $response->getBody()->write($respuesta);

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});


$app->post('/actualiza-pendiente-venta', function (Request $request, Response $response) use ($pdo) {

    $data = json_decode($request->getBody()->getContents(), true);
    $data = json_decode($data['json']);

    try {

        // Obtener detalle de venta
        $stmt = $pdo->prepare("
            SELECT d.*
            FROM aprendea_erp.venta_detalle d
            WHERE id = :id AND id_venta = :id_venta
        ");

        $stmt->execute([
            'id' => $data->id,
            'id_venta' => $data->id_venta
        ]);

        $prod = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$prod) {
            throw new Exception("No se encontró el detalle de la venta");
        }

        $pendiente = number_format($prod['pendiente'], 2, '.', '');

        // Definir cantidad
        if ($data->cantidad == 0) {
            $cantidad = $pendiente;
        } else {
            $cantidad = $data->cantidad;
        }

        // 🔥 IMPORTANTE: usar transacción (antes no tenías control de errores reales)
        $pdo->beginTransaction();

        // Registrar movimiento (CALL)
        $stmtCall = $pdo->prepare("CALL p_registrar_movimiento(
            :id_producto,
            :id_venta,
            'Salida',
            :cantidad,
            :precio,
            :usuario,
            :sucursal
        )");

        $stmtCall->execute([
            'id_producto' => $data->id_producto,
            'id_venta' => $data->id_venta,
            'cantidad' => $cantidad,
            'precio' => $prod['precio'],
            'usuario' => $data->usuario,
            'sucursal' => $data->sucursal
        ]);

        // Actualizar pendiente
        $stmtUpdate = $pdo->prepare("
            UPDATE venta_detalle
            SET pendiente = :pendiente, usuario = :usuario
            WHERE id_venta = :id_venta AND id_producto = :id_producto
        ");

        $stmtUpdate->execute([
            'pendiente' => $data->cantidad,
            'usuario' => $data->usuario,
            'id_venta' => $data->id_venta,
            'id_producto' => $data->id_producto
        ]);

        // Actualizar inventario
        $stmtInv = $pdo->prepare("
            UPDATE inventario
            SET cantidad = cantidad - :cantidad,
                fecha_actualizacion = NOW()
            WHERE producto_id = :id_producto
              AND id_almacen = :sucursal
        ");

        $stmtInv->execute([
            'cantidad' => $cantidad,
            'id_producto' => $data->id_producto,
            'sucursal' => $data->sucursal
        ]);

        $pdo->commit();

        $result = [
            "STATUS" => true,
            "messaje" => "Pendientes actualizados correctamente"
        ];

    } catch (Exception $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $result = [
            "STATUS" => false,
            "messaje" => $e->getMessage()
        ];
    }

    $response->getBody()->write(json_encode($result));

    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/subcategoria_categoria/{criterio}', function (Request $request, Response $response, $args) use ($pdo) {

    $criterio = $args['criterio'];

    try {

        $stmt = $pdo->prepare("
            SELECT nombre, id
            FROM sub_categorias
            WHERE id_categoria = :criterio
            ORDER BY nombre ASC
        ");

        $stmt->execute([
            'criterio' => $criterio
        ]);

        $prods = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($prods));

        return $response->withHeader('Content-Type', 'application/json');

    } catch (PDOException $e) {

        $response->getBody()->write(json_encode([
            "STATUS" => false,
            "message" => $e->getMessage()
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }
});



$app->get('/familia_subcategoria/{criterio}', function (Request $request, Response $response, $args) use ($pdo) {

    $criterio = $args['criterio'];

    try {

        $stmt = $pdo->prepare("
            SELECT id, nombre
            FROM sub_sub_categorias
            WHERE id_subcategoria = :criterio
            ORDER BY nombre ASC
        ");

        $stmt->execute([
            'criterio' => $criterio
        ]);

        $prods = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($prods));

        return $response->withHeader('Content-Type', 'application/json');

    } catch (PDOException $e) {

        $response->getBody()->write(json_encode([
            "STATUS" => false,
            "message" => $e->getMessage()
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }
});


$app->get('/articulo/{id}', function (Request $request, Response $response, $args) use ($pdo) {

    $id = $args['id'];

    try {

        $stmt = $pdo->prepare("
            SELECT p.*, p.id_sub_sub_categoria AS id_familia
            FROM productos p
            WHERE p.id = :id
        ");

        $stmt->execute([
            'id' => $id
        ]);

        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        // Puedes devolver null o array vacío si no existe
        $response->getBody()->write(json_encode($producto ?: []));

        return $response->withHeader('Content-Type', 'application/json');

    } catch (PDOException $e) {

        $response->getBody()->write(json_encode([
            "STATUS" => false,
            "message" => $e->getMessage()
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }
});


$app->get('/sub_categorias', function (Request $request, Response $response) use ($pdo) {

    try {

        $stmt = $pdo->prepare("
            SELECT id, nombre
            FROM sub_categorias
            ORDER BY id ASC
        ");

        $stmt->execute();

        $prods = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($prods));

        return $response->withHeader('Content-Type', 'application/json');

    } catch (PDOException $e) {

        $response->getBody()->write(json_encode([
            "STATUS" => false,
            "message" => $e->getMessage()
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }
});



$app->get('/familia', function (Request $request, Response $response) use ($pdo) {

    try {

        $stmt = $pdo->prepare("
            SELECT id, nombre
            FROM sub_sub_categorias
            ORDER BY id ASC
        ");

        $stmt->execute();

        $prods = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($prods));

        return $response->withHeader('Content-Type', 'application/json');

    } catch (PDOException $e) {

        $response->getBody()->write(json_encode([
            "STATUS" => false,
            "message" => $e->getMessage()
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }
});

$app->get('/unidad', function (Request $request, Response $response) use ($pdo) {

    try {

        $stmt = $pdo->prepare("
            SELECT id, codigo, nombre
            FROM unidad
            ORDER BY nombre ASC
        ");

        $stmt->execute();

        $prods = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($prods));

        return $response->withHeader('Content-Type', 'application/json');

    } catch (PDOException $e) {

        $response->getBody()->write(json_encode([
            "STATUS" => false,
            "message" => $e->getMessage()
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }
});

$app->put('/producto', function ($request, $response) use ($pdo) {

    header("Content-Type: application/json; charset=utf-8");

    $body = $request->getBody()->getContents();

    $j = json_decode($body, true);

    $data = json_decode($j['json']);

    try {

        // GUARDAR IMAGEN
        if (!empty($data->imagen) && !empty($data->nombre_imagen)) {

// GUARDAR IMAGEN
if (!empty($data->imagen) && !empty($data->nombre_imagen)) {

    $archivo = base64_decode($data->imagen);

    $filePath = $_SERVER['DOCUMENT_ROOT'] . "/erp-api/upload/" . $data->nombre_imagen;

    // Crear imagen desde el contenido binario
    $image = @imagecreatefromstring($archivo);

    if (!$image) {
        throw new Exception("La imagen recibida no es válida");
    }

    // Obtener dimensiones originales
    $width = imagesx($image);
    $height = imagesy($image);

    // Redimensionar si supera el ancho máximo
    $maxWidth = 1200;

    if ($width > $maxWidth) {

        $newWidth = $maxWidth;
        $newHeight = intval(($height * $newWidth) / $width);

        $resized = imagecreatetruecolor($newWidth, $newHeight);

        // Fondo blanco para imágenes PNG transparentes
        $white = imagecolorallocate($resized, 255, 255, 255);
        imagefill($resized, 0, 0, $white);

        imagecopyresampled(
            $resized,
            $image,
            0,
            0,
            0,
            0,
            $newWidth,
            $newHeight,
            $width,
            $height
        );

        imagedestroy($image);
        $image = $resized;
    }

    // Comprimir hasta llegar a 100 KB aprox.
    $maxSize = 100 * 1024; // 100 KB
    $quality = 90;
    $compressed = null;

    do {

        ob_start();
        imagejpeg($image, null, $quality);
        $compressed = ob_get_clean();

        if (strlen($compressed) <= $maxSize) {
            break;
        }

        $quality -= 5;

    } while ($quality >= 10);

    file_put_contents($filePath, $compressed);

    imagedestroy($image);

    // Para verificar en los logs
    error_log(
        "Imagen guardada: " .
        round(filesize($filePath) / 1024, 2) .
        " KB - Calidad: " . $quality
    );

    $sql = "UPDATE productos SET
                id_categoria = :id_categoria,
                id_subcategoria = :id_subcategoria,
                id_sub_sub_categoria = :id_familia,
                nombre = :nombre,
                codigo = :codigo,
                codigobarras = :codigobarras,
                unidad = :unidad,
                precio = :precio,
                precio_compra = :precio_compra,
                imagen = :imagen
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);

    $proceso = $stmt->execute([
        ':id_categoria' => $data->id_categoria,
        ':id_subcategoria' => $data->id_subcategoria,
        ':id_familia' => $data->id_familia,
        ':nombre' => $data->nombre,
        ':codigo' => $data->codigo,
        ':codigobarras' => $data->codigobarras,
        ':unidad' => $data->unidad,
        ':precio' => $data->precio,
        ':precio_compra' => $data->precio_compra,
        ':imagen' => $data->nombre_imagen,
        ':id' => $data->id
    ]);
}


        } else {

            $sql = "UPDATE productos SET
                        id_categoria = :id_categoria,
                        id_subcategoria = :id_subcategoria,
                        id_sub_sub_categoria = :id_familia,
                        nombre = :nombre,
                        codigo = :codigo,
                        codigobarras = :codigobarras,
                        unidad = :unidad,
                        precio = :precio,
                        precio_compra=:precio_compra
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);

            $proceso = $stmt->execute([
                ':id_categoria' => $data->id_categoria,
                ':id_subcategoria' => $data->id_subcategoria,
                ':id_familia' => $data->id_familia,
                ':nombre' => $data->nombre,
                ':codigo' => $data->codigo,
                ':codigobarras' => $data->codigobarras,
                ':unidad' => $data->unidad,
                ':precio' => $data->precio,
                ':precio_compra' => $data->precio_compra,
                ':id' => $data->id
            ]);
        }

        if ($proceso) {

            $result = [
                "STATUS" => true,
                "messaje" => "Producto actualizado correctamente"
            ];

        } else {

            $result = [
                "STATUS" => false,
                "messaje" => "Ocurrió un error en la actualización"
            ];
        }

    } catch (PDOException $e) {

        $result = [
            "STATUS" => false,
            "messaje" => $e->getMessage()
        ];
    }

    $response->getBody()->write(json_encode($result));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

$app->post('/categoria', function ($request, $response) use ($pdo) {

    header("Content-Type: application/json; charset=utf-8");

    $body = $request->getBody()->getContents();

    $j = json_decode($body, true);

    $data = json_decode($j['json']);

    try {

        $nombre = (is_array($data->nombre))
            ? array_shift($data->nombre)
            : $data->nombre;

        $sql = "INSERT INTO categorias (nombre,usuario) VALUES (:nombre,'admin')";

        $stmt = $pdo->prepare($sql);

        $proceso = $stmt->execute([
            ':nombre' => $nombre
        ]);

        if ($proceso) {

            $result = [
                "STATUS" => true,
                "messaje" => "Categoria creada correctamente"
            ];

        } else {

            $result = [
                "STATUS" => false,
                "messaje" => "Ocurrió un error en la creación"
            ];
        }

    } catch (PDOException $e) {

        $result = [
            "STATUS" => false,
            "messaje" => $e->getMessage()
        ];
    }

    $response->getBody()->write(json_encode($result));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});



$app->post('/producto', function (Request $request, Response $response) use ($pdo) {

    $data = json_decode($request->getBody()->getContents(), true);
    $data = json_decode($data['json']);

    try {

        // 🔥 Validación básica
        if (empty($data->nombre) || empty($data->codigo)) {
            throw new Exception("Nombre y código son obligatorios");
        }

        // ---- Guardar imagen ----
        if (!empty($data->imagen) && !empty($data->nombre_imagen)) {

            $archivo = base64_decode($data->imagen);
            $filePath = $_SERVER['DOCUMENT_ROOT'] . "/erp-api/upload/" . $data->nombre_imagen;

            // Crear imagen desde el contenido binario
            $image = imagecreatefromstring($archivo);

            if ($image !== false) {

                $quality = 90;

                do {
                    ob_start();
                    imagejpeg($image, null, $quality);
                    $compressedData = ob_get_clean();

                    $sizeKB = strlen($compressedData) / 1024;
                    $quality -= 5;

                } while ($sizeKB > 100 && $quality > 10);

                file_put_contents($filePath, $compressedData);

                imagedestroy($image);
            }
        }

        // 🔥 Transacción (CLAVE)
        $pdo->beginTransaction();

        // ---- Insert producto ----
        $stmt = $pdo->prepare("
            INSERT INTO productos
            (id_categoria, id_subcategoria, id_sub_sub_categoria, codigo, codigobarras, nombre, unidad, precio,precio_compra, imagen)
            VALUES
            (:categoria, :subcategoria, :familia, :codigo, :codigobarras, :nombre, :unidad, :precio,:precio_compra, :imagen)
        ");

        $stmt->execute([
            'categoria'     => $data->id_categoria,
            'subcategoria'  => $data->id_subcategoria,
            'familia'       => $data->id_familia,
            'codigo'        => $data->codigo,
            'codigobarras'  => $data->codigobarras,
            'nombre'        => $data->nombre,
            'unidad'        => $data->unidad,
            'precio'        => $data->precio,
            'precio_compra' => $data->precio_compra,
            'imagen'        => $data->nombre_imagen ?? null
        ]);

        // ✅ Obtener ID correcto (NO uses MAX(id))
        $ultimo_id = $pdo->lastInsertId();

        // ---- Inventario inicial ----
        $stmtInv = $pdo->prepare("
            INSERT INTO inventario (producto_id, id_almacen, cantidad, comentario)
            VALUES (:producto_id, :almacen, 0, 'carga inicial')
        ");

        $stmtInv->execute(['producto_id' => $ultimo_id, 'almacen' => 1]);
        $stmtInv->execute(['producto_id' => $ultimo_id, 'almacen' => 2]);

        // ---- Movimientos iniciales ----
        $stmtMov = $pdo->prepare("
            INSERT INTO movimiento_articulos
            (codigo_prod, tipo_movimiento, cantidad_ingreso, cantidad_salida, cantidad_acumulada, precio, comentario, id_sucursal, usuario)
            VALUES (:producto_id, 'Ingreso', 0, 0, 0, 0, 'carga inicial', :sucursal, :usuario)
        ");

        $stmtMov->execute(['producto_id' => $ultimo_id, 'sucursal' => 1, 'usuario' => 'admin']);
        $stmtMov->execute(['producto_id' => $ultimo_id, 'sucursal' => 2, 'usuario' => 'admin']);

        $pdo->commit();

        $result = [
            "STATUS" => true,
            "messaje" => "Producto creado correctamente"
        ];

    } catch (Exception $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $result = [
            "STATUS" => false,
            "messaje" => $e->getMessage()
        ];
    }

    $response->getBody()->write(json_encode($result));

    return $response->withHeader('Content-Type', 'application/json');
});


$app->put('/cliente', function ($request, $response) use ($pdo) {

    header("Content-Type: application/json; charset=utf-8");

    $body = $request->getBody()->getContents();

    $j = json_decode($body, true);

    $data = json_decode($j['json']);

    try {

        $sql = "UPDATE clientes
                SET
                    nombre = :nombre,
                    direccion = :direccion,
                    telefono = :telefono,
                    num_documento = :num_documento,
                    email = :email,
                    departamento = :departamento,
                    provincia = :provincia,
                    distrito = :distrito
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':nombre' => $data->nombre,
            ':direccion' => $data->direccion,
            ':telefono' => $data->telefono,
            ':num_documento' => $data->num_documento,
            ':email' => $data->email,
            ':departamento' => $data->departamento,
            ':provincia' => $data->provincia,
            ':distrito' => $data->distrito,
            ':id' => $data->id
        ]);

        $result = [
            "STATUS" => true,
            "messaje" => "Cliente actualizado correctamente"
        ];

    } catch (PDOException $e) {

        $result = [
            "STATUS" => false,
            "messaje" => $e->getMessage()
        ];
    }

    $response->getBody()->write(json_encode($result));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});




$app->post('/cliente', function (Request $request, Response $response) use ($pdo) {

    $body = $request->getBody()->getContents();
    $j = json_decode($body, true);

    // Validación básica
    if (!isset($j['json'])) {
        $response->getBody()->write(json_encode([
            "STATUS" => false,
            "messaje" => "No se recibió el JSON correctamente"
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $data = json_decode($j['json']);

    try {

        $sql = "INSERT INTO clientes
                (num_documento, nombre, telefono, direccion, email, departamento, provincia, distrito, estado)
                VALUES (:num_documento, :nombre, :telefono, :direccion, :email, :departamento, :provincia, :distrito, 1)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':num_documento' => $data->num_documento,
            ':nombre' => $data->nombre,
            ':telefono' => $data->telefono,
            ':direccion' => $data->direccion,
            ':email' => isset($data->email) ?$data->email :'',
            ':departamento' => isset($data->departamento) ?$data->departamento :'',
            ':provincia' => isset($data->provincia) ? $data->provincia :'',
            ':distrito' =>  isset($data->distrito) ?$data->distrito :'',
        ]);

        $result = [
            "STATUS" => true,
            "messaje" => "Cliente registrado correctamente"
        ];

    } catch (PDOException $e) {

        $result = [
            "STATUS" => false,
            "messaje" => $e->getMessage()
        ];
    }

    $response->getBody()->write(json_encode($result));

    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/anular', function (Request $request, Response $response) use ($pdo) {

    $body = $request->getBody()->getContents();
    $j = json_decode($body, true);
    $data = json_decode($j['json'], true);

    try {

        $id = $data['datos']['id'];
        $estado = $data['datos']['estado'];
        $id_sucursal = $data['datos']['id_sucursal'];

        if ($estado != 'Anulado') {

            // 🔥 1. Anular venta
            $stmt = $pdo->prepare("UPDATE ventas SET estado = 2 WHERE id = :id");
            $stmt->execute([':id' => $id]);

            // 🔥 2. Obtener detalle
            $stmt = $pdo->prepare("SELECT * FROM venta_detalle WHERE id_venta = :id");
            $stmt->execute([':id' => $id]);

            $prods = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($prods as $fila) {

                // 🔥 3. Movimiento artículos
                $stmt = $pdo->prepare("
                    INSERT INTO movimiento_articulos
                    (codigo_prod, id_venta, tipo_movimiento, id_almacen, cantidad_ingreso, precio, comentario, id_sucursal, usuario)
                    VALUES (:codigo_prod, :id_venta, 'Ingreso', :id_almacen, :cantidad, :precio, 'Venta anulada', :id_sucursal, 'admin')
                ");

                $stmt->execute([
                    ':codigo_prod' => $fila['id_producto'],
                    ':id_venta' => $fila['id_venta'],
                    ':id_almacen' => $fila['id_inventario'],
                    ':cantidad' => ($fila['cantidad'] - $fila['pendiente']),
                    ':precio' => $fila['precio'],
                    ':id_sucursal' => $id_sucursal
                ]);

                // 🔥 4. Actualizar inventario
                $stmt = $pdo->prepare("
                    UPDATE inventario
                    SET cantidad = cantidad + :cantidad, fecha_actualizacion = NOW()
                    WHERE producto_id = :producto_id AND id_almacen = :almacen
                ");

                $stmt->execute([
                    ':cantidad' => $fila['cantidad'],
                    ':producto_id' => $fila['id_producto'],
                    ':almacen' => $id_sucursal
                ]);
            }

            $result = [
                "STATUS" => true,
                "messaje" => "Venta nro $id fue anulada correctamente"
            ];

        } else {

            $result = [
                "STATUS" => true,
                "messaje" => "La venta $id ya está anulada"
            ];
        }

    } catch (Exception $e) {

        $result = [
            "STATUS" => false,
            "message" => $e->getMessage()
        ];
    }

    $response->getBody()->write(json_encode($result));

    return $response->withHeader('Content-Type', 'application/json');
});


$app->post('/del_proveedor', function (Request $request, Response $response) use ($pdo) {

    $body = $request->getBody()->getContents();
    $j = json_decode($body, true);
    $data = json_decode($j['json']);

    try {
        $stmt = $pdo->prepare("DELETE FROM proveedores WHERE id = :id");
        $stmt->bindParam(':id', $data->proveedor->id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $result = [
                "STATUS" => true,
                "message" => "Proveedor eliminado correctamente"
            ];
        } else {
            $result = [
                "STATUS" => false,
                "message" => "Error al eliminar el proveedor"
            ];
        }

    } catch (PDOException $e) {
        $result = [
            "STATUS" => false,
            "message" => "Error: " . $e->getMessage()
        ];
    }

    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');

});


$app->post('/del_cliente', function (Request $request, Response $response) use ($pdo) {

    $body = $request->getBody()->getContents();
    $j = json_decode($body, true);
    $data = json_decode($j['json']);


    try {
        $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = :id");
        $stmt->bindParam(':id', $data->cliente->id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $result = [
                "STATUS" => true,
                "messaje" => "Cliente eliminado correctamente"
            ];
        } else {
            $result = [
                "STATUS" => false,
                "messaje" => "Error al eliminar el proveedor"
            ];
        }

    } catch (PDOException $e) {
        $result = [
            "STATUS" => false,
            "messaje" => "Error: " . $e->getMessage()
        ];
    }

    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');

});

$app->post('/proveedor', function (Request $request, Response $response) use ($pdo) {

    $body = $request->getBody()->getContents();
    $j = json_decode($body, true);
    $data = json_decode($j['json']);

    // Normalizar datos (por si vienen como array o string)
    $getValue = function($field) {
        return is_array($field) ? array_shift($field) : $field;
    };

    $razon_social = $getValue($data->razon_social);
    $direccion    = $getValue($data->direccion);
    $ruc          = $getValue($data->num_documento);
    $departamento = $getValue($data->departamento);
    $provincia    = $getValue($data->provincia);
    $distrito     = $getValue($data->distrito);

    try {

        $stmt = $pdo->prepare("
            INSERT INTO proveedores
            (razon_social, direccion, num_documento, departamento, provincia, distrito)
            VALUES
            (:razon_social, :direccion, :ruc, :departamento, :provincia, :distrito)
        ");

        $stmt->execute([
            ':razon_social' => $razon_social,
            ':direccion'    => $direccion,
            ':ruc'          => $ruc,
            ':departamento' => $departamento,
            ':provincia'    => $provincia,
            ':distrito'     => $distrito
        ]);

        $result = [
            "STATUS" => true,
            "message" => "Proveedor agregado correctamente"
        ];

    } catch (PDOException $e) {

        $result = [
            "STATUS" => false,
            "message" => $e->getMessage()
        ];
    }

    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');

});


$app->post('/actualizar-precios', function ($request, $response) use ($pdo) {

    $data = json_decode($request->getBody()->getContents());

    $sql = "UPDATE productos
            SET precio = :precio
            WHERE codigo = :codigo";

    $stmt = $pdo->prepare($sql);

    foreach ($data->productos as $producto) {

        $stmt->execute([
            ':precio' => $producto->PRECIO,
            ':codigo' => $producto->CODIGO
        ]);
    }

    $response->getBody()->write(json_encode([
        'success' => true
    ]));

    return $response
        ->withHeader('Content-Type', 'application/json');
});

$app->get('/articulos/{criterio}', function (Request $request, Response $response, $args) use ($pdo) {

    $criterio = $args['criterio'];

    try {

        $stmt = $pdo->prepare("
            SELECT *
            FROM productos
            WHERE nombre LIKE :criterio
               OR codigo LIKE :criterio
        ");

        $like = "%{$criterio}%";

        $stmt->execute([
            'criterio' => $like
        ]);

        $prods = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $respuesta = json_encode($prods);

    } catch (PDOException $e) {

        $respuesta = json_encode([
            "status" => false,
            "message" => $e->getMessage()
        ]);
    }

    $response->getBody()->write($respuesta);

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});



$app->post('/agregar-inventario', function (Request $request, Response $response) use ($pdo) {

    $body = $request->getBody()->getContents();
    $j = json_decode($body, true);
    $data = json_decode($j['json']);



    $cantidad_acumulada = 0;

    $stmt = $pdo->prepare("
        SELECT *
        FROM movimiento_articulos
        WHERE codigo_prod = :producto
        AND id_sucursal = :sucursal
        ORDER BY id DESC
        LIMIT 1
    ");

    $stmt->execute([
        ':producto' => $data->id_producto,
        ':sucursal' => $data->id_sucursal
    ]);

    $inv = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$inv) {
        $inv = [
            'precio' => 0,
            'cantidad_acumulada' => 0,
            'cantidad_ingreso' => 0,
            'total' => 0,
            'promedio' => 0
        ];
    }

    if ($data->operacion === 'Ingreso') {

        if ($inv['precio'] == 0 || $inv['cantidad_acumulada'] == 0) {

            $promedio = ($inv['cantidad_acumulada'] == 0)
                ? $data->precio
                : ($data->cantidad / $data->precio);

            $sql = "
                INSERT INTO movimiento_articulos
                (
                    codigo_prod,
                    tipo_movimiento,
                    id_almacen,
                    comentario,
                    cantidad_movimiento,
                    cantidad_ingreso,
                    cantidad_acumulada,
                    precio,
                    promedio,
                    total,
                    id_sucursal,
                    usuario
                )
                VALUES
                (
                    :codigo_prod,
                    :tipo_movimiento,
                    :id_almacen,
                    :comentario,
                    :cantidad_movimiento,
                    :cantidad_ingreso,
                    :cantidad_acumulada,
                    :precio,
                    :promedio,
                    :total,
                    :id_sucursal,
                    :usuario
                )
            ";

            $params = [
                ':codigo_prod' => $data->id_producto,
                ':tipo_movimiento' => $data->operacion,
                ':id_almacen' => $data->id_sucursal,
                ':comentario' => $data->comentario,
                ':cantidad_movimiento' => $data->cantidad,
                ':cantidad_ingreso' => $data->cantidad,
                ':cantidad_acumulada' => $data->cantidad,
                ':precio' => $data->precio,
                ':promedio' => $promedio,
                ':total' => $data->cantidad * $data->precio,
                ':id_sucursal' => $data->id_sucursal,
                ':usuario' => $data->usuario
            ];

        } else {

            $cantidad_ingreso = $data->cantidad + floatval($inv['cantidad_ingreso']);
            $total = round(
                ($data->cantidad * $data->precio) + floatval($inv['total']),
                2
            );

            if (floatval($inv['cantidad_acumulada']) <= 0) {

                $promedio =
                    (
                        floatval($inv['total']) +
                        ($data->cantidad * $data->precio)
                    )
                    /
                    (
                        floatval($inv['cantidad_acumulada']) +
                        $data->cantidad
                    );

                $cantidad_acumulada =
                    floatval($inv['cantidad_acumulada']) +
                    $data->cantidad;

            } else {

                $promedio =
                    (
                        floatval($inv['total']) +
                        ($data->cantidad * $data->precio)
                    )
                    /
                    (
                        floatval($inv['cantidad_acumulada']) +
                        $data->cantidad
                    );

                $cantidad_ingreso = $data->cantidad;

                $cantidad_acumulada =
                    floatval($inv['cantidad_acumulada']) +
                    $data->cantidad;
            }

            $sql = "
                INSERT INTO movimiento_articulos
                (
                    codigo_prod,
                    tipo_movimiento,
                    id_almacen,
                    comentario,
                    cantidad_movimiento,
                    cantidad_ingreso,
                    cantidad_acumulada,
                    precio,
                    promedio,
                    total,
                    id_sucursal,
                    usuario
                )
                VALUES
                (
                    :codigo_prod,
                    :tipo_movimiento,
                    :id_almacen,
                    :comentario,
                    :cantidad_movimiento,
                    :cantidad_ingreso,
                    :cantidad_acumulada,
                    :precio,
                    :promedio,
                    :total,
                    :id_sucursal,
                    :usuario
                )
            ";

            $params = [
                ':codigo_prod' => $data->id_producto,
                ':tipo_movimiento' => $data->operacion,
                ':id_almacen' => $data->id_sucursal,
                ':comentario' => $data->comentario,
                ':cantidad_movimiento' => $data->cantidad,
                ':cantidad_ingreso' => $cantidad_ingreso,
                ':cantidad_acumulada' => $cantidad_acumulada,
                ':precio' => $data->precio,
                ':promedio' => $promedio,
                ':total' => $total,
                ':id_sucursal' => $data->id_sucursal,
                ':usuario' => $data->usuario
            ];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $stmt = $pdo->prepare("
            UPDATE inventario
            SET cantidad = cantidad + :cantidad,
                fecha_actualizacion = NOW()
            WHERE producto_id = :producto
            AND id_almacen = :almacen
        ");

        $stmt->execute([
            ':cantidad' => $data->cantidad,
            ':producto' => $data->id_producto,
            ':almacen' => $data->id_sucursal
        ]);
    }

    $result = [
        'STATUS' => true,
        'messaje' => 'Inventario registrado correctamente'
    ];

    $response->getBody()->write(json_encode($result));

    return $response
        ->withHeader('Content-Type', 'application/json');
});


$app->run();