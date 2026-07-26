CREATE TABLE `movimiento_caja` (
  `id` int NOT NULL AUTO_INCREMENT,
  `numero_operacion` varchar(145) DEFAULT NULL,
  `tipo` varchar(10) DEFAULT NULL,
  `cuenta` int DEFAULT NULL,
  `monto` decimal(10,4) NOT NULL,
  `concepto` text,
  `estado` int DEFAULT NULL,
  `usuario` varchar(45) DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
