<?php
    Class Vulnerabilidades
    {
        private $pdo;

        public $msgErro = "";

        public function conectar($nome_banco, $host, $usuario, $senha)
        {
            global $pdo;
            try{
                $pdo = new PDO("mysql:host=".$host.";dbname=".$nome_banco, $usuario, $senha);
            }
            catch(PDOException $erro){
                $this->msgErro = $erro->getMessage();
            }
        }

       public function cadastrarVulnerabilidade($id, $projeto_id, $nome, $cvss, $cve, $descricao, $descricao_tecnica, $categoria, $severidade_vulnerabilidade, $habilitado, $impacto_negocio)
        {
            global $pdo;
 
 
            $verifica = $pdo->prepare("SELECT id FROM Vulnerabilidade WHERE nome = :nome AND projeto_id = :projeto_id");
            $verifica->bindValue(":nome", $nome);
            $verifica->bindValue(":projeto_id", $projeto_id);
            $verifica->execute();
 
            if ($verifica->rowCount() > 0) {
                return false;
            }
 
            $sql = $pdo->prepare("INSERT INTO Vulnerabilidade
                (id, projeto_id, nome, cvss, cve, descricao, descricao_tecnica, categoria, severidade_vulnerabilidade, habilitado, impacto_negocio, status)
                VALUES
                (:id, :projeto_id, :nome, :cvss, :cve, :descricao, :descricao_tecnica, :categoria, :severidade_vulnerabilidade, :habilitado, :impacto_negocio, 1)");
 
            $sql->bindValue(":id", $id);
            $sql->bindValue(":projeto_id", $projeto_id);
            $sql->bindValue(":nome", $nome);
            $sql->bindValue(":cvss", $cvss);
            $sql->bindValue(":cve", $cve);
            $sql->bindValue(":descricao", $descricao);
            $sql->bindValue(":descricao_tecnica", $descricao_tecnica);
            $sql->bindValue(":categoria", $categoria);
            $sql->bindValue(":severidade_vulnerabilidade", $severidade_vulnerabilidade);
            $sql->bindValue(":habilitado", $habilitado);
            $sql->bindValue(":impacto_negocio", $impacto_negocio);
            $sql->execute();
 
            return true;
        }
 
        public function listarVulnerabilidade()
        {
            global $pdo;
 
            $sql = $pdo->prepare("SELECT * FROM Vulnerabilidade ORDER BY nome");
            $sql->execute();
 
            return $sql->fetchAll(PDO::FETCH_ASSOC);
        }
 
        public function excluirVulnerabilidades($id)
        {
            global $pdo;
 
            $sql = $pdo->prepare("DELETE FROM Vulnerabilidade WHERE id = :id");
            $sql->bindValue(":id", $id);
            $sql->execute();
 
            return $sql->rowCount() > 0;
        }
 
        public function buscarDadosVulnerabilidades($id)
        {
            global $pdo;
 
            $sql = $pdo->prepare("SELECT * FROM Vulnerabilidade WHERE id = :id");
            $sql->bindValue(":id", $id);
            $sql->execute();
 
            return $sql->fetch(PDO::FETCH_ASSOC);
        }
 
        public function atualizarDadosVulnerabilidades($id, $nome, $cvss, $cve, $descricao, $descricao_tecnica, $categoria, $severidade_vulnerabilidade, $habilitado, $impacto_negocio)
        {
            global $pdo;
 
            $sql = $pdo->prepare("UPDATE Vulnerabilidade SET
                nome = :nome,
                cvss = :cvss,
                cve = :cve,
                descricao = :descricao,
                descricao_tecnica = :descricao_tecnica,
                categoria = :categoria,
                severidade_vulnerabilidade = :severidade_vulnerabilidade,
                habilitado = :habilitado,
                impacto_negocio = :impacto_negocio
                WHERE id = :id");
 
            $sql->bindValue(":nome", $nome);
            $sql->bindValue(":cvss", $cvss);
            $sql->bindValue(":cve", $cve);
            $sql->bindValue(":descricao", $descricao);
            $sql->bindValue(":descricao_tecnica", $descricao_tecnica);
            $sql->bindValue(":categoria", $categoria);
            $sql->bindValue(":severidade_vulnerabilidade", $severidade_vulnerabilidade);
            $sql->bindValue(":habilitado", $habilitado);
            $sql->bindValue(":impacto_negocio", $impacto_negocio);
            $sql->bindValue(":id", $id);
            $sql->execute();
 
            return $sql->rowCount() > 0;
        }
 
        public function alterarStatus($id, $habilitado)
        {
            global $pdo;
 
            $sql = $pdo->prepare("UPDATE Vulnerabilidade SET status = :status WHERE id = :id");
            $sql->bindValue(":status", $habilitado);
            $sql->bindValue(":id", $id);
            $sql->execute();
 
            return $sql->rowCount() > 0;
        }
    
    }
?>