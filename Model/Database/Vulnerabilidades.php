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

            $Vulnerabilidades = $pdo->prepare("SELECT id FROM Vulnerabilidade WHERE  = :c");
            $Vulnerabilidades->bindValue(":c",);
            $Vulnerabilidades->execute();

            if($id->rowCount() > 0){
                return false;
            }
            else{
                $Vulnerabilidades = $pdo->prepare("INSERT INTO Vulnerabilidade (id,projeto_id,nome,cvss,cve,descricao,descricao_tecnica, categoria,severidade_vulnerabilidade,habilidade,status) VALUES (:id,:nome,:razao_social,:cnpj,:email_contato,:telefone,:responsavel,1)");
                $Vulnerabilidades->bindValue(":id",$id);
                $Vulnerabilidades->bindValue(":projeto_id",$projeto_id);
                $Vulnerabilidades->bindValue(":nome",$nome);
                $Vulnerabilidades->bindValue(":cvss",$cvss);
                $Vulnerabilidades->bindValue(":cve",$cve);
                $Vulnerabilidades->bindValue(":descricao",$descricao);
                $Vulnerabilidades->bindValue(":descricao_tecnica",$descricao_tecnica);
                $Vulnerabilidades->bindValue(":categoria",$categoria);
                $Vulnerabilidades->bindValue(":severidade_vulnerabilidade",$severidade_vulnerabilidade);
                $Vulnerabilidades->bindValue(":habilidade",$habilitado);
                $Vulnerabilidades->bindValue(":impacto_negocio",$impacto_negocio);
                $Vulnerabilidades->execute();
                return true;
            }
        }

        public function ListarVulnerabilidade()
        {
            $dados_vulnerabilidades = array();
            global $pdo;

            
            $sql = $pdo->prepare("SELECT vulnerabilidade.*, endereco.cidade, endereco.estado FROM empresa INNER JOIN endereco ON empresa.endereco_id = endereco.id ORDER BY empresa.nome_fantasia");
            $sql->execute();

            $dados_vulnerabilidades = $sql->fetchAll(PDO::FETCH_ASSOC);

            return $dados_vulnerabilidades;
        }

        public function excluirVulnerabilidades($id)
        {
            global $pdo;
            $sql = $pdo->prepare("DELETE FROM vulnerabilidades WHERE id = :id");
            $sql->bindValue(":id",$id);
            $sql->execute();
        }

        public function buscarDadosVulnerabilidades($id)
        {
            $dados_vulnerabilidades = array();
            global $pdo;

            $sql = $pdo->prepare("SELECT * FROM vulnerabidade WHERE id = :id");
            $sql->bindValue(":id", $id);
            $sql->execute();

            $dados_vulnerabilidades = $sql->fetch(PDO::FETCH_ASSOC);

            return $dados_vulnerabilidades;
        }

        public function atualizarDadosVulnerabilidades($id,$nome,$cvss,$cve,$descricao,$descricao_tecnica,$categoria, $severidade_vulnerabilidade, $habilitado, $impacto_negocio)
        {
            global $pdo;
            $sql = $pdo->prepare("UPDATE vulnerabilidade SET nome = :cvss, cve = :descricao, descricao_tecnica, categoria = severidade_vulnerabilidade, habilitado , impacto_negocio , WHERE id = :id");
            $sql->bindValue(":nome",$nome);
            $sql->bindValue(":cvss",$cvss);
            $sql->bindValue(":cve",$cve);
            $sql->bindValue(":descricao",$descricao);
            $sql->bindValue(":descricao_tecnica",$descricao_tecnica);
            $sql->bindValue(":categoria",$categoria);
            $sql->bindValue(":severidade_vulnerabilidade",$severidade_vulnerabilidade);
            $sql->bindValue(":habilitado",$habilitado);
            $sql->bindValue(":impacto_negocio",$impacto_negocio);
            $sql->bindValue(":id",$id);
            $sql->execute();
        }

        public function alterarStatus($id, $habilitado)
        {
            global $pdo;
            $sql = $pdo->prepare("UPDATE vulnerabilidade SET status = :status WHERE id = :id");
            $sql->bindValue(":status",$habilitado);
            $sql->bindValue(":id",$id);
            $sql->execute();
        }
    }
?>