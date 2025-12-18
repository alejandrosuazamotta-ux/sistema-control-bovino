CREATE TABLE Personal (
    id_personal INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    rol ENUM('Pasante', 'Supervisor', 'Otro') NOT NULL,
    fecha_contratacion DATE
);

CREATE TABLE Potreros (
    id_potrero INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    ubicacion VARCHAR(100),
    capacidad INT NOT NULL
);

CREATE TABLE Vacas (
    id_vaca INT PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(20) UNIQUE NOT NULL, -- Código o chip de identificación
    fecha_nacimiento DATE,
    raza VARCHAR(50),
    estado_salud ENUM('Sana', 'En tratamiento', 'En observación') DEFAULT 'Sana',
    estado_reproductivo ENUM('Celo', 'Preñada', 'Lactancia', 'Descanso') DEFAULT 'Descanso',
    id_potrero INT,
    FOREIGN KEY (id_potrero) REFERENCES Potreros(id_potrero)
);

CREATE TABLE Crias (
    id_cria INT PRIMARY KEY AUTO_INCREMENT,
    id_vaca_madre INT NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    peso DECIMAL(5,2),
    estado_destete ENUM('No destetada', 'Destetada') DEFAULT 'No destetada',
    FOREIGN KEY (id_vaca_madre) REFERENCES Vacas(id_vaca)
);

CREATE TABLE Produccion_Lechera (
    id_produccion INT PRIMARY KEY AUTO_INCREMENT,
    id_vaca INT NOT NULL,
    fecha DATE NOT NULL,
    cantidad_leche DECIMAL(5,2) NOT NULL, -- Litros de leche
    id_personal INT,
    FOREIGN KEY (id_vaca) REFERENCES Vacas(id_vaca),
    FOREIGN KEY (id_personal) REFERENCES Personal(id_personal)
);

CREATE TABLE Salud (
    id_salud INT PRIMARY KEY AUTO_INCREMENT,
    id_vaca INT NOT NULL,
    tipo_registro ENUM('Vacunación', 'Tratamiento', 'Prueba mastitis', 'Otro') NOT NULL,
    fecha DATE NOT NULL,
    descripcion TEXT,
    resultado_prueba VARCHAR(50), -- Para pruebas como mastitis
    id_personal INT,
    FOREIGN KEY (id_vaca) REFERENCES Vacas(id_vaca),
    FOREIGN KEY (id_personal) REFERENCES Personal(id_personal)
);

CREATE TABLE Asignacion_Potreros (
    id_asignacion INT PRIMARY KEY AUTO_INCREMENT,
    id_potrero INT NOT NULL,
    id_vaca INT NOT NULL,
    fecha_asignacion DATE NOT NULL,
    FOREIGN KEY (id_potrero) REFERENCES Potreros(id_potrero),
    FOREIGN KEY (id_vaca) REFERENCES Vacas(id_vaca)
);