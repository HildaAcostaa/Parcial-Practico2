-- script pa la base de datos

CREATE DATABASE IF NOT EXISTS `itech_contrataciones`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `itech_contrataciones`;

--  TABLAS CATÁLOGO

CREATE TABLE IF NOT EXISTS `cat_tipos_planilla` (
  `id`     INT          NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `cat_tipos_planilla` (`nombre`) VALUES
  ('Permanente'),
  ('Eventual'),
  ('Interino');

-- ---

CREATE TABLE IF NOT EXISTS `cat_ocupaciones` (
  `id`     INT          NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `cat_ocupaciones` (`nombre`) VALUES
  ('Secretaria'),
  ('Albañil'),
  ('Ingeniero'),
  ('Médico'),
  ('Abogado'),
  ('Contador'),
  ('Técnico'),
  ('Administrador'),
  ('Docente'),
  ('Enfermero/a'),
  ('Arquitecto'),
  ('Analista de Sistemas');

--  TABLA PRINCIPAL: colaboradores

CREATE TABLE IF NOT EXISTS `colaboradores` (
  `id`           INT          NOT NULL AUTO_INCREMENT COMMENT 'Código de Empleado',
  `identidad`    VARCHAR(20)  NOT NULL UNIQUE,
  `nombre`       VARCHAR(100) NOT NULL,
  `apellido`     VARCHAR(100) NOT NULL,
  `edad`         INT          NOT NULL,
  `tipo_sangre`  VARCHAR(5)   NOT NULL,
  `sexo`         ENUM('Masculino','Femenino','Otro') NOT NULL,
  `nacionalidad` VARCHAR(100) NOT NULL,
  `ruta`         ENUM('Panamá Este','Panamá Oeste','Panamá Norte') NOT NULL,
  `correo`       VARCHAR(150) NOT NULL UNIQUE,
  `celular`      VARCHAR(20)  NOT NULL,
  `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--  TABLA: perfiles_laborales

CREATE TABLE IF NOT EXISTS `perfiles_laborales` (
  `id`               INT           NOT NULL AUTO_INCREMENT,
  `id_empleado`      INT           NOT NULL COMMENT 'FK → colaboradores.id',
  `id_ocupacion`     INT           NOT NULL COMMENT 'FK → cat_ocupaciones.id',
  `id_tipo_planilla` INT           NOT NULL COMMENT 'FK → cat_tipos_planilla.id',
  `departamento`     VARCHAR(150)  NOT NULL COMMENT 'Ubicación o área dentro de la empresa',
  `salario`          DECIMAL(10,2) NOT NULL,
  `fecha_inicio`     DATE          NOT NULL,
  `fecha_fin`        DATE          NULL DEFAULT NULL,
  `es_activo`        BOOLEAN       NOT NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo (por promoción)',
  `empleado_activo`  TINYINT(1)    NOT NULL DEFAULT 1 COMMENT '1=Activo, 0=Dado de Baja (fecha_fin presente)',
  `motivo_baja`      TEXT          NULL DEFAULT NULL,
  `firma_digital`    TEXT          NOT NULL COMMENT 'Firma OpenSSL base64 para integridad',
  `created_at`       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_perfil_empleado`
    FOREIGN KEY (`id_empleado`)      REFERENCES `colaboradores`(`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_perfil_ocupacion`
    FOREIGN KEY (`id_ocupacion`)     REFERENCES `cat_ocupaciones`(`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_perfil_planilla`
    FOREIGN KEY (`id_tipo_planilla`) REFERENCES `cat_tipos_planilla`(`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--  ÍNDICES ADICIONALES

CREATE INDEX `idx_perfil_empleado`  ON `perfiles_laborales` (`id_empleado`);
CREATE INDEX `idx_perfil_activo`    ON `perfiles_laborales` (`id_empleado`, `es_activo`);
