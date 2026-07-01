USE cyber_report;

-- ---------------------------------------------------------
-- perfil_acesso
-- ---------------------------------------------------------
INSERT INTO perfil_acesso (nome, habilitado) VALUES
('Administrador', 1),
('Pentester', 1),
('Cliente', 1);
-- ids gerados: 1, 2, 3

-- ---------------------------------------------------------
-- permissao
-- ---------------------------------------------------------
INSERT INTO permissao (codigo, habilitado) VALUES
('usuario.criar', 1),
('usuario.editar', 1),
('usuario.excluir', 1),
('projeto.criar', 1),
('projeto.editar', 1),
('projeto.visualizar', 1),
('vulnerabilidade.criar', 1),
('vulnerabilidade.editar', 1),
('relatorio.exportar', 1),
('checklist.gerenciar', 1);
-- ids gerados: 1 a 10

-- ---------------------------------------------------------
-- perfil_permissao
-- ---------------------------------------------------------
-- Administrador (1): acesso total
INSERT INTO perfil_permissao (perfil_id, permissao_id) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5),
(1, 6), (1, 7), (1, 8), (1, 9), (1, 10),
-- Pentester (2): operação de projetos e vulnerabilidades
(2, 6), (2, 7), (2, 8), (2, 9),
-- Cliente (3): apenas visualização e exportação
(3, 6), (3, 9);

-- ---------------------------------------------------------
-- usuario
-- ---------------------------------------------------------
INSERT INTO usuario (perfil_id, nome, cpf, email, senha, telefone, cargo, especialidade, habilitado, ultimo_login) VALUES
(1, 'Admin', '00011122233', 'admin@senac.com.br', '1234', '(67) 99876-5432', 'Administrador de Sistema', NULL, 1, '2026-06-25 09:14:00'),
(2, 'Mariana Souza', '11122233344', 'mariana.souza@cyberreport.com.br', '$2y$10$abcd1234efgh5678ijklmn', '(67) 99123-4567', 'Pentester Sênior', 'Aplicações Web', 1, '2026-06-24 17:32:00'),
(2, 'Rafael Lima', '22233344455', 'rafael.lima@cyberreport.com.br', '$2y$10$opqr9876stuv5432wxyz12', '(67) 99234-5678', 'Pentester Pleno', 'Infraestrutura de Redes', 1, '2026-06-23 14:05:00'),
(2, 'Bianca Carvalho', '33344455566', 'bianca.carvalho@cyberreport.com.br', '$2y$10$lmno3456pqrs7890tuvw34', '(67) 99345-6789', 'Pentester Júnior', 'Mobile', 1, '2026-06-22 10:48:00'),
(3, 'Carlos Mendes', '44455566677', 'carlos.mendes@empresacliente.com.br', '$2y$10$wxyz5678abcd1234efgh90', '(67) 99456-7890', 'Gerente de TI', NULL, 1, '2026-06-20 08:22:00');
-- ids gerados: 1 a 5

-- ---------------------------------------------------------
-- endereco
-- ---------------------------------------------------------
INSERT INTO endereco (cep, rua, numero, complemento, bairro, cidade, estado, pais) VALUES
('79002-200', 'Avenida Afonso Pena', '4000', 'Sala 12', 'Centro', 'Campo Grande', 'Mato Grosso do Sul', 'Brasil'),
('79117-900', 'Rua Pedro Celestino', '850', NULL, 'Vila Carvalho', 'Campo Grande', 'Mato Grosso do Sul', 'Brasil'),
('01310-100', 'Avenida Paulista', '1578', 'Conjunto 304', 'Bela Vista', 'São Paulo', 'São Paulo', 'Brasil');
-- ids gerados: 1 a 3

-- ---------------------------------------------------------
-- empresa
-- ---------------------------------------------------------
INSERT INTO empresa (endereco_id, responsavel_id, nome_fantasia, razao_social, cnpj, email_contato, telefone, responsavel, habilitado) VALUES
(1, 5, 'TechPantanal Soluções', 'TechPantanal Soluções em Tecnologia LTDA', '12.345.678/0001-90', 'contato@techpantanal.com.br', '(67) 3321-4455', 'Carlos Mendes', 1),
(3, 5, 'Finasul Bank', 'Finasul Instituição Financeira S.A.', '98.765.432/0001-10', 'seguranca@finasul.com.br', '(11) 3987-6655', 'Carlos Mendes', 1);
-- ids gerados: 1 a 2

