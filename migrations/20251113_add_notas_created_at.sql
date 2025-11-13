-- Migración: agregar columnas `notas` y `fecha_creacion` a la tabla `reserva`
-- Haz un respaldo antes de ejecutar: `mysqldump -u root sugar reserva > reserva_backup.sql`

-- Añadir columna para notas/motivos y una columna de marca temporal de creación
ALTER TABLE reserva ADD COLUMN notas TEXT NULL;
ALTER TABLE reserva ADD COLUMN fecha_creacion TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;
