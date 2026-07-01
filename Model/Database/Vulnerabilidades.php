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

        public function Vulnerabilidades($endereco_id,$nome_fantasia,$razao_social,$cnpj,$email_contato,$telefone,$responsavel)
        {
            global $pdo;

            $empresa = $pdo->prepare("SELECT id FROM vulnerabilidade WHERE cnpj = :c");
            $empresa->bindValue(":c", $cnpj);
            $empresa->execute();

            if($empresa->rowCount() > 0){
                return false;
            }
            else{
                $vulnerabilidades = $pdo->prepare("INSERT INTO empresa (endereco_id,nome_fantasia,razao_social,cnpj,email_contato,telefone,responsavel,status) VALUES (:endereco_id,:nome_fantasia,:razao_social,:cnpj,:email_contato,:telefone,:responsavel,1)");
                $empresa->bindValue(":endereco_id",$endereco_id);
                $empresa->bindValue(":nome_fantasia",$nome_fantasia);
                $empresa->bindValue(":razao_social",$razao_social);
                $empresa->bindValue(":cnpj",$cnpj);
                $empresa->bindValue(":email_contato",$email_contato);
                $empresa->bindValue(":telefone",$telefone);
                $empresa->bindValue(":responsavel",$responsavel);
                $empresa->execute();
                return true;
            }
        }

        public function ListarDados()
        {
            $dados_vulnerabilidades = array();
            global $pdo;

            
            $sql = $pdo->prepare("SELECT vulnerabilidade.*, endereco.cidade, endereco.estado FROM empresa INNER JOIN endereco ON empresa.endereco_id = endereco.id ORDER BY empresa.nome_fantasia");
            $sql->execute();

            $dados_empresa = $sql->fetchAll(PDO::FETCH_ASSOC);

            return $dados_empresa;
        }

        public function excluirVulnerabilidades($id_vulnerabilidades)
        {
            global $pdo;
            $sql = $pdo->prepare("DELETE FROM vulnerabilidades WHERE id = :id");
            $sql->bindValue(":id",$id_vulnerabilidades);
            $sql->execute();
        }

        public function buscarDadosVulnerabilidade($id_vulnerabilidade)
        {
            $dados_vulnerabilidade = array();
            global $pdo;

            $sql = $pdo->prepare("SELECT * FROM empresa WHERE id = :id");
            $sql->bindValue(":id", $id_vulnerabilidade);
            $sql->execute();

            $dados_vulnerabilidade = $sql->fetch(PDO::FETCH_ASSOC);

            return $dados_vulnerabilidade;
        }

        public function atualizarDadosVulnerabilidade($id_vulnerabilidade,$nome_fantasia,$razao_social,$cnpj,$email_contato,$telefone,$responsavel)
        {
            global $pdo;
            $sql = $pdo->prepare("UPDATE vulnerabilidade SET nome_fantasia = :nome_fantasia, razao_social = :razao_social, cnpj = :cnpj, email_contato = :email_contato, telefone = :telefone, responsavel = :responsavel WHERE id = :id");
            $sql->bindValue(":nome_fantasia",$nome_fantasia);
            $sql->bindValue(":razao_social",$razao_social);
            $sql->bindValue(":cnpj",$cnpj);
            $sql->bindValue(":email_contato",$email_contato);
            $sql->bindValue(":telefone",$telefone);
            $sql->bindValue(":responsavel",$responsavel);
            $sql->bindValue(":id",$id_vulnerabilidade);
            $sql->execute();
        }

        public function alterarStatus($id_vulnerabilidades, $status)
        {
            global $pdo;
            $sql = $pdo->prepare("UPDATE vulnerabilidade SET status = :status WHERE id = :id");
            $sql->bindValue(":status",$status);
            $sql->bindValue(":id",$id_vulnerabilidades);
            $sql->execute();
        }
    }
?>