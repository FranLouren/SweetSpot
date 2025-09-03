CREATE DATABASE IF NOT EXISTS padel_reservas;
USE padel_reservas;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(25) NOT NULL,
    email VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(50) NOT NULL
);

-- HEMOS AÑADIDO LA COLUMNA ROL
ALTER TABLE usuarios 
ADD COLUMN rol ENUM('usuario', 'admin') DEFAULT 'usuario' AFTER password;


-- Tabla de pistas
CREATE TABLE pistas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

-- Tabla de reservas
CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    pista_id INT NOT NULL,
    fecha DATE NOT NULL,
    hora_inicio TIME NOT NULL AFTER fecha,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (pista_id) REFERENCES pistas(id)
);
