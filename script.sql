ALTER TABLE `planobra` ADD COLUMN `costo_unitario_mo_diseno` DOUBLE NULL AFTER `situacion_general_reforzamiento`, ADD COLUMN `solicitud_oc_diseno` VARCHAR(45) NULL AFTER `costo_unitario_mo_diseno`, ADD COLUMN `estado_sol_oc_diseno` VARCHAR(45) NULL AFTER `solicitud_oc_diseno`, ADD COLUMN `costo_unitario_mo_crea_oc_diseno` DOUBLE NULL AFTER `estado_sol_oc_diseno`; 

CREATE TABLE `itemplan_x_solicitud_oc_diseno` ( `itemplan` VARCHAR(45) NOT NULL, `codigo_solicitud_oc` VARCHAR(45) NOT NULL, `costo_unitario_mo` DOUBLE NOT NULL, `posicion` VARCHAR(45) DEFAULT NULL, PRIMARY KEY (`itemplan`,`codigo_solicitud_oc`), KEY `tb_itemplan_x_soc_codigo_solicitud__tb_soc` (`codigo_solicitud_oc`) ) ENGINE=INNODB DEFAULT CHARSET=utf8; 

CREATE TABLE `solicitud_orden_compra_diseno` ( `codigo_solicitud` VARCHAR(45) NOT NULL, `idEmpresaColab` INT(11) NOT NULL, `idSubProyecto` INT(11) NOT NULL, `estado` INT(11) NOT NULL COMMENT '1 = pendiente de OC, \n2 = ATENDIDO CON OC, \n3 = CANCELADO\n, 4 = EN ESPERA DE EDICION (SOLO A LOS TIPO CERTIFICACION)\n, 5= EN ESPERA DE ACTA\n\nfk tabla estado_solicitud_orden_compra', `plan` VARCHAR(45) DEFAULT NULL, `cesta` VARCHAR(45) DEFAULT NULL, `orden_compra` VARCHAR(120) DEFAULT NULL, `tipo_solicitud` CHAR(1) CHARACTER SET latin1 NOT NULL COMMENT '1 = CREACION, \r\n\n2 = EDICION\n,\r\n3 = CERTIFICACION,\r\n4= ANULACION', `estatus_solicitud` VARCHAR(45) NOT NULL COMMENT 'nuevo o regularizacion', `usuario_creacion` INT(11) NOT NULL, `fecha_creacion` DATETIME NOT NULL, `path_oc` TEXT CHARACTER SET latin1 DEFAULT NULL, `usuario_valida` INT(11) DEFAULT NULL, `fecha_valida` DATETIME DEFAULT NULL, `usuario_cancela` INT(11) DEFAULT NULL, `fecha_cancelacion` DATETIME DEFAULT NULL, `motivo_cancela` TEXT DEFAULT NULL, `codigo_certificacion` VARCHAR(120) DEFAULT NULL, `costo_sap` DECIMAL(10,2) DEFAULT NULL, `usuario_to_pndte` INT(11) DEFAULT NULL, `fecha_to_pndte` DATETIME DEFAULT NULL, `flg_robot_oc` CHAR(1) DEFAULT NULL, `codigoInversion` VARCHAR(120) DEFAULT NULL, PRIMARY KEY (`codigo_solicitud`), KEY `tb_soc_idEmpresaColab__tb_empresacolab` (`idEmpresaColab`), KEY `tb_soc_idSubProyecto__tb_subproyecto` (`idSubProyecto`), KEY `tb_soc_estado__tb_estado_soc` (`estado`) ) ENGINE=INNODB DEFAULT CHARSET=utf8; 

ALTER TABLE `planobra` ADD COLUMN `orden_compra_diseno` VARCHAR(45) NULL AFTER `costo_unitario_mo_crea_oc_diseno`; 
ALTER TABLE `planobra` ADD COLUMN `costo_sap_diseno` DOUBLE NULL AFTER `orden_compra_diseno`; 


DROP TABLE IF EXISTS `matriz_jumpeo_file`;

CREATE TABLE `matriz_jumpeo_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `matrizjumpeo_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT NULL,
  `tipo` int(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;





ALTER TABLE `planobra` 
ADD COLUMN `solicitud_oc_dev_diseno` VARCHAR(45) NULL AFTER `costo_sap_diseno`, 
ADD COLUMN `costo_devolucion_diseno` DOUBLE NULL AFTER `solicitud_oc_dev_diseno`, 
ADD COLUMN `estado_oc_dev_diseno` VARCHAR(45) NULL AFTER `costo_devolucion_diseno`, 
ADD COLUMN `solicitud_oc_certi_diseno` VARCHAR(45) NULL AFTER `estado_oc_dev_diseno`, 
ADD COLUMN `costo_unitario_mo_certi_diseno` DOUBLE NULL AFTER `solicitud_oc_certi_diseno`, 
ADD COLUMN `estado_oc_certi_diseno` VARCHAR(45) NULL AFTER `costo_unitario_mo_certi_diseno`; 