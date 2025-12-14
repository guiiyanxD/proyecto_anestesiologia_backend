ALTER TABLE datos_personales 
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT NULL;

-- Para datos_intra_operatorios (para futuras actualizaciones)
ALTER TABLE datos_intra_operatorios 
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT NULL;

-- Para datos_post_operatorios (para futuras actualizaciones)
ALTER TABLE datos_post_operatorios 
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT NULL;

-- Verificar que se agregaron correctamente
SELECT column_name, data_type 
FROM information_schema.columns 
WHERE table_name = 'datos_personales' 
AND column_name = 'updated_at';