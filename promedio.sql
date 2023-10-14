select p.id,p.nombre,p.categoria from movimiento_articulos m, productos p where m.codigo_prod=p.id group by 1 order by id asc;
SELECT  p.id,p.nombre, m.tipo_movimiento,cantidad_ingreso,cantidad_salida,m.precio FROM aprendea_erp.movimiento_articulos m, productos p where m.codigo_prod=p.id and p.id=1 order by id desc;

select codigo_prod,  ROUND((sum(cantidad_ingreso*precio) + sum(cantidad_salida*precio))/sum(cantidad_ingreso+cantidad_salida),2) promedio from movimiento_articulos where codigo_prod=1 group by 1;

select cantidad from inventario where producto_id=1;