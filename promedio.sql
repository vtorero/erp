SELECT * FROM aprendea_erp.movimiento_articulos order by id desc;

select codigo_prod, (sum(cantidad_ingreso*precio) + sum(cantidad_salida*precio))/sum(cantidad_ingreso+cantidad_salida) promedio from movimiento_articulos group by 1;