CREATE DATABASE IF NOT EXISTS cyber_report;
USE cyber_report;
CREATE TABLE IF NOT EXISTS perfil_acesso (
  id INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(60) NOT NULL,
  habilitado TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
);
CREATE TABLE IF NOT EXISTS permissao (
  id INT NOT NULL AUTO_INCREMENT,
  codigo VARCHAR(80) NOT NULL,
  habilitado TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
);
CREATE TABLE IF NOT EXISTS perfil_permissao (
  perfil_id INT NOT NULL,
  permissao_id INT NOT NULL,
  PRIMARY KEY (perfil_id, permissao_id)
  -- FOREIGN KEY (perfil_id) REFERENCES perfil_acesso(id),
  -- FOREIGN KEY (permissao_id) REFERENCES permissao(id)
);
CREATE TABLE IF NOT EXISTS usuario (
  id INT NOT NULL AUTO_INCREMENT,
  perfil_id INT NOT NULL,
  nome VARCHAR(150) NOT NULL,
  cpf VARCHAR(20) NOT NULL UNIQUE,
  email VARCHAR(150) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL,
  telefone VARCHAR(30) DEFAULT NULL,
  cargo VARCHAR(80) DEFAULT NULL,
  especialidade VARCHAR(80) DEFAULT NULL,
  habilitado TINYINT NOT NULL DEFAULT 1,
  ultimo_login DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
  -- FOREIGN KEY (perfil_id) REFERENCES perfil_acesso(id)
);
CREATE TABLE IF NOT EXISTS endereco (
  id INT NOT NULL AUTO_INCREMENT,
  cep VARCHAR(20) NOT NULL,
  rua VARCHAR(255) NOT NULL,
  numero VARCHAR(20) NOT NULL,
  complemento VARCHAR(100),
  bairro VARCHAR(100) NOT NULL,
  cidade VARCHAR(100) NOT NULL,
  estado VARCHAR(100) NOT NULL,
  pais VARCHAR(100) NOT NULL,
  PRIMARY KEY (id)
);
CREATE TABLE IF NOT EXISTS empresa (
  id INT NOT NULL AUTO_INCREMENT,
  endereco_id INT NOT NULL,
  responsavel_id INT NOT NULL,
  nome_fantasia VARCHAR(150) DEFAULT NULL,
  razao_social VARCHAR(180) NOT NULL,
  cnpj VARCHAR(20) NOT NULL UNIQUE,
  email_contato VARCHAR(150) NOT NULL,
  telefone VARCHAR(20) DEFAULT NULL,
  responsavel VARCHAR(150) NOT NULL,
  habilitado TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
  -- FOREIGN KEY (endereco_id) REFERENCES endereco(id),
  -- FOREIGN KEY (responsavel_id) REFERENCES usuario(id)
);
CREATE TABLE IF NOT EXISTS checklist (
  id INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(80) NOT NULL,
  habilitado TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
);
CREATE TABLE IF NOT EXISTS checklist_item (
  id INT NOT NULL AUTO_INCREMENT,
  checklist_id INT NOT NULL,
  titulo VARCHAR(80) NOT NULL,
  referencia VARCHAR(120) DEFAULT NULL,
  obrigatorio TINYINT NOT NULL DEFAULT 1,
  habilitado TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
  -- FOREIGN KEY (checklist_id) REFERENCES checklist(id)
);
CREATE TABLE IF NOT EXISTS modelo_pentest (
  id INT NOT NULL AUTO_INCREMENT,
  checklist_id INT NOT NULL,
  nome VARCHAR(255) NOT NULL,
  categoria VARCHAR(80) NOT NULL,
  metodologia VARCHAR(80) NOT NULL,
  framework VARCHAR(80) NOT NULL,
  nv_risco ENUM('BAIXO', 'MEDIO', 'ALTO', 'CRITICO') NOT NULL,
  descricao TEXT,
  habilitado TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
  -- FOREIGN KEY (checklist_id) REFERENCES checklist(id)
);
CREATE TABLE IF NOT EXISTS projeto (
  id INT NOT NULL AUTO_INCREMENT,
  empresa_id INT NOT NULL,
  modelo_pentest_id INT NOT NULL,
  nome VARCHAR(80) NOT NULL,
  data_inicio DATE DEFAULT NULL,
  data_fim_prevista DATE DEFAULT NULL,
  data_fim_real DATE DEFAULT NULL,
  horas_contratadas DECIMAL(8, 2) NOT NULL DEFAULT 0.00,
  horas_executadas DECIMAL(8, 2) NOT NULL DEFAULT 0.00,
  tipo ENUM ('BLACK BOX', 'GRAY BOX', 'WHITE BOX') NOT NULL,
  nivel_sigilo ENUM ('INTERNO', 'EXTERNO') NOT NULL,
  escopo TEXT NOT NULL,
  alvo TEXT NOT NULL,
  contrato VARCHAR(255) DEFAULT NULL,
  restricao TEXT,
  habilitado TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
  -- FOREIGN KEY (empresa_id) REFERENCES empresa(id),
  -- FOREIGN KEY (modelo_pentest_id) REFERENCES modelo_pentest(id)
);
CREATE TABLE IF NOT EXISTS projeto_usuario (
  projeto_id INT NOT NULL,
  usuario_id INT NOT NULL,
  papel ENUM('GESTOR', 'LIDER', 'ESPECIALISTA') NOT NULL,
  habilitado TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (projeto_id, usuario_id, papel)
  -- FOREIGN KEY (projeto_id) REFERENCES projeto(id),
  -- FOREIGN KEY (usuario_id) REFERENCES usuario(id)
);
CREATE TABLE IF NOT EXISTS cronometro_registro (
  id INT NOT NULL AUTO_INCREMENT,
  projeto_id INT NOT NULL,
  ip_analista VARCHAR(45) DEFAULT NULL,
  ip_alvo VARCHAR(45) NOT NULL,
  inicio DATETIME NOT NULL,
  fim DATETIME DEFAULT NULL,
  pausa DATETIME DEFAULT NULL,
  duracao_minutos INT NOT NULL DEFAULT 0,
  habilitado TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
  -- FOREIGN KEY (projeto_id) REFERENCES projeto(id)
);
CREATE TABLE IF NOT EXISTS cronometro_log (
  id INT NOT NULL AUTO_INCREMENT,
  cronometro_id INT NOT NULL,
  usuario_id INT NOT NULL,
  ip_analista INT NOT NULL,
  ip_alvo INT NOT NULL,
  inicio DATETIME NOT NULL,
  fim DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
  -- FOREIGN KEY (cronometro_id) REFERENCES cronometro_registro(id),
  -- FOREIGN KEY (ip_analista) REFERENCES cronometro_registro(ip_analista),
  -- FOREIGN KEY (ip_alvo) REFERENCES cronometro_registro(ip_alvo), 
  -- FOREIGN KEY (usuario_id) REFERENCES usuario(id)
);