-- ---------------------------------------------------------
-- checklist
-- ---------------------------------------------------------
INSERT INTO checklist (nome, descricao, categoria, habilitado) VALUES
('OWASP Top 10 - Aplicação Web', 'Checklist baseado no OWASP Top 10 para avaliação de segurança em aplicações web.', 'Web Application', 1),
('Checklist de Rede Interna', 'Checklist para avaliação de segurança em infraestrutura de rede corporativa interna.', 'Rede Interna', 1);
-- ids gerados: 1 a 2

-- ---------------------------------------------------------
-- checklist_item
-- ---------------------------------------------------------
INSERT INTO checklist_item (checklist_id, titulo, referencia, obrigatorio, habilitado) VALUES
(1, 'Quebra de Controle de Acesso', 'OWASP A01:2021', 1, 1),
(1, 'Falhas Criptográficas', 'OWASP A02:2021', 1, 1),
(1, 'Injeção (SQL, Command, etc)', 'OWASP A03:2021', 1, 1),
(1, 'Design Inseguro', 'OWASP A04:2021', 0, 1),
(2, 'Varredura de Portas e Serviços', 'NIST SP 800-115', 1, 1),
(2, 'Análise de Segmentação de Rede', 'NIST SP 800-115', 1, 1);
-- ids gerados: 1 a 6

-- ---------------------------------------------------------
-- modelo_pentest
-- ---------------------------------------------------------
INSERT INTO modelo_pentest (checklist_id, nome, categoria, metodologia, framework, nv_risco, descricao, habilitado) VALUES
(1, 'Pentest Aplicação Web Externa', 'Web Application', 'PTES', 'OWASP', 'ALTO', 'Modelo padrão para testes de intrusão em aplicações web expostas à internet.', 1),
(2, 'Pentest Infraestrutura Interna', 'Rede Interna', 'OSSTMM', 'NIST', 'MEDIO', 'Modelo para avaliação de segurança em redes corporativas internas.', 1);
-- ids gerados: 1 a 2

-- ---------------------------------------------------------
-- projeto
-- ---------------------------------------------------------
INSERT INTO projeto (empresa_id, modelo_pentest_id, nome, data_inicio, data_fim_prevista, data_fim_real, horas_contratadas, horas_executadas, tipo, nivel_sigilo, escopo, alvo, contrato, restricao, habilitado) VALUES
(1, 1, 'Pentest Portal Institucional TechPantanal', '2026-05-04', '2026-05-18', '2026-05-17', 80.00, 76.50, 'GRAY BOX', 'EXTERNO', 'Avaliação de segurança do portal institucional e API de autenticação.', 'portal.techpantanal.com.br, api.techpantanal.com.br', 'CONTR-2026-0041', 'Não realizar testes de negação de serviço (DoS).', 1),
(2, 2, 'Pentest Rede Interna Finasul - Agência São Paulo', '2026-06-01', '2026-06-20', NULL, 120.00, 64.00, 'BLACK BOX', 'INTERNO', 'Avaliação de segurança da infraestrutura de rede interna, incluindo servidores e estações de trabalho.', '10.20.0.0/22', 'CONTR-2026-0058', 'Testes restritos ao horário comercial, das 08h às 18h.', 1);
-- ids gerados: 1 a 2

-- ---------------------------------------------------------
-- projeto_usuario
-- ---------------------------------------------------------
INSERT INTO projeto_usuario (projeto_id, usuario_id, papel, habilitado) VALUES
(1, 2, 'LIDER', 1),
(1, 4, 'ESPECIALISTA', 1),
(1, 1, 'GESTOR', 1),
(2, 3, 'LIDER', 1),
(2, 2, 'ESPECIALISTA', 1),
(2, 1, 'GESTOR', 1);

-- ---------------------------------------------------------
-- cronometro_registro
-- ---------------------------------------------------------
INSERT INTO cronometro_registro (projeto_id, ip_analista, ip_alvo, inicio, fim, pausa, duracao_minutos, habilitado) VALUES
(1, '192.168.10.15', '200.150.30.10', '2026-05-05 09:00:00', '2026-05-05 12:30:00', NULL, 210, 1),
(1, '192.168.10.16', '200.150.30.11', '2026-05-06 13:00:00', '2026-05-06 17:00:00', '2026-05-06 15:00:00', 210, 1),
(2, '10.0.5.20', '10.20.1.5', '2026-06-02 08:30:00', '2026-06-02 11:45:00', NULL, 195, 1);
-- ids gerados: 1 a 3

