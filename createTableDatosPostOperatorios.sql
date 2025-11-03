create table datos_post_operatorios(
id varchar(255) primary key, 
recuperacionpostanestesia integer not null,
ramsay integer not null,
evaingreso integer not null, 
eva1hr integer not null, 
nauseas boolean not null, 
vomitos boolean not null,
consumoanalgesico boolean not null, 
tipoanalgesico varchar(100),
depresionrespiratoria boolean not null, 
spo2bajo varchar(100),
created_at TIMESTAMP
);