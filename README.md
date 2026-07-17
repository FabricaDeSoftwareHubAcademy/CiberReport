# CiberReport

Um sistema web para geração/registro de relatórios relacionados a cibersegurança. Fornece formulários e visualizações para criar, consultar e gerir incidentes, avaliações e evidências relacionadas à segurança.

## Sumário
- [Sobre](#sobre)
- [Funcionalidades](#funcionalidades)
- [Tecnologias](#tecnologias)
- [Requisitos](#requisitos)
- [Instalação](#instalação)

## Sobre
CiberReport é um projeto voltado a facilitar a criação e o gerenciamento de relatórios de segurança (incidentes, auditorias, checklists). O foco é em simplicidade de uso, armazenamento organizado das evidências e exportação de relatórios para análises posteriores.

## Funcionalidades
- Cadastro de relatórios/incidentes com categorias e níveis de severidade.
- Upload e gerenciamento de evidências (arquivos, imagens).
- Visualização e busca de relatórios por filtros (data, severidade, categoria).
- Exportação de relatórios (PDF/CSV) — dependendo da implementação.
- Painel administrativo para gerir usuários e permissões (se aplicável).

## Tecnologias
Base do projeto (linguagens principais):
- PHP (servidor/back-end)
- CSS
- HTML
- JavaScript

(Detalhes de frameworks ou bibliotecas específicos devem ser acrescentados conforme o projeto: ex. Laravel, Slim, Bootstrap, Tailwind, jQuery, Vue/React, etc.)

## Requisitos
- PHP 7.4+ ou PHP 8.x (confirme a versão suportada pelo projeto)
- Servidor web (Apache/Nginx) ou PHP built-in server para desenvolvimento
- Banco de dados: MySQL / MariaDB / PostgreSQL (conforme configuração do projeto)

## Instalação (local)
1. Clone o repositório:
   git clone https://github.com/FabricaDeSoftwareHubAcademy/CiberReport.git
2. Entre na pasta do projeto:
   cd CiberReport
3. Configure variáveis de ambiente:
   - Copie um arquivo de exemplo `.env.example` para `.env` (ou edite `config.php` conforme o padrão do projeto).
   - Ajuste as credenciais do banco de dados e outras chaves.
4. Crie o banco de dados e execute migrations/import SQL.

## Executando em desenvolvimento
- Usando servidor PHP embutido:
  php -S localhost:8000 -t public
- Ou configure um virtual host no Apache/Nginx apontando para a pasta `public/` (ou raiz do projeto, dependendo da estrutura).

Acesse: http://localhost:8000 (ou a URL configurada)
