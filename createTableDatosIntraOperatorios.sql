CREATE TABLE datos_intra_operatorios(
    id VARCHAR(255) PRIMARY KEY,
    
    -- Signos Vitales - Presión Arterial
    pasistolica_ini INTEGER,
    padiastolica_ini INTEGER,
    pasistolica_postint INTEGER,
    padiastolica_postint INTEGER,
    pasistolica_fin INTEGER,
    padiastolica_fin INTEGER,
    
    -- Signos Vitales - Frecuencia Cardíaca
    fcard_ini INTEGER,
    fcard_postint INTEGER,
    fcard_fin INTEGER,
    
    -- Signos Vitales - Saturación O2
    sato_ini INTEGER,
    sato_postint INTEGER,
    sato_fin INTEGER,
    
    -- Otros Signos Vitales
    etco2 NUMERIC(5,2),
    bis INTEGER,
    
    -- Tiempo Quirúrgico
    despertar INTEGER,
    tiempoqx INTEGER,
    
    -- Fármacos de Inducción
    induccionpropofol NUMERIC(5,2),
    inducciondexmedetomidina NUMERIC(5,2),
    induccionlidocaina NUMERIC(5,2),
    induccionketamina NUMERIC(5,2),
    induccionrnm NUMERIC(5,2),
    
    -- Fármacos de Mantenimiento
    mantenimientosevorane NUMERIC(5,2),
    mantenimientodexmedetomidina NUMERIC(5,2),
    mantenimientolidocaina NUMERIC(5,2),
    mantenimientoketamina NUMERIC(5,2),
    mantenimientosulfatomg NUMERIC(5,2),
    
    -- Coadyuvantes - Ondasetron
    ondasetron BOOLEAN DEFAULT FALSE,
    valorondasetron NUMERIC(5,2),
    
    -- Coadyuvantes - Metamizol
    metamizol BOOLEAN DEFAULT FALSE,
    valormetamizol NUMERIC(5,2),
    
    -- Coadyuvantes - Dexametasona
    dexametasona BOOLEAN DEFAULT FALSE,
    valordexametasona NUMERIC(5,2),
    
    -- Coadyuvantes - Ketorol
    ketorol BOOLEAN DEFAULT FALSE,
    valorketorol NUMERIC(5,2),
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign Key (ajusta según tu tabla de pacientes)
    CONSTRAINT fk_paciente FOREIGN KEY (id) REFERENCES datos_personales(id) ON DELETE CASCADE
);

-- Índice para mejorar consultas por fecha
CREATE INDEX idx_datos_intra_created_at ON datos_intra_operatorios(created_at);

-- Trigger para actualizar updated_at automáticamente (PostgreSQL)
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_datos_intra_updated_at
BEFORE UPDATE ON datos_intra_operatorios
FOR EACH ROW
EXECUTE FUNCTION update_updated_at_column();