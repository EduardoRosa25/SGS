CREATE DATABASE IF NOT EXISTS sgs_seguros;
USE sgs_seguros;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(200) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL, -- Armazenar a senha via hash, por isso o campo longo
    perfil ENUM('admin', 'corretor', 'cliente') NOT NULL, --- Adiciona o campo perfil para diferenciar os tipos de usuários cliente = segurado
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
)

CREATE TABLE parceiros (                      -- cadastro corretoras e seguradoras 
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(200) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    tipo ENUM('seguradora', 'corretora') NOT NULL,
    cnpj VARCHAR(20) NOT NULL UNIQUE,
    telefone VARCHAR(20),
)


