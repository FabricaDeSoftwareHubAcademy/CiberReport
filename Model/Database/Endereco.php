<?php
    Class Endereco
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

        public function cadastrarEndereco($cep,$rua,$numero,$complemento,$bairro,$cidade,$estado,$pais)
        {
            global $pdo;

            $sql = $pdo->prepare("INSERT INTO endereco (cep,rua,numero,complemento,bairro,cidade,estado,pais) VALUES (:cep,:rua,:numero,:complemento,:bairro,:cidade,:estado,:pais)");
            $sql->bindValue(":cep",$cep);
            $sql->bindValue(":rua",$rua);
            $sql->bindValue(":numero",$numero);
            $sql->bindValue(":complemento",$complemento);
            $sql->bindValue(":bairro",$bairro);
            $sql->bindValue(":cidade",$cidade);
            $sql->bindValue(":estado",$estado);
            $sql->bindValue(":pais",$pais);
            $sql->execute();

            return $pdo->lastInsertId();
        }

        public function buscarDadosEndereco($id_endereco)
        {
            $dados_endereco = array();
            global $pdo;

            $sql = $pdo->prepare("SELECT * FROM endereco WHERE id = :id");
            $sql->bindValue(":id", $id_endereco);
            $sql->execute();

            $dados_endereco = $sql->fetch(PDO::FETCH_ASSOC);

            return $dados_endereco;
        }

        public function atualizarDadosEndereco($id_endereco,$cep,$rua,$numero,$complemento,$bairro,$cidade,$estado,$pais)
        {
            global $pdo;
            $sql = $pdo->prepare("UPDATE endereco SET cep = :cep, rua = :rua, numero = :numero, complemento = :complemento, bairro = :bairro, cidade = :cidade, estado = :estado, pais = :pais WHERE id = :id");
            $sql->bindValue(":cep",$cep);
            $sql->bindValue(":rua",$rua);
            $sql->bindValue(":numero",$numero);
            $sql->bindValue(":complemento",$complemento);
            $sql->bindValue(":bairro",$bairro);
            $sql->bindValue(":cidade",$cidade);
            $sql->bindValue(":estado",$estado);
            $sql->bindValue(":pais",$pais);
            $sql->bindValue(":id",$id_endereco);
            $sql->execute();
        }

        public function excluirEndereco($id_endereco)
        {
            global $pdo;
            $sql = $pdo->prepare("DELETE FROM endereco WHERE id = :id");
            $sql->bindValue(":id",$id_endereco);
            $sql->execute();
        }
    }
?>