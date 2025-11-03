-- Agregar FK para datos intraoperatorios
ALTER TABLE datos_intra_operatorios 
ADD CONSTRAINT fk_intra_paciente 
FOREIGN KEY (id) REFERENCES datos_personales(id) ON DELETE CASCADE;

-- Agregar FK para datos postoperatorios  
ALTER TABLE datos_post_operatorios 
ADD CONSTRAINT fk_post_paciente 
FOREIGN KEY (id) REFERENCES datos_personales(id) ON DELETE CASCADE;