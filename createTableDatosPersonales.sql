CREATE TABLE datos_personales (
    id VARCHAR(255) NOT NULL PRIMARY KEY,
    fechanacimiento DATE NOT NULL,
    fechacirugia DATE NOT NULL,
    genero VARCHAR(50) NOT NULL,
    asa VARCHAR(10) NOT NULL, 
    tipocirugia VARCHAR(100) NOT NULL,
    otracirugia VARCHAR(255) NULL, -- Puede ser opcional/nulo según tu lógica
    edad INT NOT NULL,
    imc DECIMAL(5, 2) NOT NULL, 
    peso DECIMAL(5, 2) NOT NULL, 
    talla DECIMAL(4, 2) NOT NULL, 
    created_at DATETIME NOT NULL 
);