-- ---------------------------------------------------------
-- cronometro_log
-- ATENÇÃO: nesta tabela ip_analista/ip_alvo estão como INT, enquanto em
-- cronometro_registro são VARCHAR(45). Isso impede uma FK real entre as colunas
-- (mesmo que fosse descomentada) e provavelmente é um erro no schema original.
-- Aqui usei valores INT (ids fictícios) apenas para respeitar o tipo declarado.
-- ---------------------------------------------------------
INSERT INTO cronometro_log (cronometro_id, usuario_id, ip_analista, ip_alvo, inicio, fim) VALUES
(1, 2, 1, 1, '2026-05-05 09:00:00', '2026-05-05 12:30:00'),
(2, 4, 2, 2, '2026-05-06 13:00:00', '2026-05-06 17:00:00'),
(3, 3, 3, 3, '2026-06-02 08:30:00', '2026-06-02 11:45:00');
-- ids gerados: 1 a 3

-- ---------------------------------------------------------
-- vulnerabilidade
-- ---------------------------------------------------------
INSERT INTO vulnerabilidade (projeto_id, nome, cvss, cve, descricao, descricao_tecnica, categoria, severidade_vulnerabilidade, habilitado, impacto_negocio) VALUES
(1, 'Injeção SQL no formulário de login', 9.1, NULL, 'Parâmetro "usuario" vulnerável a SQL Injection.', 'O parâmetro "usuario" do endpoint /api/login não realiza sanitização adequada, permitindo a injeção de comandos SQL arbitrários. Foi possível extrair hashes de senha da tabela de usuários através de payloads booleanos.', 'Injeção', 'CRITICA', 1, 'Possível vazamento total da base de usuários e comprometimento de contas administrativas.'),
(1, 'Cross-Site Scripting (XSS) Refletido', 6.1, NULL, 'Campo de busca reflete entrada do usuário sem sanitização.', 'O parâmetro "q" do endpoint /busca reflete o valor informado diretamente no HTML de resposta, permitindo a execução de scripts arbitrários no navegador da vítima.', 'Cross-Site Scripting', 'MEDIA', 1, 'Roubo de sessão de usuários autenticados e possível desfiguração de páginas.'),
(2, 'Compartilhamento SMB sem autenticação', 7.5, NULL, 'Compartilhamento de rede acessível sem credenciais.', 'O servidor de arquivos 10.20.1.5 expõe um compartilhamento SMB ("Financeiro") acessível anonimamente, contendo planilhas com dados sensíveis de clientes.', 'Configuração Insegura', 'ALTA', 1, 'Exposição de dados financeiros sensíveis de clientes a qualquer usuário da rede interna.'),
(2, 'Credenciais padrão em switch de rede', 5.3, NULL, 'Dispositivo de rede com credenciais padrão de fábrica.', 'O switch core (10.20.0.1) aceita login com as credenciais padrão de fábrica, permitindo acesso à configuração de VLANs e portas.', 'Configuração Insegura', 'MEDIA', 1, 'Possibilidade de reconfiguração maliciosa da rede, levando a interceptação de tráfego ou indisponibilidade.');
-- ids gerados: 1 a 4

-- ---------------------------------------------------------
-- evidencia
-- ---------------------------------------------------------
INSERT INTO evidencia (vulnerabilidade_id, nome_arquivo, caminho_arquivo, tipo_arquivo, descricao) VALUES
(1, 'sqli_login_burpsuite.png', '/evidencias/projeto1/sqli_login_burpsuite.png', 'image/png', 'Captura de tela do Burp Suite demonstrando a injeção SQL no campo "usuario".'),
(1, 'dump_tabela_usuarios.txt', '/evidencias/projeto1/dump_tabela_usuarios.txt', 'text/plain', 'Saída do sqlmap com o dump parcial da tabela de usuários.'),
(3, 'smb_acesso_anonimo.png', '/evidencias/projeto2/smb_acesso_anonimo.png', 'image/png', 'Captura de tela do acesso anônimo ao compartilhamento "Financeiro" via smbclient.');
-- ids gerados: 1 a 3

