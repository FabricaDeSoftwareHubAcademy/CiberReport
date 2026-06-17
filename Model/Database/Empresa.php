<?php
    Class Empresa
    {
        private $pdo;

        public $msgErro = "";

        public function conectar($nome_banco, $host, $usuario, $senha)
        {
            global $pdo;
            try{
                $pdo = new PDO("mysql:dbname=".$nome_banco,$usuario,$senha);
            }
            catch(PDOException $erro){
                $this->msgErro = $erro->getMessage();
            }
        }

        public function cadastrarEmpresa($endereco_id,$nome_fantasia,$razao_social,$cnpj,$email_contato,$telefone,$responsavel)
        {
            global $pdo;

            $empresa = $pdo->prepare("SELECT id FROM empresa WHERE cnpj = :c");
            $empresa->bindValue(":c", $cnpj);
            $empresa->execute();

            if($empresa->rowCount() > 0){
                return false;
            }
            else{
                $empresa = $pdo->prepare("INSERT INTO empresa (endereco_id,nome_fantasia,razao_social,cnpj,email_contact,telefone,responsavel,status) VALUES (:endereco_id,:nome_fantasia,:razao_social,:cnpj,:email_contato,:telefone,:responsavel,1)");
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
            $dados_empresa = array();
            global $pdo;

            // join com endereco pra já trazer cidade/estado junto na listagem
            $sql = $pdo->prepare("SELECT empresa.*, endereco.cidade, endereco.estado FROM empresa INNER JOIN endereco ON empresa.endereco_id = endereco.id ORDER BY empresa.nome_fantasia");
            $sql->execute();

            $dados_empresa = $sql->fetchAll(PDO::FETCH_ASSOC);

            return $dados_empresa;
        }

        public function excluirEmpresa($id_empresa)
        {
            global $pdo;
            $sql = $pdo->prepare("DELETE FROM empresa WHERE id = :id");
            $sql->bindValue(":id",$id_empresa);
            $sql->execute();
        }

        public function buscarDadosEmpresa($id_empresa)
        {
            $dados_empresa = array();
            global $pdo;

            $sql = $pdo->prepare("SELECT * FROM empresa WHERE id = :id");
            $sql->bindValue(":id", $id_empresa);
            $sql->execute();

            $dados_empresa = $sql->fetch(PDO::FETCH_ASSOC);

            return $dados_empresa;
        }

        public function atualizarDadosEmpresa($id_empresa,$nome_fantasia,$razao_social,$cnpj,$email_contato,$telefone,$responsavel)
        {
            global $pdo;
            $sql = $pdo->prepare("UPDATE empresa SET nome_fantasia = :nome_fantasia, razao_social = :razao_social, cnpj = :cnpj, email_contact = :email_contato, telefone = :telefone, responsavel = :responsavel WHERE id = :id");
            $sql->bindValue(":nome_fantasia",$nome_fantasia);
            $sql->bindValue(":razao_social",$razao_social);
            $sql->bindValue(":cnpj",$cnpj);
            $sql->bindValue(":email_contato",$email_contato);
            $sql->bindValue(":telefone",$telefone);
            $sql->bindValue(":responsavel",$responsavel);
            $sql->bindValue(":id",$id_empresa);
            $sql->execute();
        }

        public function alterarStatus($id_empresa, $status)
        {
            global $pdo;
            $sql = $pdo->prepare("UPDATE empresa SET status = :status WHERE id = :id");
            $sql->bindValue(":status",$status);
            $sql->bindValue(":id",$id_empresa);
            $sql->execute();
        }
    }
?>