CREATE DATABASE IF NOT EXISTS stockApi;
USE stockApi;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    token VARCHAR(255) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS categorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL UNIQUE
);

INSERT INTO categorias (nome) VALUES
('Periféricos'),
('Monitores'),
('Notebooks'),
('Componentes'),
('Acessórios');

CREATE TABLE IF NOT EXISTS produtos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL UNIQUE,
    descricao TEXT NOT NULL,
    categoria INT NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    quantidade INT NOT NULL,
    estoque_minimo INT NOT NULL,

    FOREIGN KEY (categoria) REFERENCES categorias(id) ON DELETE CASCADE
);

INSERT INTO produtos 
(nome, descricao, categoria, preco, quantidade, estoque_minimo)
VALUES
('Teclado Mecânico', 'Teclado mecânico ABNT2 com iluminação RGB', 1, 249.90, 20, 5),
('Mouse Gamer', 'Mouse óptico com 6 botões programáveis', 1, 129.90, 35, 10),
('Monitor 24 Polegadas', 'Monitor Full HD de 24 polegadas', 2, 899.90, 8, 3),
('Notebook Pro', 'Notebook para desenvolvimento de software', 3, 4599.90, 5, 2),
('SSD 1TB', 'SSD NVMe de 1TB', 4, 499.90, 15, 5),
('Headset USB', 'Headset com microfone e conexão USB', 5, 12.90, 2, 5);

CREATE TABLE IF NOT EXISTS movimentacoes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    produto_id INT NOT NULL,
    usuario_id INT NOT NULL,
    tipo ENUM('entrada', 'saida') NOT NULL,
    quantidade INT NOT NULL,
    data_movimentacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (produto_id) REFERENCES produtos(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);