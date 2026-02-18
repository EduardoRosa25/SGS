CREATE DATABASE IF NOT EXISTS sgs_seguros;
USE sgs_seguros;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(200) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL, -- Armazenar a senha via hash, por isso o campo longo
    perfil ENUM('admin', 'corretor', 'cliente') NOT NULL, --- Adiciona o campo perfil para diferenciar os tipos de usuários cliente = segurado
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE parceiros (                      -- cadastro corretoras e seguradoras 
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(200) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    tipo ENUM('seguradora', 'corretora') NOT NULL,
    cnpj VARCHAR(20) NOT NULL UNIQUE,
    telefone VARCHAR(20)
);

CREATE TABLE apolices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    seguradora_id INT NOT NULL,
    corretora_id INT NOT NULL,
    numero_apolice VARCHAR(75) NOT NULL UNIQUE,
    tipo_seguro ENUM('Vida', 'Auto', 'RCG', 'Cyber', 'Riscos Operacionais', 'Riscos de Engenharia','RCTR-C','RCF-DC','D&O', 'E&O','STP-TN') NOT NULL,
    valor_total DECIMAL(10, 2) NOT NULL, -- premio liquido + iof
    premio_liquido DECIMAL(10, 2) NOT NULL, -- Valor sem iof
    iof DECIMAL(10, 2) NOT NULL, -- 7,38% do valor total
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    arquivo_apolice VARCHAR(255), -- Caminho para o arquivo da apólice
    status_apolice ENUM('vigente', 'cancelada', 'vencida') NOT NULL,
    -- chaves estrangeiras 
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (seguradora_id) REFERENCES parceiros(id),
    FOREIGN KEY (corretora_id) REFERENCES parceiros(id)
);

CREATE TABLE boletos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    apolice_id INT NOT NULL,
    numero_boleto VARCHAR(150) NOT NULL UNIQUE,
    premio_liquido_parcela DECIMAL(10, 2) NOT NULL,
    iof_parcela DECIMAL(10, 2) NOT NULL, -- Valor do IOF para a parcela
    valor_total_parcela DECIMAL(10, 2) NOT NULL, -- Valor total do boleto (premio liquido + iof)
    data_vencimento DATE NOT NULL,
    status_boleto ENUM('pendente', 'enviado para pagamento','pago', 'vencido') NOT NULL,
    numero_pedido VARCHAR(100), -- Número do pedido de pagamento, se aplicável
    material TEXT NOT NULL, -- Descrição do material ou serviço relacionado ao boleto   
    conta_contabil VARCHAR(75) NOT NULL, -- Conta contábil associada ao boleto
    centro_custo VARCHAR(75) NOT NULL, -- Centro de custo associado ao boleto
    arquivo_boleto VARCHAR(255), -- Caminho para o arquivo do boleto
    FOREIGN KEY (apolice_id) REFERENCES apolices(id)
);


CREATE TABLE sinistros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    apolice_id INT NOT NULL,
    numero_sinistro VARCHAR(75) NOT NULL UNIQUE,
    empresa VARCHAR(200) NOT NULL, -- Nome da empresa segurada
    status_sinistro ENUM('aguardando atendimento', 'em análise', 'indenizado', 'encerrado sem cobertura') NOT NULL,
    tipo_evento TEXT NOT NULL, -- Descrição do tipo de evento (ex: acidente, roubo, etc.)
    data_ocorrencia DATE NOT NULL,
    descricao TEXT NOT NULL,
    numero_boletim VARCHAR(100), -- Número do boletim de ocorrência, se aplicável
    prejuizo_reclamado DECIMAL(10, 2) NOT NULL,
    prejuizo_apurado DECIMAL(10, 2), -- Valor apurado pela seguradora após análise
    franquia DECIMAL(10, 2), -- Valor da franquia, se aplicável
    valor_indenizado DECIMAL(10, 2), -- Valor efetivamente indenizado pela seguradora   
    cobertura_aplicada TEXT, -- Descrição da cobertura aplicada ao sinistro
    observacoes TEXT, -- Campo para observações adicionais sobre o sinistro
    arquivos_sinistro VARCHAR(255), -- Caminho para o arquivo do comprovante
    FOREIGN KEY (apolice_id) REFERENCES apolices(id) 
);

 -- ADMIN INICIAL p / teste
INSERT INTO usuarios (nome, email, senha, perfil) VALUES 
('Admin', 'admin@sgs.com', '123456', 'admin');