CREATE TABLE IF NOT EXISTS vulnerabilidade (
  id INT NOT NULL AUTO_INCREMENT,
  projeto_id INT NOT NULL,
  nome VARCHAR(80) NOT NULL,
  cvss DECIMAL(3, 1) CHECK (cvss >= 0.0 AND cvss <= 10.0),
  cve VARCHAR (50),
  descricao VARCHAR(255),
  descricao_tecnica TEXT NOT NULL,
  categoria VARCHAR(120) DEFAULT NULL,
  severidade_vulnerabilidade ENUM('BAIXA', 'MEDIA', 'ALTA', 'CRITICA') NOT NULL,
  habilitado TINYINT NOT NULL DEFAULT 1,
  impacto_negocio TEXT,
  PRIMARY KEY (id)
  -- FOREIGN KEY (projeto_id) REFERENCES projeto(id)
);
CREATE TABLE IF NOT EXISTS evidencia (
  id INT NOT NULL AUTO_INCREMENT,
  vulnerabilidade_id INT DEFAULT NULL,
  nome_arquivo VARCHAR(180) NOT NULL,
  caminho_arquivo VARCHAR(255) NOT NULL,
  tipo_arquivo VARCHAR(80) DEFAULT NULL,
  descricao TEXT,
  PRIMARY KEY (id)
  -- FOREIGN KEY (vulnerabilidade_id) REFERENCES vulnerabilidade(id)
);
CREATE TABLE IF NOT EXISTS banco_conhecimento (
  id INT NOT NULL AUTO_INCREMENT,
  vulnerabilidade_id INT NOT NULL,
  titulo VARCHAR(255) NOT NULL,
  categoria VARCHAR(255) DEFAULT NULL,
  conteudo TEXT NOT NULL,
  tecnica_utilizada TEXT,
  solucao_recomendada TEXT,
  habilitado TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
  -- FOREIGN KEY (vulnerabilidade_id) REFERENCES vulnerabilidade(id)
);
