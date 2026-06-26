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

CREATE TABLE pentest_tipos (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    nome              VARCHAR(255) NOT NULL,
    descricao_breve   VARCHAR(500),
    descricao_completa TEXT,
    categoria         VARCHAR(50),
    modelo            VARCHAR(50),
    tecnica           VARCHAR(50),
    frameworks        VARCHAR(500),
    checklist         VARCHAR(255),
    nivel_profundidade VARCHAR(50),
    horas_execucao    INT,
    status            TINYINT DEFAULT 1
);

INSERT INTO pentest_tipos (nome, descricao_breve, descricao_completa, categoria, modelo, tecnica, frameworks, checklist, nivel_profundidade, horas_execucao, status) VALUES
(
    'Web Application Pentest',
    'Teste de penetração completo em aplicações web, incluindo OWASP Top 10 e lógica de negócio.',
    'Avaliação abrangente de segurança em aplicações web com foco na identificação de vulnerabilidades do OWASP Top 10, falhas de autenticação, injeções, exposição de dados sensíveis e falhas na lógica de negócio.',
    'WEB', 'Black Box', 'Híbrida', 'OWASP,PTES', 'OWASP', 'Standard Security Assessment', 40, 1
),
(
    'Mobile App Security Assessment',
    'Avaliação de segurança em aplicações mobile iOS e Android seguindo o OWASP MASVS.',
    'Análise estática e dinâmica de aplicativos mobile para iOS e Android, verificando armazenamento inseguro de dados, comunicação insegura, controles de autenticação e controles do lado do cliente.',
    'MOBILE', 'Gray Box', 'Manual', 'OWASP', 'OWASP MASVS', 'Deep Security Assessment', 32, 1
),
(
    'Cloud Security Assessment',
    'Avaliação de segurança em ambientes cloud (AWS, Azure, GCP) com foco em configurações e IAM.',
    'Revisão completa de configurações de infraestrutura em nuvem, políticas de IAM, exposição de buckets, grupos de segurança, criptografia de dados em repouso e em trânsito, e conformidade com benchmarks CIS.',
    'CLOUD', 'Black Box', 'Híbrida', 'NIST,CIS,CSA,MITRE ATT&CK,PTES,ISO 27001', 'CIS Benchmarks', 'Advanced Red Team', 60, 0
),
(
    'Social Engineering Campaign',
    'Campanha de engenharia social incluindo phishing, vishing e simulação de ameaças internas.',
    'Simulação controlada de ataques de engenharia social para avaliar a conscientização dos colaboradores. Inclui campanhas de phishing por e-mail, testes de vishing por telefone e simulação de ameaça interna com acesso físico.',
    'ENG. SOCIAL', 'Gray Box', 'Manual', 'MITRE ATT&CK', 'NIST SP 800-115', 'Standard Security Assessment', 24, 1
),
(
    'Red Team Exercise',
    'Exercício completo de Red Team simulando cenários reais de ataque APT contra a organização.',
    'Simulação de ataque avançado e persistente (APT) cobrindo todas as fases do ciclo de vida do ataque: reconhecimento, armamento, entrega, exploração, instalação, comando e controle e ações sobre objetivos.',
    'RED TEAMS', 'White Box', 'Automatizada', 'PTES,MITRE ATT&CK', 'PTES', 'Advanced Red Team', 120, 1
),
(
    'Network Infrastructure Pentest',
    'Teste de penetração em infraestrutura de rede interna e perimetral, incluindo switches e roteadores.',
    'Avaliação de segurança da infraestrutura de rede cobrindo varredura de portas, análise de protocolos, enumeração de serviços, exploração de vulnerabilidades em dispositivos de rede e segmentação de VLANs.',
    'WEB', 'Gray Box', 'Híbrida', 'NIST,PTES', 'CIS Benchmarks', 'Standard Security Assessment', 48, 1
),
(
    'API Security Testing',
    'Testes de segurança em APIs REST e GraphQL cobrindo o OWASP API Security Top 10.',
    'Análise de segurança focada em interfaces de programação de aplicações, verificando autenticação, autorização em nível de objeto e função, exposição excessiva de dados, limitação de taxa e injeções via endpoints.',
    'WEB', 'Black Box', 'Automatizada', 'OWASP,PTES', 'OWASP API Top 10', 'Deep Security Assessment', 20, 1
);
