SELECT v.id,vp.fecha_registro,'Ingreso',u.nombre usuario, s.nombre sucursal,
 tp.nombre tipopago,valor_total,vp.monto, vp.monto_pendiente
 FROM aprendea_erp.venta_pagos vp,ventas v,usuarios u,sucursales s,tipoPago tp where vp.tipoPago=tp.id and v.id_sucursal=s.id and v.id=vp.id_venta and vp.usuario=u.id
and vp.fecha_registro  between '2024-10-01 00:00:00' and '2024-10-25 23:59:00' union all
SELECT v.id,vp.fecha_registro,'Salida',u.nombre usuario ,s.nombre sucursal, 
tp.nombre tipopago,valor_total,vp.monto, vp.monto_pendiente 
FROM aprendea_erp.compra_pagos vp,compras v,usuarios u,sucursales s,tipoPago tp 
where vp.tipoPago=tp.id and v.id_sucursal=s.id and v.id=vp.id_compra and vp.usuario=u.id
and vp.fecha_registro  between '2024-10-01 00:00:00' and '2024-10-25 23:59:00'  order by fecha_registro asc;
