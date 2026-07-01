<?php
class Empresa
{
    private $pdo;

    public $msgErro = "";

    public function conectar($nome_banco, $host, $usuario, $senha)
    {
        $this->pdo = new PDO("mysql:host=" . $host . ";dbname=" . $nome_banco, $usuario, $senha);
    }

    public function buscarPorCnpj($cnpj)
    {
        $sql = $this->pdo->prepare("SELECT id FROM empresa WHERE cnpj = :cnpj");
        $sql->bindValue(":cnpj", $cnpj);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function cadastrarEmpresa($endereco_id, $nome_fantasia, $razao_social, $cnpj, $email_contato, $telefone, $responsavel)
    {
        $sql = $this->pdo->prepare("INSERT INTO empresa (endereco_id,nome_fantasia,razao_social,cnpj,email_contato,telefone,responsavel,habilitado) VALUES (:endereco_id,:nome_fantasia,:razao_social,:cnpj,:email_contato,:telefone,:responsavel,1)");
        $sql->bindValue(":endereco_id", $endereco_id);
        $sql->bindValue(":nome_fantasia", $nome_fantasia);
        $sql->bindValue(":razao_social", $razao_social);
        $sql->bindValue(":cnpj", $cnpj);
        $sql->bindValue(":email_contato", $email_contato);
        $sql->bindValue(":telefone", $telefone);
        $sql->bindValue(":responsavel", $responsavel);
        $sql->execute();
        return $this->pdo->lastInsertId();
    }

    public function ListarDados()
    {
        $sql = $this->pdo->prepare("SELECT empresa.*, endereco.cidade, endereco.estado FROM empresa INNER JOIN endereco ON empresa.endereco_id = endereco.id ORDER BY empresa.nome_fantasia");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function excluirEmpresa($id_empresa)
    {
        $sql = $this->pdo->prepare("DELETE FROM empresa WHERE id = :id");
        $sql->bindValue(":id", $id_empresa);
        $sql->execute();
    }

    public function buscarDadosEmpresa($id_empresa)
    {
        $sql = $this->pdo->prepare("SELECT * FROM empresa WHERE id = :id");
        $sql->bindValue(":id", $id_empresa);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizarDadosEmpresa($id_empresa, $nome_fantasia, $razao_social, $cnpj, $email_contato, $telefone, $responsavel)
    {
        $sql = $this->pdo->prepare("UPDATE empresa SET nome_fantasia = :nome_fantasia, razao_social = :razao_social, cnpj = :cnpj, email_contato = :email_contato, telefone = :telefone, responsavel = :responsavel WHERE id = :id");
        $sql->bindValue(":nome_fantasia", $nome_fantasia);
        $sql->bindValue(":razao_social", $razao_social);
        $sql->bindValue(":cnpj", $cnpj);
        $sql->bindValue(":email_contato", $email_contato);
        $sql->bindValue(":telefone", $telefone);
        $sql->bindValue(":responsavel", $responsavel);
        $sql->bindValue(":id", $id_empresa);
        $sql->execute();
    }

    public function alterarStatus($id_empresa, $habilitado)
    {
        $sql = $this->pdo->prepare("UPDATE empresa SET habilitado = :habilitado WHERE id = :id");
        $sql->bindValue(":habilitado", $habilitado);
        $sql->bindValue(":id", $id_empresa);
        $sql->execute();
    }
}