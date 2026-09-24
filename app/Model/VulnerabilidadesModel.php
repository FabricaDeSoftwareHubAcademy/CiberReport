<?php
    Class Vulnerabilidades
    {
        private $pdo;
 
        public $msgErro = "";
 
 
        const MAX_NOME = 80;
        const MAX_CVE = 30;
        const MAX_DESCRICAO = 100;
        const MAX_DESCRICAO_TECNICA = 150;
        const MAX_IMPACTO = 100;
        const MAX_RESPONSAVEL = 100;
 
        const CATEGORIAS_VALIDAS = ['API', 'Aplicação Web', 'Infraestrutura', 'Mobile', 'Rede'];
        const SEVERIDADES_VALIDAS = ['ALTA', 'BAIXA', 'CRITICA', 'MEDIA'];
        const STATUS_VALIDOS = ['ABERTA', 'ACEITA', 'CORRIGIDA', 'EM_ANALISE', 'FALSO_POSITIVO'];
 
        public function __construct($pdo)
        {
            $this->pdo = $pdo;
        }
 
 
        private function validarDados($nome, $cvss, $cve, $descricao, $descricao_tecnica, $categoria, $severidade_vulnerabilidade, $impacto_negocio, $responsavel, $status)
        {
 
            $nome = trim($nome);
            if (mb_strlen($nome, 'UTF-8') < 1 || mb_strlen($nome, 'UTF-8') > self::MAX_NOME) {
                $this->msgErro = "Nome inválido (1 a " . self::MAX_NOME . " caracteres).";
                return false;
            }
 
 
            if (!is_numeric($cvss) || $cvss < 0 || $cvss > 10) {
                $this->msgErro = "CVSS deve ser um número entre 0.0 e 10.0.";
                return false;
            }
 
 
            if ($cve !== '' && $cve !== null) {
                if (mb_strlen($cve, 'UTF-8') > self::MAX_CVE || !preg_match('/^CVE-\d{4}-\d{4,}$/', $cve)) {
                    $this->msgErro = "Formato de CVE inválido (ex: CVE-2024-0001).";
                    return false;
                }
            }
 
 
            if (mb_strlen($descricao, 'UTF-8') > self::MAX_DESCRICAO) {
                $this->msgErro = "Descrição muito longa (máx. " . self::MAX_DESCRICAO . " caracteres).";
                return false;
            }
 
 
            if (mb_strlen($descricao_tecnica, 'UTF-8') > self::MAX_DESCRICAO_TECNICA) {
                $this->msgErro = "Descrição técnica muito longa (máx. " . self::MAX_DESCRICAO_TECNICA . " caracteres).";
                return false;
            }
 
 
            if (mb_strlen($impacto_negocio, 'UTF-8') > self::MAX_IMPACTO) {
                $this->msgErro = "Impacto muito longo (máx. " . self::MAX_IMPACTO . " caracteres).";
                return false;
            }
 
 
            if (!in_array($categoria, self::CATEGORIAS_VALIDAS, true)) {
                $this->msgErro = "Categoria inválida.";
                return false;
            }
 
 
            if (!in_array($severidade_vulnerabilidade, self::SEVERIDADES_VALIDAS, true)) {
                $this->msgErro = "Severidade inválida.";
                return false;
            }


            if (mb_strlen($responsavel, 'UTF-8') > self::MAX_RESPONSAVEL) {
                $this->msgErro = "Responsável muito longo (máx. " . self::MAX_RESPONSAVEL . " caracteres).";
                return false;
            }


            if (!in_array($status, self::STATUS_VALIDOS, true)) {
                $this->msgErro = "Status inválido.";
                return false;
            }
 
            return true;
        }
 
       public function cadastrarVulnerabilidade($id, $projeto_id, $nome, $cvss, $cve, $descricao, $descricao_tecnica, $categoria, $severidade_vulnerabilidade, $habilitado, $impacto_negocio, $responsavel, $status)
        {
            if (!$this->validarDados($nome, $cvss, $cve, $descricao, $descricao_tecnica, $categoria, $severidade_vulnerabilidade, $impacto_negocio, $responsavel, $status)) {
                return false;
            }
 
            $verifica = $this->pdo->prepare("SELECT id FROM vulnerabilidade WHERE nome = :nome AND projeto_id = :projeto_id");
            $verifica->bindValue(":nome", $nome);
            $verifica->bindValue(":projeto_id", $projeto_id);
            $verifica->execute();
 
            if ($verifica->rowCount() > 0) {
                $this->msgErro = "Já existe uma vulnerabilidade com esse nome neste projeto.";
                return false;
            }
 
            $sql = $this->pdo->prepare("INSERT INTO vulnerabilidade
                (projeto_id, nome, cvss, cve, descricao, descricao_tecnica, categoria, severidade_vulnerabilidade, habilitado, impacto_negocio, responsavel, status)
                VALUES
                (:projeto_id, :nome, :cvss, :cve, :descricao, :descricao_tecnica, :categoria, :severidade_vulnerabilidade, :habilitado, :impacto_negocio, :responsavel, :status)");
 
            $sql->bindValue(":projeto_id", $projeto_id);
            $sql->bindValue(":nome", $nome);
            $sql->bindValue(":cvss", $cvss);
            $sql->bindValue(":cve", $cve !== '' ? $cve : null);
            $sql->bindValue(":descricao", $descricao);
            $sql->bindValue(":descricao_tecnica", $descricao_tecnica);
            $sql->bindValue(":categoria", $categoria);
            $sql->bindValue(":severidade_vulnerabilidade", $severidade_vulnerabilidade);
            $sql->bindValue(":habilitado", $habilitado);
            $sql->bindValue(":impacto_negocio", $impacto_negocio);
            $sql->bindValue(":responsavel", $responsavel !== '' ? $responsavel : null);
            $sql->bindValue(":status", $status);
            $sql->execute();
 
            return true;
        }
 
        public function listarVulnerabilidade()
        {
            $sql = $this->pdo->prepare("SELECT v.*, p.nome AS projeto_nome
                FROM vulnerabilidade v
                LEFT JOIN projeto p ON p.id = v.projeto_id
                ORDER BY v.id");
            $sql->execute();
 
            return $sql->fetchAll(PDO::FETCH_ASSOC);
        }
 
        public function listarProjetos()
        {
            $sql = $this->pdo->prepare("SELECT id, nome FROM projeto WHERE habilitado = 1 ORDER BY nome");
            $sql->execute();

            return $sql->fetchAll(PDO::FETCH_ASSOC);
        }

        public function excluirVulnerabilidades($id)
        {
            $sql = $this->pdo->prepare("DELETE FROM vulnerabilidade WHERE id = :id");
            $sql->bindValue(":id", $id);
            $sql->execute();
 
            return $sql->rowCount() > 0;
        }
 
        public function buscarDadosVulnerabilidades($id)
        {
            $sql = $this->pdo->prepare("SELECT * FROM vulnerabilidade WHERE id = :id");
            $sql->bindValue(":id", $id);
            $sql->execute();
 
            return $sql->fetch(PDO::FETCH_ASSOC);
        }
 
        public function atualizarDadosVulnerabilidades($id, $nome, $cvss, $cve, $descricao, $descricao_tecnica, $categoria, $severidade_vulnerabilidade, $habilitado, $impacto_negocio, $responsavel, $status)
        {
            if (!$this->validarDados($nome, $cvss, $cve, $descricao, $descricao_tecnica, $categoria, $severidade_vulnerabilidade, $impacto_negocio, $responsavel, $status)) {
                return false;
            }
 
            $sql = $this->pdo->prepare("UPDATE vulnerabilidade SET
                nome = :nome,
                cvss = :cvss,
                cve = :cve,
                descricao = :descricao,
                descricao_tecnica = :descricao_tecnica,
                categoria = :categoria,
                severidade_vulnerabilidade = :severidade_vulnerabilidade,
                habilitado = :habilitado,
                impacto_negocio = :impacto_negocio,
                responsavel = :responsavel,
                status = :status
                WHERE id = :id");
 
            $sql->bindValue(":nome", $nome);
            $sql->bindValue(":cvss", $cvss);
            $sql->bindValue(":cve", $cve !== '' ? $cve : null);
            $sql->bindValue(":descricao", $descricao);
            $sql->bindValue(":descricao_tecnica", $descricao_tecnica);
            $sql->bindValue(":categoria", $categoria);
            $sql->bindValue(":severidade_vulnerabilidade", $severidade_vulnerabilidade);
            $sql->bindValue(":habilitado", $habilitado);
            $sql->bindValue(":impacto_negocio", $impacto_negocio);
            $sql->bindValue(":responsavel", $responsavel !== '' ? $responsavel : null);
            $sql->bindValue(":status", $status);
            $sql->bindValue(":id", $id);
            $sql->execute();

            return true;
        }

        public function alterarStatus($id, $habilitado)
        {
            $sql = $this->pdo->prepare("UPDATE vulnerabilidade SET habilitado = :habilitado WHERE id = :id");
            $sql->bindValue(":habilitado", $habilitado);
            $sql->bindValue(":id", $id);
            $sql->execute();
 
            return $sql->rowCount() > 0;
        }
 
    }
 