-- ---------------------------------------------------------
-- banco_conhecimento
-- ---------------------------------------------------------
INSERT INTO banco_conhecimento (vulnerabilidade_id, titulo, categoria, conteudo, tecnica_utilizada, solucao_recomendada, habilitado) VALUES
(1, 'Mitigação de Injeção SQL em Formulários de Autenticação', 'Injeção', 'Vulnerabilidades de SQL Injection em formulários de login são recorrentes em aplicações que constroem queries via concatenação de strings.', 'Testes manuais com payloads booleanos e automação com sqlmap.', 'Utilizar consultas parametrizadas (prepared statements) e validar/normalizar toda entrada do usuário no backend.', 1),
(3, 'Riscos de Compartilhamentos de Rede sem Autenticação', 'Configuração Insegura', 'Compartilhamentos SMB configurados para acesso anônimo são um vetor comum de exfiltração de dados em redes corporativas.', 'Enumeração de compartilhamentos com smbclient e crackmapexec.', 'Desabilitar acesso anônimo (guest) no servidor SMB e aplicar controle de acesso baseado em grupos.', 1);
-- ids gerados: 1 a 2

-- ---------------------------------------------------------
-- Tipos de Pentest
-- ---------------------------------------------------------

INSERT INTO pentest_tipos (nome, descricao_breve, descricao_completa, categoria, modelo, tecnica, frameworks, checklist, nivel_profundidade, horas_execucao, habilitado) VALUES
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
    'ENG. SOCIAL', 'Gray Box', 'Manual', 'MITRE ATT&CK', 'NIST SP 800-115', 'Standard Security Assessment', 24, 0
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
    'WEB', 'Gray Box', 'Híbrida', 'NIST,PTES', 'CIS Benchmarks', 'Standard Security Assessment', 48, 0
),
(
    'API Security Testing',
    'Testes de segurança em APIs REST e GraphQL cobrindo o OWASP API Security Top 10.',
    'Análise de segurança focada em interfaces de programação de aplicações, verificando autenticação, autorização em nível de objeto e função, exposição excessiva de dados, limitação de taxa e injeções via endpoints.',
    'WEB', 'Black Box', 'Automatizada', 'OWASP,PTES', 'OWASP API Top 10', 'Deep Security Assessment', 20, 1
),
(
    'IoT Security Assessment',
    'Avaliação de segurança em dispositivos IoT, firmware e protocolos de comunicação embarcados.',
    'Análise de firmware, interfaces de hardware, protocolos de comunicação (MQTT, CoAP, Zigbee) e superfícies de ataque em dispositivos IoT industriais e residenciais.',
    'WEB', 'Gray Box', 'Manual', 'OWASP,NIST', 'OWASP IoT Top 10', 'Deep Security Assessment', 56, 1
),
(
    'Active Directory Pentest',
    'Teste de penetração focado em ambientes Active Directory, incluindo escalada de privilégios e movimentação lateral.',
    'Simulação de ataques internos em ambientes Windows Active Directory cobrindo Kerberoasting, Pass-the-Hash, DCSync, abuso de ACLs e caminhos de escalada de domínio.',
    'RED TEAMS', 'White Box', 'Híbrida', 'MITRE ATT&CK,PTES', 'CIS Benchmarks', 'Advanced Red Team', 80, 1
),
(
    'Phishing Simulation',
    'Simulação de campanhas de phishing direcionado para medir o índice de conscientização dos colaboradores.',
    'Execução de campanhas de spear phishing personalizadas por setor, com rastreamento de cliques, coleta de credenciais em ambiente controlado e relatório de exposição por departamento.',
    'ENG. SOCIAL', 'Black Box', 'Automatizada', 'MITRE ATT&CK', 'NIST SP 800-115', 'Basic Security Assessment', 16, 1
),
(
    'Container & Kubernetes Security Review',
    'Revisão de segurança em ambientes containerizados Docker e orquestração Kubernetes.',
    'Auditoria de configurações de containers, imagens Docker, políticas de rede Kubernetes, RBAC, secrets management e exposição de APIs do cluster contra benchmarks CIS.',
    'CLOUD', 'White Box', 'Automatizada', 'CIS,NIST,MITRE ATT&CK', 'CIS Benchmarks', 'Standard Security Assessment', 36, 0
),
(
    'Thick Client Application Pentest',
    'Teste de penetração em aplicações desktop (thick client), análise de comunicação e armazenamento local.',
    'Avaliação de segurança em aplicações desktop cobrindo análise de tráfego entre cliente e servidor, armazenamento inseguro de dados locais, bypass de controles client-side e engenharia reversa de binários.',
    'WEB', 'Gray Box', 'Manual', 'OWASP,PTES', 'OWASP', 'Deep Security Assessment', 44, 1
);
