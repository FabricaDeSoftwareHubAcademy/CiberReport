-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 17/07/2026 às 15:05
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `crud544`
--
CREATE DATABASE IF NOT EXISTS `crud544` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `crud544`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--
-- Erro ao ler a estrutura para a tabela crud544.usuario: #1932 - Table 'crud544.usuario' doesn't exist in engine
-- Erro ao ler dados para tabela crud544.usuario: #1064 - Você tem um erro de sintaxe no seu SQL próximo a 'FROM `crud544`.`usuario`' na linha 1
--
-- Banco de dados: `cyber_report`
--
CREATE DATABASE IF NOT EXISTS `cyber_report` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `cyber_report`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `banco_conhecimento`
--

CREATE TABLE `banco_conhecimento` (
  `id` int(11) NOT NULL,
  `vulnerabilidade_id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `categoria` varchar(255) DEFAULT NULL,
  `conteudo` text NOT NULL,
  `tecnica_utilizada` text DEFAULT NULL,
  `solucao_recomendada` text DEFAULT NULL,
  `habilitado` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `banco_conhecimento`
--

INSERT INTO `banco_conhecimento` (`id`, `vulnerabilidade_id`, `titulo`, `categoria`, `conteudo`, `tecnica_utilizada`, `solucao_recomendada`, `habilitado`) VALUES
(1, 1, 'Mitigação de Injeção SQL em Formulários de Autenticação', 'Injeção', 'Vulnerabilidades de SQL Injection em formulários de login são recorrentes em aplicações que constroem queries via concatenação de strings.', 'Testes manuais com payloads booleanos e automação com sqlmap.', 'Utilizar consultas parametrizadas (prepared statements) e validar/normalizar toda entrada do usuário no backend.', 1),
(2, 3, 'Riscos de Compartilhamentos de Rede sem Autenticação', 'Configuração Insegura', 'Compartilhamentos SMB configurados para acesso anônimo são um vetor comum de exfiltração de dados em redes corporativas.', 'Enumeração de compartilhamentos com smbclient e crackmapexec.', 'Desabilitar acesso anônimo (guest) no servidor SMB e aplicar controle de acesso baseado em grupos.', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `checklist`
--

CREATE TABLE `checklist` (
  `id` int(11) NOT NULL,
  `nome` varchar(80) NOT NULL,
  `descricao` varchar(1000) DEFAULT NULL,
  `categoria` varchar(150) DEFAULT NULL,
  `habilitado` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `checklist`
--

INSERT INTO `checklist` (`id`, `nome`, `descricao`, `categoria`, `habilitado`) VALUES
(2, 'Checklist de Rede Interna', 'test', 'dasdasd', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `checklist_item`
--

CREATE TABLE `checklist_item` (
  `id` int(11) NOT NULL,
  `checklist_id` int(11) NOT NULL,
  `titulo` varchar(80) NOT NULL,
  `referencia` varchar(120) DEFAULT NULL,
  `obrigatorio` tinyint(4) NOT NULL DEFAULT 1,
  `habilitado` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `checklist_item`
--

INSERT INTO `checklist_item` (`id`, `checklist_id`, `titulo`, `referencia`, `obrigatorio`, `habilitado`) VALUES
(15, 5, 'test', NULL, 1, 1),
(20, 2, 'Varredura de Portas e Serviços', NULL, 1, 1),
(21, 2, 'xasxsa', NULL, 1, 1),
(22, 2, 'xsaxasx', NULL, 1, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `checklist_item_catalogo`
--

CREATE TABLE `checklist_item_catalogo` (
  `id` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `referencia` varchar(255) DEFAULT NULL,
  `obrigatorio` tinyint(4) NOT NULL DEFAULT 1,
  `habilitado` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `checklist_item_catalogo`
--

INSERT INTO `checklist_item_catalogo` (`id`, `titulo`, `referencia`, `obrigatorio`, `habilitado`) VALUES
(15, 'test1', 'test1', 1, 1),
(20, 'Varredura de Portas e Serviços', NULL, 1, 1),
(21, 'xasxsa', NULL, 1, 1),
(22, 'xsaxasx', NULL, 1, 1),
(25, 'test2026', 'test1', 1, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `checklist_item_vinculo`
--

CREATE TABLE `checklist_item_vinculo` (
  `id` int(11) NOT NULL,
  `checklist_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `checklist_item_vinculo`
--

INSERT INTO `checklist_item_vinculo` (`id`, `checklist_id`, `item_id`) VALUES
(13, 2, 21),
(14, 2, 22);

-- --------------------------------------------------------

--
-- Estrutura para tabela `cronometro_log`
--

CREATE TABLE `cronometro_log` (
  `id` int(11) NOT NULL,
  `cronometro_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `ip_analista` int(11) NOT NULL,
  `ip_alvo` int(11) NOT NULL,
  `inicio` datetime NOT NULL,
  `fim` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cronometro_log`
--

INSERT INTO `cronometro_log` (`id`, `cronometro_id`, `usuario_id`, `ip_analista`, `ip_alvo`, `inicio`, `fim`) VALUES
(1, 1, 2, 1, 1, '2026-05-05 09:00:00', '2026-05-05 12:30:00'),
(2, 2, 4, 2, 2, '2026-05-06 13:00:00', '2026-05-06 17:00:00'),
(3, 3, 3, 3, 3, '2026-06-02 08:30:00', '2026-06-02 11:45:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cronometro_registro`
--

CREATE TABLE `cronometro_registro` (
  `id` int(11) NOT NULL,
  `projeto_id` int(11) NOT NULL,
  `ip_analista` varchar(45) DEFAULT NULL,
  `ip_alvo` varchar(45) NOT NULL,
  `inicio` datetime NOT NULL,
  `fim` datetime DEFAULT NULL,
  `pausa` datetime DEFAULT NULL,
  `duracao_minutos` int(11) NOT NULL DEFAULT 0,
  `habilitado` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cronometro_registro`
--

INSERT INTO `cronometro_registro` (`id`, `projeto_id`, `ip_analista`, `ip_alvo`, `inicio`, `fim`, `pausa`, `duracao_minutos`, `habilitado`) VALUES
(1, 1, '192.168.10.15', '200.150.30.10', '2026-05-05 09:00:00', '2026-05-05 12:30:00', NULL, 210, 1),
(2, 1, '192.168.10.16', '200.150.30.11', '2026-05-06 13:00:00', '2026-05-06 17:00:00', '2026-05-06 15:00:00', 210, 1),
(3, 2, '10.0.5.20', '10.20.1.5', '2026-06-02 08:30:00', '2026-06-02 11:45:00', NULL, 195, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `empresa`
--

CREATE TABLE `empresa` (
  `id` int(11) NOT NULL,
  `endereco_id` int(11) NOT NULL,
  `responsavel_id` int(11) NOT NULL,
  `nome_fantasia` varchar(150) DEFAULT NULL,
  `razao_social` varchar(180) NOT NULL,
  `cnpj` varchar(20) NOT NULL,
  `email_contato` varchar(150) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `responsavel` varchar(150) NOT NULL,
  `habilitado` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `empresa`
--

INSERT INTO `empresa` (`id`, `endereco_id`, `responsavel_id`, `nome_fantasia`, `razao_social`, `cnpj`, `email_contato`, `telefone`, `responsavel`, `habilitado`) VALUES
(1, 1, 5, 'TechPantanal Soluções', 'TechPantanal Soluções em Tecnologia LTDA', '12.345.678/0001-90', 'contato@techpantanal.com.br', '(67) 3321-4455', 'Carlos Mendes', 1),
(2, 3, 5, 'Finasul Bank', 'Finasul Instituição Financeira S.A.', '98.765.432/0001-10', 'seguranca@finasul.com.br', '(11) 3987-6655', 'Carlos Mendes', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `endereco`
--

CREATE TABLE `endereco` (
  `id` int(11) NOT NULL,
  `cep` varchar(20) NOT NULL,
  `rua` varchar(255) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `complemento` varchar(100) DEFAULT NULL,
  `bairro` varchar(100) NOT NULL,
  `cidade` varchar(100) NOT NULL,
  `estado` varchar(100) NOT NULL,
  `pais` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `endereco`
--

INSERT INTO `endereco` (`id`, `cep`, `rua`, `numero`, `complemento`, `bairro`, `cidade`, `estado`, `pais`) VALUES
(1, '79002-200', 'Avenida Afonso Pena', '4000', 'Sala 12', 'Centro', 'Campo Grande', 'Mato Grosso do Sul', 'Brasil'),
(2, '79117-900', 'Rua Pedro Celestino', '850', NULL, 'Vila Carvalho', 'Campo Grande', 'Mato Grosso do Sul', 'Brasil'),
(3, '01310-100', 'Avenida Paulista', '1578', 'Conjunto 304', 'Bela Vista', 'São Paulo', 'São Paulo', 'Brasil');

-- --------------------------------------------------------

--
-- Estrutura para tabela `evidencia`
--

CREATE TABLE `evidencia` (
  `id` int(11) NOT NULL,
  `vulnerabilidade_id` int(11) DEFAULT NULL,
  `nome_arquivo` varchar(180) NOT NULL,
  `caminho_arquivo` varchar(255) NOT NULL,
  `tipo_arquivo` varchar(80) DEFAULT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `evidencia`
--

INSERT INTO `evidencia` (`id`, `vulnerabilidade_id`, `nome_arquivo`, `caminho_arquivo`, `tipo_arquivo`, `descricao`) VALUES
(1, 1, 'sqli_login_burpsuite.png', '/evidencias/projeto1/sqli_login_burpsuite.png', 'image/png', 'Captura de tela do Burp Suite demonstrando a injeção SQL no campo \"usuario\".'),
(2, 1, 'dump_tabela_usuarios.txt', '/evidencias/projeto1/dump_tabela_usuarios.txt', 'text/plain', 'Saída do sqlmap com o dump parcial da tabela de usuários.'),
(3, 3, 'smb_acesso_anonimo.png', '/evidencias/projeto2/smb_acesso_anonimo.png', 'image/png', 'Captura de tela do acesso anônimo ao compartilhamento \"Financeiro\" via smbclient.');

-- --------------------------------------------------------

--
-- Estrutura para tabela `modelo_pentest`
--

CREATE TABLE `modelo_pentest` (
  `id` int(11) NOT NULL,
  `checklist_id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `categoria` varchar(80) NOT NULL,
  `metodologia` varchar(80) NOT NULL,
  `framework` varchar(80) NOT NULL,
  `nv_risco` enum('BAIXO','MEDIO','ALTO','CRITICO') NOT NULL,
  `descricao` text DEFAULT NULL,
  `habilitado` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `modelo_pentest`
--

INSERT INTO `modelo_pentest` (`id`, `checklist_id`, `nome`, `categoria`, `metodologia`, `framework`, `nv_risco`, `descricao`, `habilitado`) VALUES
(1, 1, 'Pentest Aplicação Web Externa', 'Web Application', 'PTES', 'OWASP', 'ALTO', 'Modelo padrão para testes de intrusão em aplicações web expostas à internet.', 1),
(2, 2, 'Pentest Infraestrutura Interna', 'Rede Interna', 'OSSTMM', 'NIST', 'MEDIO', 'Modelo para avaliação de segurança em redes corporativas internas.', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pentest_tipos`
--

CREATE TABLE `pentest_tipos` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao_breve` varchar(500) DEFAULT NULL,
  `descricao_completa` text DEFAULT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `modelo` varchar(50) DEFAULT NULL,
  `tecnica` varchar(50) DEFAULT NULL,
  `frameworks` varchar(500) DEFAULT NULL,
  `checklist` varchar(255) DEFAULT NULL,
  `nivel_profundidade` varchar(50) DEFAULT NULL,
  `horas_execucao` int(11) DEFAULT NULL,
  `habilitado` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pentest_tipos`
--

INSERT INTO `pentest_tipos` (`id`, `nome`, `descricao_breve`, `descricao_completa`, `categoria`, `modelo`, `tecnica`, `frameworks`, `checklist`, `nivel_profundidade`, `horas_execucao`, `habilitado`) VALUES
(1, 'Web Application Pentest', 'Teste de penetração completo em aplicações web, incluindo OWASP Top 10 e lógica de negócio.', 'Avaliação abrangente de segurança em aplicações web com foco na identificação de vulnerabilidades do OWASP Top 10, falhas de autenticação, injeções, exposição de dados sensíveis e falhas na lógica de negócio.', 'WEB', 'Black Box', 'Híbrida', 'OWASP,PTES', 'OWASP', 'Standard Security Assessment', 40, 1),
(2, 'Mobile App Security Assessment', 'Avaliação de segurança em aplicações mobile iOS e Android seguindo o OWASP MASVS.', 'Análise estática e dinâmica de aplicativos mobile para iOS e Android, verificando armazenamento inseguro de dados, comunicação insegura, controles de autenticação e controles do lado do cliente.', 'MOBILE', 'Gray Box', 'Manual', 'OWASP', 'OWASP MASVS', 'Deep Security Assessment', 32, 1),
(3, 'Cloud Security Assessment', 'Avaliação de segurança em ambientes cloud (AWS, Azure, GCP) com foco em configurações e IAM.', 'Revisão completa de configurações de infraestrutura em nuvem, políticas de IAM, exposição de buckets, grupos de segurança, criptografia de dados em repouso e em trânsito, e conformidade com benchmarks CIS.', 'CLOUD', 'Black Box', 'Híbrida', 'NIST,CIS,CSA,MITRE ATT&CK,PTES,ISO 27001', 'CIS Benchmarks', 'Advanced Red Team', 60, 0),
(4, 'Social Engineering Campaign', 'Campanha de engenharia social incluindo phishing, vishing e simulação de ameaças internas.', 'Simulação controlada de ataques de engenharia social para avaliar a conscientização dos colaboradores. Inclui campanhas de phishing por e-mail, testes de vishing por telefone e simulação de ameaça interna com acesso físico.', 'ENG. SOCIAL', 'Gray Box', 'Manual', 'MITRE ATT&CK', 'NIST SP 800-115', 'Standard Security Assessment', 24, 0),
(5, 'Red Team Exercise', 'Exercício completo de Red Team simulando cenários reais de ataque APT contra a organização.', 'Simulação de ataque avançado e persistente (APT) cobrindo todas as fases do ciclo de vida do ataque: reconhecimento, armamento, entrega, exploração, instalação, comando e controle e ações sobre objetivos.', 'RED TEAMS', 'White Box', 'Automatizada', 'PTES,MITRE ATT&CK', 'PTES', 'Advanced Red Team', 120, 1),
(6, 'Network Infrastructure Pentest', 'Teste de penetração em infraestrutura de rede interna e perimetral, incluindo switches e roteadores.', 'Avaliação de segurança da infraestrutura de rede cobrindo varredura de portas, análise de protocolos, enumeração de serviços, exploração de vulnerabilidades em dispositivos de rede e segmentação de VLANs.', 'WEB', 'Gray Box', 'Híbrida', 'NIST,PTES', 'CIS Benchmarks', 'Standard Security Assessment', 48, 0),
(7, 'API Security Testing', 'Testes de segurança em APIs REST e GraphQL cobrindo o OWASP API Security Top 10.', 'Análise de segurança focada em interfaces de programação de aplicações, verificando autenticação, autorização em nível de objeto e função, exposição excessiva de dados, limitação de taxa e injeções via endpoints.', 'WEB', 'Black Box', 'Automatizada', 'OWASP,PTES', 'OWASP API Top 10', 'Deep Security Assessment', 20, 1),
(8, 'IoT Security Assessment', 'Avaliação de segurança em dispositivos IoT, firmware e protocolos de comunicação embarcados.', 'Análise de firmware, interfaces de hardware, protocolos de comunicação (MQTT, CoAP, Zigbee) e superfícies de ataque em dispositivos IoT industriais e residenciais.', 'WEB', 'Gray Box', 'Manual', 'OWASP,NIST', 'OWASP IoT Top 10', 'Deep Security Assessment', 56, 1),
(9, 'Active Directory Pentest', 'Teste de penetração focado em ambientes Active Directory, incluindo escalada de privilégios e movimentação lateral.', 'Simulação de ataques internos em ambientes Windows Active Directory cobrindo Kerberoasting, Pass-the-Hash, DCSync, abuso de ACLs e caminhos de escalada de domínio.', 'RED TEAMS', 'White Box', 'Híbrida', 'MITRE ATT&CK,PTES', 'CIS Benchmarks', 'Advanced Red Team', 80, 1),
(10, 'Phishing Simulation', 'Simulação de campanhas de phishing direcionado para medir o índice de conscientização dos colaboradores.', 'Execução de campanhas de spear phishing personalizadas por setor, com rastreamento de cliques, coleta de credenciais em ambiente controlado e relatório de exposição por departamento.', 'ENG. SOCIAL', 'Black Box', 'Automatizada', 'MITRE ATT&CK', 'NIST SP 800-115', 'Basic Security Assessment', 16, 1),
(12, 'Thick Client Application Pentest', 'Teste de penetração em aplicações desktop (thick client), análise de comunicação e armazenamento local.', 'Avaliação de segurança em aplicações desktop cobrindo análise de tráfego entre cliente e servidor, armazenamento inseguro de dados locais, bypass de controles client-side e engenharia reversa de binários.', 'WEB', 'Gray Box', 'Manual', 'OWASP,PTES', 'OWASP', 'Deep Security Assessment', 44, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `perfil_acesso`
--

CREATE TABLE `perfil_acesso` (
  `id` int(11) NOT NULL,
  `nome` varchar(60) NOT NULL,
  `habilitado` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `perfil_acesso`
--

INSERT INTO `perfil_acesso` (`id`, `nome`, `habilitado`) VALUES
(1, 'Administrador', 1),
(2, 'Pentester', 1),
(3, 'Cliente', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `perfil_permissao`
--

CREATE TABLE `perfil_permissao` (
  `perfil_id` int(11) NOT NULL,
  `permissao_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `perfil_permissao`
--

INSERT INTO `perfil_permissao` (`perfil_id`, `permissao_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(2, 6),
(2, 7),
(2, 8),
(2, 9),
(3, 6),
(3, 9);

-- --------------------------------------------------------

--
-- Estrutura para tabela `permissao`
--

CREATE TABLE `permissao` (
  `id` int(11) NOT NULL,
  `codigo` varchar(80) NOT NULL,
  `habilitado` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `permissao`
--

INSERT INTO `permissao` (`id`, `codigo`, `habilitado`) VALUES
(1, 'usuario.criar', 1),
(2, 'usuario.editar', 1),
(3, 'usuario.excluir', 1),
(4, 'projeto.criar', 1),
(5, 'projeto.editar', 1),
(6, 'projeto.visualizar', 1),
(7, 'vulnerabilidade.criar', 1),
(8, 'vulnerabilidade.editar', 1),
(9, 'relatorio.exportar', 1),
(10, 'checklist.gerenciar', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `projeto`
--

CREATE TABLE `projeto` (
  `id` int(11) NOT NULL,
  `empresa_id` int(11) NOT NULL,
  `modelo_pentest_id` int(11) NOT NULL,
  `nome` varchar(80) NOT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_fim_prevista` date DEFAULT NULL,
  `data_fim_real` date DEFAULT NULL,
  `horas_contratadas` decimal(8,2) NOT NULL DEFAULT 0.00,
  `horas_executadas` decimal(8,2) NOT NULL DEFAULT 0.00,
  `tipo` enum('BLACK BOX','GRAY BOX','WHITE BOX') NOT NULL,
  `nivel_sigilo` enum('INTERNO','EXTERNO') NOT NULL,
  `escopo` text NOT NULL,
  `alvo` text NOT NULL,
  `contrato` varchar(255) DEFAULT NULL,
  `restricao` text DEFAULT NULL,
  `habilitado` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `projeto`
--

INSERT INTO `projeto` (`id`, `empresa_id`, `modelo_pentest_id`, `nome`, `data_inicio`, `data_fim_prevista`, `data_fim_real`, `horas_contratadas`, `horas_executadas`, `tipo`, `nivel_sigilo`, `escopo`, `alvo`, `contrato`, `restricao`, `habilitado`) VALUES
(1, 1, 1, 'Pentest Portal Institucional TechPantanal', '2026-05-04', '2026-05-18', '2026-05-17', 80.00, 76.50, 'GRAY BOX', 'EXTERNO', 'Avaliação de segurança do portal institucional e API de autenticação.', 'portal.techpantanal.com.br, api.techpantanal.com.br', 'CONTR-2026-0041', 'Não realizar testes de negação de serviço (DoS).', 1),
(2, 2, 2, 'Pentest Rede Interna Finasul - Agência São Paulo', '2026-06-01', '2026-06-20', NULL, 120.00, 64.00, 'BLACK BOX', 'INTERNO', 'Avaliação de segurança da infraestrutura de rede interna, incluindo servidores e estações de trabalho.', '10.20.0.0/22', 'CONTR-2026-0058', 'Testes restritos ao horário comercial, das 08h às 18h.', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `projeto_usuario`
--

CREATE TABLE `projeto_usuario` (
  `projeto_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `papel` enum('GESTOR','LIDER','ESPECIALISTA') NOT NULL,
  `habilitado` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `projeto_usuario`
--

INSERT INTO `projeto_usuario` (`projeto_id`, `usuario_id`, `papel`, `habilitado`) VALUES
(1, 1, 'GESTOR', 1),
(1, 2, 'LIDER', 1),
(1, 4, 'ESPECIALISTA', 1),
(2, 1, 'GESTOR', 1),
(2, 2, 'ESPECIALISTA', 1),
(2, 3, 'LIDER', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `perfil_id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `cpf` varchar(20) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `telefone` varchar(30) DEFAULT NULL,
  `cargo` varchar(80) DEFAULT NULL,
  `especialidade` varchar(80) DEFAULT NULL,
  `habilitado` tinyint(4) NOT NULL DEFAULT 1,
  `ultimo_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id`, `perfil_id`, `nome`, `cpf`, `email`, `senha`, `telefone`, `cargo`, `especialidade`, `habilitado`, `ultimo_login`) VALUES
(1, 1, 'Fábio Rocha', '00011122233', 'fabio.rocha@cyberreport.com.br', '$2y$10$N9qo8uLOickgx2ZMRZoMye', '(67) 99876-5432', 'Administrador de Sistema', NULL, 1, '2026-06-25 09:14:00'),
(2, 2, 'Mariana Souza', '11122233344', 'mariana.souza@cyberreport.com.br', '$2y$10$abcd1234efgh5678ijklmn', '(67) 99123-4567', 'Pentester Sênior', 'Aplicações Web', 1, '2026-06-24 17:32:00'),
(3, 2, 'Rafael Lima', '22233344455', 'rafael.lima@cyberreport.com.br', '$2y$10$opqr9876stuv5432wxyz12', '(67) 99234-5678', 'Pentester Pleno', 'Infraestrutura de Redes', 1, '2026-06-23 14:05:00'),
(4, 2, 'Bianca Carvalho', '33344455566', 'bianca.carvalho@cyberreport.com.br', '$2y$10$lmno3456pqrs7890tuvw34', '(67) 99345-6789', 'Pentester Júnior', 'Mobile', 1, '2026-06-22 10:48:00'),
(5, 3, 'Carlos Mendes', '44455566677', 'carlos.mendes@empresacliente.com.br', '$2y$10$wxyz5678abcd1234efgh90', '(67) 99456-7890', 'Gerente de TI', NULL, 1, '2026-06-20 08:22:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `vulnerabilidade`
--

CREATE TABLE `vulnerabilidade` (
  `id` int(11) NOT NULL,
  `projeto_id` int(11) NOT NULL,
  `nome` varchar(80) NOT NULL,
  `cvss` decimal(3,1) DEFAULT NULL CHECK (`cvss` >= 0.0 and `cvss` <= 10.0),
  `cve` varchar(50) DEFAULT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `descricao_tecnica` text NOT NULL,
  `categoria` varchar(120) DEFAULT NULL,
  `severidade_vulnerabilidade` enum('BAIXA','MEDIA','ALTA','CRITICA') NOT NULL,
  `habilitado` tinyint(4) NOT NULL DEFAULT 1,
  `impacto_negocio` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `vulnerabilidade`
--

INSERT INTO `vulnerabilidade` (`id`, `projeto_id`, `nome`, `cvss`, `cve`, `descricao`, `descricao_tecnica`, `categoria`, `severidade_vulnerabilidade`, `habilitado`, `impacto_negocio`) VALUES
(1, 1, 'Injeção SQL no formulário de login', 9.1, NULL, 'Parâmetro \"usuario\" vulnerável a SQL Injection.', 'O parâmetro \"usuario\" do endpoint /api/login não realiza sanitização adequada, permitindo a injeção de comandos SQL arbitrários. Foi possível extrair hashes de senha da tabela de usuários através de payloads booleanos.', 'Injeção', 'CRITICA', 1, 'Possível vazamento total da base de usuários e comprometimento de contas administrativas.'),
(2, 1, 'Cross-Site Scripting (XSS) Refletido', 6.1, NULL, 'Campo de busca reflete entrada do usuário sem sanitização.', 'O parâmetro \"q\" do endpoint /busca reflete o valor informado diretamente no HTML de resposta, permitindo a execução de scripts arbitrários no navegador da vítima.', 'Cross-Site Scripting', 'MEDIA', 1, 'Roubo de sessão de usuários autenticados e possível desfiguração de páginas.'),
(3, 2, 'Compartilhamento SMB sem autenticação', 7.5, NULL, 'Compartilhamento de rede acessível sem credenciais.', 'O servidor de arquivos 10.20.1.5 expõe um compartilhamento SMB (\"Financeiro\") acessível anonimamente, contendo planilhas com dados sensíveis de clientes.', 'Configuração Insegura', 'ALTA', 1, 'Exposição de dados financeiros sensíveis de clientes a qualquer usuário da rede interna.'),
(4, 2, 'Credenciais padrão em switch de rede', 5.3, NULL, 'Dispositivo de rede com credenciais padrão de fábrica.', 'O switch core (10.20.0.1) aceita login com as credenciais padrão de fábrica, permitindo acesso à configuração de VLANs e portas.', 'Configuração Insegura', 'MEDIA', 1, 'Possibilidade de reconfiguração maliciosa da rede, levando a interceptação de tráfego ou indisponibilidade.');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `banco_conhecimento`
--
ALTER TABLE `banco_conhecimento`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `checklist`
--
ALTER TABLE `checklist`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `checklist_item`
--
ALTER TABLE `checklist_item`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `checklist_item_catalogo`
--
ALTER TABLE `checklist_item_catalogo`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `checklist_item_vinculo`
--
ALTER TABLE `checklist_item_vinculo`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `cronometro_log`
--
ALTER TABLE `cronometro_log`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `cronometro_registro`
--
ALTER TABLE `cronometro_registro`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cnpj` (`cnpj`);

--
-- Índices de tabela `endereco`
--
ALTER TABLE `endereco`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `evidencia`
--
ALTER TABLE `evidencia`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `modelo_pentest`
--
ALTER TABLE `modelo_pentest`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pentest_tipos`
--
ALTER TABLE `pentest_tipos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `perfil_acesso`
--
ALTER TABLE `perfil_acesso`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `perfil_permissao`
--
ALTER TABLE `perfil_permissao`
  ADD PRIMARY KEY (`perfil_id`,`permissao_id`);

--
-- Índices de tabela `permissao`
--
ALTER TABLE `permissao`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `projeto`
--
ALTER TABLE `projeto`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `projeto_usuario`
--
ALTER TABLE `projeto_usuario`
  ADD PRIMARY KEY (`projeto_id`,`usuario_id`,`papel`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `vulnerabilidade`
--
ALTER TABLE `vulnerabilidade`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `banco_conhecimento`
--
ALTER TABLE `banco_conhecimento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `checklist`
--
ALTER TABLE `checklist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `checklist_item`
--
ALTER TABLE `checklist_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de tabela `checklist_item_catalogo`
--
ALTER TABLE `checklist_item_catalogo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de tabela `checklist_item_vinculo`
--
ALTER TABLE `checklist_item_vinculo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `cronometro_log`
--
ALTER TABLE `cronometro_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `cronometro_registro`
--
ALTER TABLE `cronometro_registro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `empresa`
--
ALTER TABLE `empresa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `endereco`
--
ALTER TABLE `endereco`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `evidencia`
--
ALTER TABLE `evidencia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `modelo_pentest`
--
ALTER TABLE `modelo_pentest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `pentest_tipos`
--
ALTER TABLE `pentest_tipos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `perfil_acesso`
--
ALTER TABLE `perfil_acesso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `permissao`
--
ALTER TABLE `permissao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `projeto`
--
ALTER TABLE `projeto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `vulnerabilidade`
--
ALTER TABLE `vulnerabilidade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- Banco de dados: `francaportifolio`
--
CREATE DATABASE IF NOT EXISTS `francaportifolio` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `francaportifolio`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `campeonato`
--
-- Erro ao ler a estrutura para a tabela francaportifolio.campeonato: #1932 - Table 'francaportifolio.campeonato' doesn't exist in engine
-- Erro ao ler dados para tabela francaportifolio.campeonato: #1064 - Você tem um erro de sintaxe no seu SQL próximo a 'FROM `francaportifolio`.`campeonato`' na linha 1

-- --------------------------------------------------------

--
-- Estrutura para tabela `campeonato_time`
--
-- Erro ao ler a estrutura para a tabela francaportifolio.campeonato_time: #1932 - Table 'francaportifolio.campeonato_time' doesn't exist in engine
-- Erro ao ler dados para tabela francaportifolio.campeonato_time: #1064 - Você tem um erro de sintaxe no seu SQL próximo a 'FROM `francaportifolio`.`campeonato_time`' na linha 1

-- --------------------------------------------------------

--
-- Estrutura para tabela `times`
--
-- Erro ao ler a estrutura para a tabela francaportifolio.times: #1932 - Table 'francaportifolio.times' doesn't exist in engine
-- Erro ao ler dados para tabela francaportifolio.times: #1064 - Você tem um erro de sintaxe no seu SQL próximo a 'FROM `francaportifolio`.`times`' na linha 1
--
-- Banco de dados: `frança portifolio`
--
CREATE DATABASE IF NOT EXISTS `frança portifolio` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `frança portifolio`;
--
-- Banco de dados: `movimenta_cg_db`
--
CREATE DATABASE IF NOT EXISTS `movimenta_cg_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `movimenta_cg_db`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `arquivo`
--

CREATE TABLE `arquivo` (
  `arquivo_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `caminho` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `contato`
--

CREATE TABLE `contato` (
  `contato_id` int(11) NOT NULL,
  `nome` varchar(225) DEFAULT NULL,
  `telefone` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `contato`
--

INSERT INTO `contato` (`contato_id`, `nome`, `telefone`) VALUES
(1, 'João Silva', '(67) 99999-1234'),
(2, 'Maria Santos', '(67) 98888-5678'),
(3, 'Pedro Oliveira', '(67) 97777-9012'),
(4, 'Ana Souza', '(67) 96666-3456'),
(5, 'Carlos Lima', '(67) 95555-7890'),
(6, 'João Silva', '(67) 99999-1234'),
(7, 'Maria Santos', '(67) 98888-5678'),
(8, 'Pedro Oliveira', '(67) 97777-9012'),
(9, 'Ana Souza', '(67) 96666-3456'),
(10, 'Carlos Lima', '(67) 95555-7890');

-- --------------------------------------------------------

--
-- Estrutura para tabela `contato_emergencia`
--

CREATE TABLE `contato_emergencia` (
  `pessoa_id` int(11) NOT NULL,
  `contato_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `contato_emergencia`
--

INSERT INTO `contato_emergencia` (`pessoa_id`, `contato_id`) VALUES
(1, 2),
(1, 3),
(2, 4),
(3, 5),
(4, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `demanda`
--

CREATE TABLE `demanda` (
  `demanda_id` int(11) NOT NULL,
  `motivo` varchar(225) DEFAULT NULL,
  `prazo` date DEFAULT NULL,
  `prioridade` enum('ALTA','MEDIA','BAIXA') DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `locais_id` int(11) DEFAULT NULL,
  `oficina_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `demanda`
--

INSERT INTO `demanda` (`demanda_id`, `motivo`, `prazo`, `prioridade`, `usuario_id`, `locais_id`, `oficina_id`) VALUES
(1, 'Necessidade de reforma no vestiário', '2024-03-30', 'ALTA', 2, 1, 1),
(2, 'Aquisição de novos equipamentos esportivos', '2024-04-15', 'MEDIA', 2, 1, 2),
(3, 'Manutenção da quadra de areia', '2024-02-28', 'BAIXA', 1, 2, 3),
(4, 'Ampliação do horário de funcionamento', '2024-03-10', 'MEDIA', 3, 3, 4);

-- --------------------------------------------------------

--
-- Estrutura para tabela `endereco`
--

CREATE TABLE `endereco` (
  `endereco_id` int(11) NOT NULL,
  `cep` varchar(8) DEFAULT NULL,
  `logradouro` varchar(225) DEFAULT NULL,
  `bairro` varchar(225) DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `complemento` varchar(225) DEFAULT NULL,
  `cidade` varchar(225) DEFAULT NULL,
  `uf` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `endereco`
--

INSERT INTO `endereco` (`endereco_id`, `cep`, `logradouro`, `bairro`, `numero`, `complemento`, `cidade`, `uf`) VALUES
(1, '79002000', 'Rua das Flores', 'Centro', 100, 'Sala 101', 'Campo Grande', 'MS'),
(2, '79030000', 'Av. Mato Grosso', 'Jardim América', 500, NULL, 'Campo Grande', 'MS'),
(3, '79040000', 'Rua 14 de Julho', 'Vila Progresso', 250, 'Apto 23', 'Campo Grande', 'MS'),
(4, '79050000', 'Av. Afonso Pena', 'Centro', 1000, NULL, 'Campo Grande', 'MS'),
(5, '79060000', 'Rua Bahia', 'Jardim dos Estados', 45, 'Casa', 'Campo Grande', 'MS'),
(6, '79002000', 'Rua das Flores', 'Centro', 100, 'Sala 101', 'Campo Grande', 'MS'),
(7, '79030000', 'Av. Mato Grosso', 'Jardim América', 500, NULL, 'Campo Grande', 'MS'),
(8, '79040000', 'Rua 14 de Julho', 'Vila Progresso', 250, 'Apto 23', 'Campo Grande', 'MS'),
(9, '79050000', 'Av. Afonso Pena', 'Centro', 1000, NULL, 'Campo Grande', 'MS'),
(10, '79060000', 'Rua Bahia', 'Jardim dos Estados', 45, 'Casa', 'Campo Grande', 'MS');

-- --------------------------------------------------------

--
-- Estrutura para tabela `fotos_visita`
--

CREATE TABLE `fotos_visita` (
  `arquivo_id` int(11) NOT NULL,
  `visita_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `frequencia`
--

CREATE TABLE `frequencia` (
  `frequencia_id` int(11) NOT NULL,
  `oficina_id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `frequencia_data` date NOT NULL,
  `situacao` enum('PRESENTE','AUSENTE','JUSTIFICADA','ABONADA') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `locais`
--

CREATE TABLE `locais` (
  `locais_id` int(11) NOT NULL,
  `nome` varchar(225) DEFAULT NULL,
  `nucleo` tinyint(1) DEFAULT NULL,
  `estruturas_disponiveis` text DEFAULT NULL,
  `horario_inicial` time DEFAULT NULL,
  `horario_final` time DEFAULT NULL,
  `endereco_id` int(11) DEFAULT NULL,
  `foto_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `locais`
--

INSERT INTO `locais` (`locais_id`, `nome`, `nucleo`, `estruturas_disponiveis`, `horario_inicial`, `horario_final`, `endereco_id`, `foto_id`) VALUES
(1, 'Centro Esportivo Municipal', 1, 'Quadra poliesportiva, vestiários, arquibancada', '08:00:00', '22:00:00', 1, NULL),
(2, 'Praça do Esporte', 0, 'Campo de futebol, quadra de areia', '06:00:00', '20:00:00', 2, NULL),
(3, 'Ginásio Poliesportivo', 1, 'Quadra coberta, academia, piscina', '07:00:00', '21:00:00', 3, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `matricula`
--

CREATE TABLE `matricula` (
  `oficina_id` int(11) NOT NULL,
  `pessoa_id` int(11) NOT NULL,
  `situacao` enum('APROVADO','REPROVADO','AGUARDANDO') DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `matricula`
--

INSERT INTO `matricula` (`oficina_id`, `pessoa_id`, `situacao`, `email`) VALUES
(1, 1, 'APROVADO', 'joao.silva@email.com'),
(1, 2, 'APROVADO', 'maria.oliveira@email.com'),
(1, 3, 'REPROVADO', 'pedro.santos@email.com'),
(1, 4, 'APROVADO', 'ana.souza@email.com'),
(1, 5, 'AGUARDANDO', 'carlos.lima@email.com'),
(1, 6, 'APROVADO', 'anaclara@email.com'),
(1, 7, 'APROVADO', 'bruno.costa@email.com'),
(1, 8, 'AGUARDANDO', 'carla.mendes@email.com'),
(1, 9, 'APROVADO', 'daniel.rocha@email.com'),
(1, 10, 'REPROVADO', 'eduarda.lima@email.com'),
(1, 11, 'APROVADO', 'felipe.andrade@email.com'),
(1, 12, 'APROVADO', 'gabriela.nunes@email.com'),
(1, 13, 'AGUARDANDO', 'henrique.martins@email.com'),
(1, 14, 'APROVADO', 'isabela.rocha@email.com'),
(1, 15, 'APROVADO', 'joaopedro@email.com'),
(1, 16, 'REPROVADO', 'larissa.ferreira@email.com'),
(1, 17, 'APROVADO', 'mateus.oliveira@email.com'),
(1, 18, 'APROVADO', 'natalia.cardoso@email.com'),
(1, 19, 'AGUARDANDO', 'otavio.lima@email.com'),
(1, 20, 'APROVADO', 'patricia.gomes@email.com'),
(1, 21, 'APROVADO', 'rafael.alves@email.com'),
(1, 22, 'REPROVADO', 'sofia.ribeiro@email.com'),
(1, 23, 'APROVADO', 'thiago.monteiro@email.com'),
(1, 24, 'APROVADO', 'valentina.castro@email.com'),
(1, 25, 'AGUARDANDO', 'william.barros@email.com');

-- --------------------------------------------------------

--
-- Estrutura para tabela `modalidade`
--

CREATE TABLE `modalidade` (
  `modalidade_id` int(11) NOT NULL,
  `nome` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `modalidade`
--

INSERT INTO `modalidade` (`modalidade_id`, `nome`) VALUES
(1, 'Futebol'),
(2, 'Basquete'),
(3, 'Vôlei'),
(4, 'Capoeira'),
(5, 'Dança'),
(6, 'Jiu-Jitsu'),
(7, 'Futebol'),
(8, 'Basquete'),
(9, 'Vôlei'),
(10, 'Capoeira'),
(11, 'Dança'),
(12, 'Jiu-Jitsu');

-- --------------------------------------------------------

--
-- Estrutura para tabela `modalidade_interesse`
--

CREATE TABLE `modalidade_interesse` (
  `locais_id` int(11) NOT NULL,
  `modalidade_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `modalidade_interesse`
--

INSERT INTO `modalidade_interesse` (`locais_id`, `modalidade_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 4),
(3, 5),
(3, 6);

-- --------------------------------------------------------

--
-- Estrutura para tabela `noticia`
--

CREATE TABLE `noticia` (
  `noticia_id` int(11) NOT NULL,
  `titulo` varchar(225) DEFAULT NULL,
  `data_noticia` date DEFAULT NULL,
  `publico` enum('CRIANCAS','JOVENS','ADULTOS','IDOSOS') DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `locais_id` int(11) DEFAULT NULL,
  `oficina_id` int(11) DEFAULT NULL,
  `imagem` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `noticia`
--

INSERT INTO `noticia` (`noticia_id`, `titulo`, `data_noticia`, `publico`, `descricao`, `locais_id`, `oficina_id`, `imagem`) VALUES
(1, 'Inscrições Abertas para Futebol Infantil', '2024-01-10', 'CRIANCAS', 'Estão abertas as inscrições para as turmas de futebol para crianças de 6 a 12 anos. As vagas são limitadas!', 1, 1, NULL),
(2, 'Basquete Juvenil tem nova turma', '2024-01-15', 'JOVENS', 'Nova turma de basquete para jovens de 13 a 17 anos com início em fevereiro.', 1, 2, NULL),
(3, 'Apresentação de Capoeira no Praça do Esporte', '2024-01-20', 'ADULTOS', 'Apresentação especial de capoeira com participação de mestres convidados.', 2, 3, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `oficina`
--

CREATE TABLE `oficina` (
  `oficina_id` int(11) NOT NULL,
  `nome` varchar(225) DEFAULT NULL,
  `qtd_vagas` int(11) DEFAULT NULL,
  `publico` enum('CRIANCAS','JOVENS','ADULTOS','IDOSOS') DEFAULT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `dia_semana` varchar(75) DEFAULT NULL,
  `horario_inicio` time DEFAULT NULL,
  `horario_fim` time DEFAULT NULL,
  `professor` int(11) DEFAULT NULL,
  `coordenador` int(11) DEFAULT NULL,
  `locais_id` int(11) DEFAULT NULL,
  `modalidade_id` int(11) DEFAULT NULL,
  `foto_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `oficina`
--

INSERT INTO `oficina` (`oficina_id`, `nome`, `qtd_vagas`, `publico`, `data_inicio`, `data_fim`, `dia_semana`, `horario_inicio`, `horario_fim`, `professor`, `coordenador`, `locais_id`, `modalidade_id`, `foto_id`) VALUES
(1, 'Futebol para Crianças', 30, 'CRIANCAS', '2024-01-15', '2024-12-15', 'Segunda, Quarta, Sexta', '09:00:00', '11:00:00', 3, 2, 1, 1, NULL),
(2, 'Basquete Juvenil', 25, 'JOVENS', '2024-02-01', '2024-12-20', 'Terça, Quinta', '14:00:00', '16:00:00', 3, 2, 1, 2, NULL),
(3, 'Capoeira Adultos', 20, 'ADULTOS', '2024-01-10', '2024-12-10', 'Segunda, Quarta', '19:00:00', '21:00:00', 3, 2, 2, 4, NULL),
(4, 'Dança para Idosos', 15, 'IDOSOS', '2024-01-20', '2024-12-15', 'Terça, Quinta', '08:00:00', '10:00:00', 3, 2, 3, 5, NULL),
(5, 'Futebol Matutino', 20, 'JOVENS', '2025-01-01', '2025-12-31', 'Segunda, Quarta, Sexta', '07:00:00', '08:00:00', 3, 2, 1, 1, NULL),
(6, 'Basquete Matutino', 20, 'JOVENS', '2025-01-01', '2025-12-31', 'Segunda, Quarta, Sexta', '08:00:00', '09:00:00', 3, 2, 1, 2, NULL),
(7, 'Volêi Matutino', 20, 'JOVENS', '2025-01-01', '2025-12-31', 'Segunda, Quarta, Sexta', '09:00:00', '10:00:00', 3, 2, 1, 3, NULL),
(8, 'Futebol Vespertino', 20, 'JOVENS', '2025-01-01', '2025-12-31', 'Segunda, Quarta, Sexta', '15:00:00', '16:00:00', 3, 2, 1, 1, NULL),
(9, 'Volêi Vespertino', 20, 'JOVENS', '2025-01-01', '2025-12-31', 'Segunda, Quarta, Sexta', '16:00:00', '17:00:00', 3, 2, 1, 3, NULL),
(10, 'Capoeira Matutino', 20, 'JOVENS', '2024-01-01', '2024-12-31', 'Terca Quinta', '07:00:00', '08:00:00', 3, 2, 1, 4, NULL),
(11, 'Dança Matutino', 20, 'ADULTOS', '2024-01-01', '2024-12-31', 'Terca Quinta', '08:00:00', '09:00:00', 3, 2, 1, 5, NULL),
(12, 'Jiu-Jitsu Matutino', 20, 'JOVENS', '2025-01-01', '2025-12-31', 'Terca Quinta', '09:00:00', '10:00:00', 3, 2, 1, 6, NULL),
(13, 'Capoeira Vespertino', 20, 'JOVENS', '2025-01-01', '2025-12-31', 'Terca Quinta', '15:00:00', '16:00:00', 3, 2, 1, 4, NULL),
(14, 'Jiu-Jitsu Vespertino', 20, 'JOVENS', '2025-01-01', '2025-12-31', 'Terca Quinta', '16:00:00', '17:00:00', 3, 2, 1, 6, NULL),
(15, 'Futebol Matutino', 20, 'JOVENS', '2026-01-01', '2027-12-31', 'Segunda, Quarta, Sexta', '07:00:00', '08:00:00', 3, 2, 1, 1, NULL),
(16, 'Basquete Matutino', 20, 'JOVENS', '2026-01-01', '2027-12-31', 'Segunda, Quarta, Sexta', '08:00:00', '09:00:00', 3, 2, 1, 2, NULL),
(17, 'Volêi Matutino', 20, 'JOVENS', '2026-01-01', '2027-12-31', 'Segunda, Quarta, Sexta', '09:00:00', '10:00:00', 3, 2, 1, 3, NULL),
(18, 'Futebol Vespertino', 20, 'JOVENS', '2026-01-01', '2027-12-31', 'Segunda, Quarta, Sexta', '15:00:00', '16:00:00', 3, 2, 1, 1, NULL),
(19, 'Volêi Vespertino', 20, 'JOVENS', '2026-01-01', '2027-12-31', 'Segunda, Quarta, Sexta', '16:00:00', '17:00:00', 3, 2, 1, 3, NULL),
(20, 'Capoeira Matutino', 20, 'JOVENS', '2026-01-01', '2027-12-31', 'Terca Quinta', '07:00:00', '08:00:00', 3, 2, 1, 4, NULL),
(21, 'Dança Matutino', 20, 'ADULTOS', '2026-01-01', '2027-12-31', 'Terca Quinta', '08:00:00', '09:00:00', 3, 2, 1, 5, NULL),
(22, 'Jiu-Jitsu Matutino', 20, 'JOVENS', '2026-01-01', '2027-12-31', 'Terca Quinta', '09:00:00', '10:00:00', 3, 2, 1, 6, NULL),
(23, 'Capoeira Vespertino', 20, 'JOVENS', '2026-01-01', '2027-12-31', 'Terca Quinta', '15:00:00', '16:00:00', 3, 2, 1, 4, NULL),
(24, 'Jiu-Jitsu Vespertino', 20, 'JOVENS', '2026-01-01', '2027-12-31', 'Terca Quinta', '16:00:00', '17:00:00', 3, 2, 1, 6, NULL),
(25, 'Futebol Matutino', 20, 'JOVENS', '2025-01-01', '2025-12-31', 'Segunda, Quarta, Sexta', '07:00:00', '08:00:00', 3, 2, 1, 1, NULL),
(26, 'Basquete Matutino', 20, 'JOVENS', '2025-01-01', '2025-12-31', 'Segunda, Quarta, Sexta', '08:00:00', '09:00:00', 3, 2, 1, 2, NULL),
(27, 'Volêi Matutino', 20, 'JOVENS', '2025-01-01', '2025-12-31', 'Segunda, Quarta, Sexta', '09:00:00', '10:00:00', 3, 2, 1, 3, NULL),
(28, 'Futebol Vespertino', 20, 'JOVENS', '2025-01-01', '2025-12-31', 'Segunda, Quarta, Sexta', '15:00:00', '16:00:00', 3, 2, 1, 1, NULL),
(29, 'Volêi Vespertino', 20, 'JOVENS', '2025-01-01', '2025-12-31', 'Segunda, Quarta, Sexta', '16:00:00', '17:00:00', 3, 2, 1, 3, NULL),
(30, 'Capoeira Matutino', 20, 'JOVENS', '2024-01-01', '2024-12-31', 'Terca Quinta', '07:00:00', '08:00:00', 3, 2, 1, 4, NULL),
(31, 'Dança Matutino', 20, 'ADULTOS', '2024-01-01', '2024-12-31', 'Terca Quinta', '08:00:00', '09:00:00', 3, 2, 1, 5, NULL),
(32, 'Jiu-Jitsu Matutino', 20, 'JOVENS', '2025-01-01', '2025-12-31', 'Terca Quinta', '09:00:00', '10:00:00', 3, 2, 1, 6, NULL),
(33, 'Capoeira Vespertino', 20, 'JOVENS', '2025-01-01', '2025-12-31', 'Terca Quinta', '15:00:00', '16:00:00', 3, 2, 1, 4, NULL),
(34, 'Jiu-Jitsu Vespertino', 20, 'JOVENS', '2025-01-01', '2025-12-31', 'Terca Quinta', '16:00:00', '17:00:00', 3, 2, 1, 6, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pessoa`
--

CREATE TABLE `pessoa` (
  `pessoa_id` int(11) NOT NULL,
  `cpf` varchar(11) NOT NULL,
  `nome` varchar(225) NOT NULL,
  `nome_social` varchar(225) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `raca` enum('BRANCA','PARDA','PRETO','AMARELA','INDIGENA') DEFAULT NULL,
  `genero` enum('MASCULINO','FEMININO','OUTROS') DEFAULT NULL,
  `renda_familiar` varchar(100) DEFAULT NULL,
  `deficiencia` tinyint(1) DEFAULT NULL,
  `desc_deficiencia` varchar(100) DEFAULT NULL,
  `problema_saude` tinyint(1) DEFAULT NULL,
  `desc_problema_saude` varchar(100) DEFAULT NULL,
  `contato_id` int(11) DEFAULT NULL,
  `endereco_id` int(11) DEFAULT NULL,
  `foto_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pessoa`
--

INSERT INTO `pessoa` (`pessoa_id`, `cpf`, `nome`, `nome_social`, `data_nascimento`, `raca`, `genero`, `renda_familiar`, `deficiencia`, `desc_deficiencia`, `problema_saude`, `desc_problema_saude`, `contato_id`, `endereco_id`, `foto_id`) VALUES
(1, '12345678901', 'João da Silva', NULL, '1985-05-15', 'PARDA', 'MASCULINO', '', 0, NULL, 0, NULL, 1, 1, NULL),
(2, '98765432100', 'Maria Oliveira', 'Maria O.', '1990-08-22', 'BRANCA', 'FEMININO', '', 0, NULL, 1, 'Asma', 2, 2, NULL),
(3, '11122233344', 'Pedro Santos', NULL, '1978-03-10', 'PRETO', 'MASCULINO', '', 1, 'Deficiência visual', 0, NULL, 3, 3, NULL),
(4, '55566677788', 'Ana Paula Souza', 'Ana P.', '2000-12-01', 'AMARELA', 'FEMININO', '', 0, NULL, 0, NULL, 4, 4, NULL),
(5, '99988877766', 'Carlos Alberto Lima', 'Carlinhos', '1995-07-20', 'INDIGENA', 'MASCULINO', '', 0, NULL, 0, NULL, 5, 5, NULL),
(6, '11111111111', 'Ana Clara Silva', NULL, '2015-05-10', 'BRANCA', 'FEMININO', NULL, NULL, NULL, NULL, NULL, 1, 1, NULL),
(7, '22222222222', 'Bruno Costa', NULL, '2014-08-15', 'PARDA', 'MASCULINO', NULL, NULL, NULL, NULL, NULL, 2, 2, NULL),
(8, '33333333333', 'Carla Mendes', NULL, '2016-03-20', 'PRETO', 'FEMININO', NULL, NULL, NULL, NULL, NULL, 3, 3, NULL),
(9, '44444444444', 'Daniel Rocha', NULL, '2015-12-01', 'AMARELA', 'MASCULINO', NULL, NULL, NULL, NULL, NULL, 4, 4, NULL),
(10, '55555555555', 'Eduarda Lima', NULL, '2013-07-25', 'BRANCA', 'FEMININO', NULL, NULL, NULL, NULL, NULL, 5, 5, NULL),
(11, '66666666666', 'Felipe Andrade', NULL, '2014-11-12', 'PARDA', 'MASCULINO', NULL, NULL, NULL, NULL, NULL, 1, 1, NULL),
(12, '77777777777', 'Gabriela Nunes', NULL, '2016-02-18', 'INDIGENA', 'FEMININO', NULL, NULL, NULL, NULL, NULL, 2, 2, NULL),
(13, '88888888888', 'Henrique Martins', NULL, '2015-09-22', 'BRANCA', 'MASCULINO', NULL, NULL, NULL, NULL, NULL, 3, 3, NULL),
(14, '99999999999', 'Isabela Rocha', NULL, '2014-04-05', 'PARDA', 'FEMININO', NULL, NULL, NULL, NULL, NULL, 4, 4, NULL),
(15, '10101010101', 'João Pedro Souza', NULL, '2013-10-30', 'PRETO', 'MASCULINO', NULL, NULL, NULL, NULL, NULL, 5, 5, NULL),
(16, '11111111112', 'Larissa Ferreira', NULL, '2015-06-14', 'BRANCA', 'FEMININO', NULL, NULL, NULL, NULL, NULL, 1, 1, NULL),
(17, '12121212121', 'Mateus Oliveira', NULL, '2014-08-27', 'AMARELA', 'MASCULINO', NULL, NULL, NULL, NULL, NULL, 2, 2, NULL),
(18, '13131313131', 'Natália Cardoso', NULL, '2016-01-09', 'PARDA', 'FEMININO', NULL, NULL, NULL, NULL, NULL, 3, 3, NULL),
(19, '14141414141', 'Otávio Lima', NULL, '2015-07-19', 'BRANCA', 'MASCULINO', NULL, NULL, NULL, NULL, NULL, 4, 4, NULL),
(20, '15151515151', 'Patrícia Gomes', NULL, '2013-12-03', 'PRETO', 'FEMININO', NULL, NULL, NULL, NULL, NULL, 5, 5, NULL),
(21, '16161616161', 'Rafael Alves', NULL, '2014-03-11', 'INDIGENA', 'MASCULINO', NULL, NULL, NULL, NULL, NULL, 1, 1, NULL),
(22, '17171717171', 'Sofia Ribeiro', NULL, '2016-09-28', 'BRANCA', 'FEMININO', NULL, NULL, NULL, NULL, NULL, 2, 2, NULL),
(23, '18181818181', 'Thiago Monteiro', NULL, '2015-05-17', 'PARDA', 'MASCULINO', NULL, NULL, NULL, NULL, NULL, 3, 3, NULL),
(24, '19191919191', 'Valentina Castro', NULL, '2014-11-08', 'AMARELA', 'FEMININO', NULL, NULL, NULL, NULL, NULL, 4, 4, NULL),
(25, '20202020202', 'William Barros', NULL, '2013-04-25', 'BRANCA', 'MASCULINO', NULL, NULL, NULL, NULL, NULL, 5, 5, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `regiao`
--

CREATE TABLE `regiao` (
  `regiao_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `regiao`
--

INSERT INTO `regiao` (`regiao_id`, `nome`) VALUES
(1, 'Anhanduzinho'),
(2, 'Bandeira'),
(3, 'Centro'),
(4, 'Distrito de Área Rural'),
(5, 'Imbirussu'),
(6, 'Lagoa'),
(7, 'Prosa'),
(8, 'Segredo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `responsavel`
--

CREATE TABLE `responsavel` (
  `contato_id` int(11) NOT NULL,
  `locais_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `responsavel`
--

INSERT INTO `responsavel` (`contato_id`, `locais_id`) VALUES
(1, 1),
(2, 1),
(3, 2),
(4, 3);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `usuario_id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `senha` varchar(16) DEFAULT NULL,
  `cargo` enum('ADM','GERENTE','PROFESSOR') NOT NULL,
  `pessoa_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`usuario_id`, `email`, `senha`, `cargo`, `pessoa_id`) VALUES
(1, 'joao.silva@movimenta.com', 'senha123', 'ADM', 1),
(2, 'maria.oliveira@movimenta.com', 'senha456', 'GERENTE', 2),
(3, 'pedro.santos@movimenta.com', 'senha789', 'PROFESSOR', 3);

-- --------------------------------------------------------

--
-- Estrutura para tabela `visita`
--

CREATE TABLE `visita` (
  `visita_id` int(11) NOT NULL,
  `qtd_presentes` int(11) DEFAULT NULL,
  `data_visita` date DEFAULT NULL,
  `horario_inicio` time DEFAULT NULL,
  `horario_fim` time DEFAULT NULL,
  `observacao` text DEFAULT NULL,
  `ajustes` text DEFAULT NULL,
  `demanda_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `visita`
--

INSERT INTO `visita` (`visita_id`, `qtd_presentes`, `data_visita`, `horario_inicio`, `horario_fim`, `observacao`, `ajustes`, `demanda_id`) VALUES
(1, 25, '2024-01-20', '14:00:00', '16:00:00', 'Vestiário com problemas de iluminação', 'Necessário substituir lâmpadas', 1),
(2, 18, '2024-01-22', '09:00:00', '11:00:00', 'Equipamentos antigos e desgastados', 'Solicitar orçamento para novas bolas e redes', 2),
(3, 12, '2024-01-25', '15:00:00', '17:00:00', 'Quadra com areia compactada demais', 'Revolver a areia e adicionar mais areia', 3);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `arquivo`
--
ALTER TABLE `arquivo`
  ADD PRIMARY KEY (`arquivo_id`);

--
-- Índices de tabela `contato`
--
ALTER TABLE `contato`
  ADD PRIMARY KEY (`contato_id`);

--
-- Índices de tabela `contato_emergencia`
--
ALTER TABLE `contato_emergencia`
  ADD PRIMARY KEY (`pessoa_id`,`contato_id`),
  ADD KEY `contato_id` (`contato_id`);

--
-- Índices de tabela `demanda`
--
ALTER TABLE `demanda`
  ADD PRIMARY KEY (`demanda_id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `locais_id` (`locais_id`),
  ADD KEY `oficina_id` (`oficina_id`);

--
-- Índices de tabela `endereco`
--
ALTER TABLE `endereco`
  ADD PRIMARY KEY (`endereco_id`);

--
-- Índices de tabela `fotos_visita`
--
ALTER TABLE `fotos_visita`
  ADD PRIMARY KEY (`arquivo_id`,`visita_id`),
  ADD KEY `visita_id` (`visita_id`);

--
-- Índices de tabela `frequencia`
--
ALTER TABLE `frequencia`
  ADD PRIMARY KEY (`frequencia_id`),
  ADD UNIQUE KEY `frequencia_unica` (`oficina_id`,`aluno_id`,`frequencia_data`);

--
-- Índices de tabela `locais`
--
ALTER TABLE `locais`
  ADD PRIMARY KEY (`locais_id`),
  ADD KEY `endereco_id` (`endereco_id`),
  ADD KEY `foto_id` (`foto_id`);

--
-- Índices de tabela `matricula`
--
ALTER TABLE `matricula`
  ADD PRIMARY KEY (`oficina_id`,`pessoa_id`),
  ADD KEY `pessoa_id` (`pessoa_id`);

--
-- Índices de tabela `modalidade`
--
ALTER TABLE `modalidade`
  ADD PRIMARY KEY (`modalidade_id`);

--
-- Índices de tabela `modalidade_interesse`
--
ALTER TABLE `modalidade_interesse`
  ADD PRIMARY KEY (`locais_id`,`modalidade_id`),
  ADD KEY `modalidade_id` (`modalidade_id`);

--
-- Índices de tabela `noticia`
--
ALTER TABLE `noticia`
  ADD PRIMARY KEY (`noticia_id`),
  ADD KEY `locais_id` (`locais_id`),
  ADD KEY `oficina_id` (`oficina_id`),
  ADD KEY `imagem` (`imagem`);

--
-- Índices de tabela `oficina`
--
ALTER TABLE `oficina`
  ADD PRIMARY KEY (`oficina_id`),
  ADD KEY `professor` (`professor`),
  ADD KEY `coordenador` (`coordenador`),
  ADD KEY `locais_id` (`locais_id`),
  ADD KEY `modalidade_id` (`modalidade_id`),
  ADD KEY `foto_id` (`foto_id`);

--
-- Índices de tabela `pessoa`
--
ALTER TABLE `pessoa`
  ADD PRIMARY KEY (`pessoa_id`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD KEY `contato_id` (`contato_id`),
  ADD KEY `endereco_id` (`endereco_id`),
  ADD KEY `foto_id` (`foto_id`);

--
-- Índices de tabela `regiao`
--
ALTER TABLE `regiao`
  ADD PRIMARY KEY (`regiao_id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `responsavel`
--
ALTER TABLE `responsavel`
  ADD PRIMARY KEY (`contato_id`,`locais_id`),
  ADD KEY `locais_id` (`locais_id`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`usuario_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `pessoa_id` (`pessoa_id`);

--
-- Índices de tabela `visita`
--
ALTER TABLE `visita`
  ADD PRIMARY KEY (`visita_id`),
  ADD KEY `demanda_id` (`demanda_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `arquivo`
--
ALTER TABLE `arquivo`
  MODIFY `arquivo_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `contato`
--
ALTER TABLE `contato`
  MODIFY `contato_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `demanda`
--
ALTER TABLE `demanda`
  MODIFY `demanda_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `endereco`
--
ALTER TABLE `endereco`
  MODIFY `endereco_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `frequencia`
--
ALTER TABLE `frequencia`
  MODIFY `frequencia_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `locais`
--
ALTER TABLE `locais`
  MODIFY `locais_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `modalidade`
--
ALTER TABLE `modalidade`
  MODIFY `modalidade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `noticia`
--
ALTER TABLE `noticia`
  MODIFY `noticia_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `oficina`
--
ALTER TABLE `oficina`
  MODIFY `oficina_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de tabela `pessoa`
--
ALTER TABLE `pessoa`
  MODIFY `pessoa_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de tabela `regiao`
--
ALTER TABLE `regiao`
  MODIFY `regiao_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `usuario_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `visita`
--
ALTER TABLE `visita`
  MODIFY `visita_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `contato_emergencia`
--
ALTER TABLE `contato_emergencia`
  ADD CONSTRAINT `contato_emergencia_ibfk_1` FOREIGN KEY (`pessoa_id`) REFERENCES `pessoa` (`pessoa_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contato_emergencia_ibfk_2` FOREIGN KEY (`contato_id`) REFERENCES `contato` (`contato_id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `demanda`
--
ALTER TABLE `demanda`
  ADD CONSTRAINT `demanda_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`),
  ADD CONSTRAINT `demanda_ibfk_2` FOREIGN KEY (`locais_id`) REFERENCES `locais` (`locais_id`),
  ADD CONSTRAINT `demanda_ibfk_3` FOREIGN KEY (`oficina_id`) REFERENCES `oficina` (`oficina_id`);

--
-- Restrições para tabelas `fotos_visita`
--
ALTER TABLE `fotos_visita`
  ADD CONSTRAINT `fotos_visita_ibfk_1` FOREIGN KEY (`arquivo_id`) REFERENCES `arquivo` (`arquivo_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fotos_visita_ibfk_2` FOREIGN KEY (`visita_id`) REFERENCES `visita` (`visita_id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `frequencia`
--
ALTER TABLE `frequencia`
  ADD CONSTRAINT `frequencia_ibfk_1` FOREIGN KEY (`oficina_id`,`aluno_id`) REFERENCES `matricula` (`oficina_id`, `pessoa_id`);

--
-- Restrições para tabelas `locais`
--
ALTER TABLE `locais`
  ADD CONSTRAINT `locais_ibfk_1` FOREIGN KEY (`endereco_id`) REFERENCES `endereco` (`endereco_id`),
  ADD CONSTRAINT `locais_ibfk_2` FOREIGN KEY (`foto_id`) REFERENCES `arquivo` (`arquivo_id`);

--
-- Restrições para tabelas `matricula`
--
ALTER TABLE `matricula`
  ADD CONSTRAINT `matricula_ibfk_1` FOREIGN KEY (`oficina_id`) REFERENCES `oficina` (`oficina_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `matricula_ibfk_2` FOREIGN KEY (`pessoa_id`) REFERENCES `pessoa` (`pessoa_id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `modalidade_interesse`
--
ALTER TABLE `modalidade_interesse`
  ADD CONSTRAINT `modalidade_interesse_ibfk_1` FOREIGN KEY (`locais_id`) REFERENCES `locais` (`locais_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `modalidade_interesse_ibfk_2` FOREIGN KEY (`modalidade_id`) REFERENCES `modalidade` (`modalidade_id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `noticia`
--
ALTER TABLE `noticia`
  ADD CONSTRAINT `noticia_ibfk_1` FOREIGN KEY (`locais_id`) REFERENCES `locais` (`locais_id`),
  ADD CONSTRAINT `noticia_ibfk_2` FOREIGN KEY (`oficina_id`) REFERENCES `oficina` (`oficina_id`),
  ADD CONSTRAINT `noticia_ibfk_3` FOREIGN KEY (`imagem`) REFERENCES `arquivo` (`arquivo_id`);

--
-- Restrições para tabelas `oficina`
--
ALTER TABLE `oficina`
  ADD CONSTRAINT `oficina_ibfk_1` FOREIGN KEY (`professor`) REFERENCES `pessoa` (`pessoa_id`),
  ADD CONSTRAINT `oficina_ibfk_2` FOREIGN KEY (`coordenador`) REFERENCES `pessoa` (`pessoa_id`),
  ADD CONSTRAINT `oficina_ibfk_3` FOREIGN KEY (`locais_id`) REFERENCES `locais` (`locais_id`),
  ADD CONSTRAINT `oficina_ibfk_4` FOREIGN KEY (`modalidade_id`) REFERENCES `modalidade` (`modalidade_id`),
  ADD CONSTRAINT `oficina_ibfk_5` FOREIGN KEY (`foto_id`) REFERENCES `arquivo` (`arquivo_id`);

--
-- Restrições para tabelas `pessoa`
--
ALTER TABLE `pessoa`
  ADD CONSTRAINT `pessoa_ibfk_1` FOREIGN KEY (`contato_id`) REFERENCES `contato` (`contato_id`),
  ADD CONSTRAINT `pessoa_ibfk_2` FOREIGN KEY (`endereco_id`) REFERENCES `endereco` (`endereco_id`),
  ADD CONSTRAINT `pessoa_ibfk_3` FOREIGN KEY (`foto_id`) REFERENCES `arquivo` (`arquivo_id`);

--
-- Restrições para tabelas `responsavel`
--
ALTER TABLE `responsavel`
  ADD CONSTRAINT `responsavel_ibfk_1` FOREIGN KEY (`contato_id`) REFERENCES `contato` (`contato_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `responsavel_ibfk_2` FOREIGN KEY (`locais_id`) REFERENCES `locais` (`locais_id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`pessoa_id`) REFERENCES `pessoa` (`pessoa_id`);

--
-- Restrições para tabelas `visita`
--
ALTER TABLE `visita`
  ADD CONSTRAINT `visita_ibfk_1` FOREIGN KEY (`demanda_id`) REFERENCES `demanda` (`demanda_id`);
--
-- Banco de dados: `phpmyadmin`
--
CREATE DATABASE IF NOT EXISTS `phpmyadmin` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `phpmyadmin`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__bookmark`
--

CREATE TABLE `pma__bookmark` (
  `id` int(10) UNSIGNED NOT NULL,
  `dbase` varchar(255) NOT NULL DEFAULT '',
  `user` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `query` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Bookmarks';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__central_columns`
--

CREATE TABLE `pma__central_columns` (
  `db_name` varchar(64) NOT NULL,
  `col_name` varchar(64) NOT NULL,
  `col_type` varchar(64) NOT NULL,
  `col_length` text DEFAULT NULL,
  `col_collation` varchar(64) NOT NULL,
  `col_isNull` tinyint(1) NOT NULL,
  `col_extra` varchar(255) DEFAULT '',
  `col_default` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Central list of columns';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__column_info`
--

CREATE TABLE `pma__column_info` (
  `id` int(5) UNSIGNED NOT NULL,
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `column_name` varchar(64) NOT NULL DEFAULT '',
  `comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `mimetype` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `transformation` varchar(255) NOT NULL DEFAULT '',
  `transformation_options` varchar(255) NOT NULL DEFAULT '',
  `input_transformation` varchar(255) NOT NULL DEFAULT '',
  `input_transformation_options` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Column information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__designer_settings`
--

CREATE TABLE `pma__designer_settings` (
  `username` varchar(64) NOT NULL,
  `settings_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Settings related to Designer';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__export_templates`
--

CREATE TABLE `pma__export_templates` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `export_type` varchar(10) NOT NULL,
  `template_name` varchar(64) NOT NULL,
  `template_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved export templates';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__favorite`
--

CREATE TABLE `pma__favorite` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Favorite tables';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__history`
--

CREATE TABLE `pma__history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db` varchar(64) NOT NULL DEFAULT '',
  `table` varchar(64) NOT NULL DEFAULT '',
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp(),
  `sqlquery` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='SQL history for phpMyAdmin';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__navigationhiding`
--

CREATE TABLE `pma__navigationhiding` (
  `username` varchar(64) NOT NULL,
  `item_name` varchar(64) NOT NULL,
  `item_type` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Hidden items of navigation tree';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__pdf_pages`
--

CREATE TABLE `pma__pdf_pages` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `page_nr` int(10) UNSIGNED NOT NULL,
  `page_descr` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='PDF relation pages for phpMyAdmin';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__recent`
--

CREATE TABLE `pma__recent` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Recently accessed tables';

--
-- Despejando dados para a tabela `pma__recent`
--

INSERT INTO `pma__recent` (`username`, `tables`) VALUES
('root', '[{\"db\":\"cyber_report\",\"table\":\"usuario\"},{\"db\":\"cyber_report\",\"table\":\"checklist\"},{\"db\":\"cyber_report\",\"table\":\"checklist_item\"}]');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__relation`
--

CREATE TABLE `pma__relation` (
  `master_db` varchar(64) NOT NULL DEFAULT '',
  `master_table` varchar(64) NOT NULL DEFAULT '',
  `master_field` varchar(64) NOT NULL DEFAULT '',
  `foreign_db` varchar(64) NOT NULL DEFAULT '',
  `foreign_table` varchar(64) NOT NULL DEFAULT '',
  `foreign_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Relation table';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__savedsearches`
--

CREATE TABLE `pma__savedsearches` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `search_name` varchar(64) NOT NULL DEFAULT '',
  `search_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved searches';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__table_coords`
--

CREATE TABLE `pma__table_coords` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `pdf_page_number` int(11) NOT NULL DEFAULT 0,
  `x` float UNSIGNED NOT NULL DEFAULT 0,
  `y` float UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table coordinates for phpMyAdmin PDF output';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__table_info`
--

CREATE TABLE `pma__table_info` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `display_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__table_uiprefs`
--

CREATE TABLE `pma__table_uiprefs` (
  `username` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `prefs` text NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tables'' UI preferences';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__tracking`
--

CREATE TABLE `pma__tracking` (
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `version` int(10) UNSIGNED NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `schema_snapshot` text NOT NULL,
  `schema_sql` text DEFAULT NULL,
  `data_sql` longtext DEFAULT NULL,
  `tracking` set('UPDATE','REPLACE','INSERT','DELETE','TRUNCATE','CREATE DATABASE','ALTER DATABASE','DROP DATABASE','CREATE TABLE','ALTER TABLE','RENAME TABLE','DROP TABLE','CREATE INDEX','DROP INDEX','CREATE VIEW','ALTER VIEW','DROP VIEW') DEFAULT NULL,
  `tracking_active` int(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Database changes tracking for phpMyAdmin';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__userconfig`
--

CREATE TABLE `pma__userconfig` (
  `username` varchar(64) NOT NULL,
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `config_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User preferences storage for phpMyAdmin';

--
-- Despejando dados para a tabela `pma__userconfig`
--

INSERT INTO `pma__userconfig` (`username`, `timevalue`, `config_data`) VALUES
('root', '2026-07-17 13:05:12', '{\"Console\\/Mode\":\"collapse\",\"lang\":\"pt_BR\",\"NavigationWidth\":0}');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__usergroups`
--

CREATE TABLE `pma__usergroups` (
  `usergroup` varchar(64) NOT NULL,
  `tab` varchar(64) NOT NULL,
  `allowed` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User groups with configured menu items';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__users`
--

CREATE TABLE `pma__users` (
  `username` varchar(64) NOT NULL,
  `usergroup` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Users and their assignments to user groups';

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pma__central_columns`
--
ALTER TABLE `pma__central_columns`
  ADD PRIMARY KEY (`db_name`,`col_name`);

--
-- Índices de tabela `pma__column_info`
--
ALTER TABLE `pma__column_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `db_name` (`db_name`,`table_name`,`column_name`);

--
-- Índices de tabela `pma__designer_settings`
--
ALTER TABLE `pma__designer_settings`
  ADD PRIMARY KEY (`username`);

--
-- Índices de tabela `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_user_type_template` (`username`,`export_type`,`template_name`);

--
-- Índices de tabela `pma__favorite`
--
ALTER TABLE `pma__favorite`
  ADD PRIMARY KEY (`username`);

--
-- Índices de tabela `pma__history`
--
ALTER TABLE `pma__history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`db`,`table`,`timevalue`);

--
-- Índices de tabela `pma__navigationhiding`
--
ALTER TABLE `pma__navigationhiding`
  ADD PRIMARY KEY (`username`,`item_name`,`item_type`,`db_name`,`table_name`);

--
-- Índices de tabela `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  ADD PRIMARY KEY (`page_nr`),
  ADD KEY `db_name` (`db_name`);

--
-- Índices de tabela `pma__recent`
--
ALTER TABLE `pma__recent`
  ADD PRIMARY KEY (`username`);

--
-- Índices de tabela `pma__relation`
--
ALTER TABLE `pma__relation`
  ADD PRIMARY KEY (`master_db`,`master_table`,`master_field`),
  ADD KEY `foreign_field` (`foreign_db`,`foreign_table`);

--
-- Índices de tabela `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_savedsearches_username_dbname` (`username`,`db_name`,`search_name`);

--
-- Índices de tabela `pma__table_coords`
--
ALTER TABLE `pma__table_coords`
  ADD PRIMARY KEY (`db_name`,`table_name`,`pdf_page_number`);

--
-- Índices de tabela `pma__table_info`
--
ALTER TABLE `pma__table_info`
  ADD PRIMARY KEY (`db_name`,`table_name`);

--
-- Índices de tabela `pma__table_uiprefs`
--
ALTER TABLE `pma__table_uiprefs`
  ADD PRIMARY KEY (`username`,`db_name`,`table_name`);

--
-- Índices de tabela `pma__tracking`
--
ALTER TABLE `pma__tracking`
  ADD PRIMARY KEY (`db_name`,`table_name`,`version`);

--
-- Índices de tabela `pma__userconfig`
--
ALTER TABLE `pma__userconfig`
  ADD PRIMARY KEY (`username`);

--
-- Índices de tabela `pma__usergroups`
--
ALTER TABLE `pma__usergroups`
  ADD PRIMARY KEY (`usergroup`,`tab`,`allowed`);

--
-- Índices de tabela `pma__users`
--
ALTER TABLE `pma__users`
  ADD PRIMARY KEY (`username`,`usergroup`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pma__column_info`
--
ALTER TABLE `pma__column_info`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pma__history`
--
ALTER TABLE `pma__history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  MODIFY `page_nr` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Banco de dados: `test`
--
CREATE DATABASE IF NOT EXISTS `test` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `test`;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
