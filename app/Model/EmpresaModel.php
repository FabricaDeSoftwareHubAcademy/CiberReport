<?php
class Empresa
{
    private $pdo;

    public $msgErro = "";

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    public function buscarPorCnpj($cnpj)
    {
        $sql = $this->pdo->prepare("SELECT id FROM empresa WHERE cnpj = :cnpj");
        $sql->bindValue(":cnpj", $cnpj);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function cadastrarEmpresa($endereco_id, $nome_fantasia, $razao_social, $cnpj, $email_contato, $telefone, $responsavel,$telefone_responsavel, $email_responsavel,$cpf_responsavel)
    {
        $sql = $this->pdo->prepare("INSERT INTO empresa (endereco_id,nome_fantasia,razao_social,cnpj,email_contato,telefone,responsavel,telefone_responsavel,email_responsavel,cpf_responsavel,habilitado) VALUES (:endereco_id,:nome_fantasia,:razao_social,:cnpj,:email_contato,:telefone,:responsavel,:telefone_responsavel,:email_responsavel,:cpf_responsavel,1)");
        $sql->bindValue(":endereco_id", $endereco_id);
        $sql->bindValue(":nome_fantasia", $nome_fantasia);
        $sql->bindValue(":razao_social", $razao_social);
        $sql->bindValue(":cnpj", $cnpj);
        $sql->bindValue(":email_contato", $email_contato);
        $sql->bindValue(":telefone", $telefone);
        $sql->bindValue(":responsavel", $responsavel);
        $sql->bindValue(":telefone_responsavel", $telefone_responsavel);
        $sql->bindValue(":email_responsavel", $email_responsavel);
        $sql->bindValue(":cpf_responsavel", $cpf_responsavel);
        $sql->execute();
        return $this->pdo->lastInsertId();
    }

    public function ListarDadosEmpresa()
    {
        $sql = $this->pdo->prepare("SELECT empresa.*, endereco.cidade, endereco.estado FROM empresa INNER JOIN endereco ON empresa.endereco_id = endereco.id ORDER BY empresa.nome_fantasia");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarEmpresasAtivasParaSelecao()
    {
        $sql = $this->pdo->prepare(
            "SELECT id, nome_fantasia, razao_social
             FROM empresa
             WHERE habilitado = 1
             ORDER BY COALESCE(nome_fantasia, razao_social)"
        );
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarDadosEmpresa($id_empresa)
    {
        $sql = $this->pdo->prepare("SELECT * FROM empresa WHERE id = :id");
        $sql->bindValue(":id", $id_empresa);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizarDadosEmpresa($id_empresa, $nome_fantasia, $razao_social, $cnpj, $email_contato, $telefone, $responsavel, $telefone_responsavel, $email_responsavel, $cpf_responsavel )
    {
        $sql = $this->pdo->prepare("UPDATE empresa SET nome_fantasia = :nome_fantasia, razao_social = :razao_social, cnpj = :cnpj, email_contato = :email_contato, telefone = :telefone, responsavel = :responsavel, telefone_responsavel = :telefone_responsavel, email_responsavel = :email_responsavel, cpf_responsavel = :cpf_responsavel WHERE id = :id");
        $sql->bindValue(":nome_fantasia", $nome_fantasia);
        $sql->bindValue(":razao_social", $razao_social);
        $sql->bindValue(":cnpj", $cnpj);
        $sql->bindValue(":email_contato", $email_contato);
        $sql->bindValue(":telefone", $telefone);
        $sql->bindValue(":responsavel", $responsavel);
        $sql->bindValue(":id", $id_empresa);
        $sql->bindValue(":telefone_responsavel", $telefone_responsavel);
        $sql->bindValue(":email_responsavel", $email_responsavel);
        $sql->bindValue(":cpf_responsavel", $cpf_responsavel);
        $sql->execute();
    }

    public function alterarStatusEmpresa($id_empresa, $habilitado)
    {
        $sql = $this->pdo->prepare("UPDATE empresa SET habilitado = :habilitado WHERE id = :id");
        $sql->bindValue(":habilitado", $habilitado);
        $sql->bindValue(":id", $id_empresa);
        $sql->execute();
    }
}
