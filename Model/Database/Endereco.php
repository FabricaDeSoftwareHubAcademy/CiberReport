<?php
class Endereco
{
    private $pdo;

    public $msgErro = "";

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function cadastrarEndereco($cep, $rua, $numero, $complemento, $bairro, $cidade, $estado, $pais)
    {
        $sql = $this->pdo->prepare("INSERT INTO endereco (cep,rua,numero,complemento,bairro,cidade,estado,pais) VALUES (:cep,:rua,:numero,:complemento,:bairro,:cidade,:estado,:pais)");
        $sql->bindValue(":cep", $cep);
        $sql->bindValue(":rua", $rua);
        $sql->bindValue(":numero", $numero);
        $sql->bindValue(":complemento", $complemento);
        $sql->bindValue(":bairro", $bairro);
        $sql->bindValue(":cidade", $cidade);
        $sql->bindValue(":estado", $estado);
        $sql->bindValue(":pais", $pais);
        $sql->execute();

        return $this->pdo->lastInsertId();
    }

    public function buscarDadosEndereco($id_endereco)
    {
        $sql = $this->pdo->prepare("SELECT * FROM endereco WHERE id = :id");
        $sql->bindValue(":id", $id_endereco);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizarDadosEndereco($id_endereco, $cep, $rua, $numero, $complemento, $bairro, $cidade, $estado, $pais)
    {
        $sql = $this->pdo->prepare("UPDATE endereco SET cep = :cep, rua = :rua, numero = :numero, complemento = :complemento, bairro = :bairro, cidade = :cidade, estado = :estado, pais = :pais WHERE id = :id");
        $sql->bindValue(":cep", $cep);
        $sql->bindValue(":rua", $rua);
        $sql->bindValue(":numero", $numero);
        $sql->bindValue(":complemento", $complemento);
        $sql->bindValue(":bairro", $bairro);
        $sql->bindValue(":cidade", $cidade);
        $sql->bindValue(":estado", $estado);
        $sql->bindValue(":pais", $pais);
        $sql->bindValue(":id", $id_endereco);
        $sql->execute();
    }



    public function excluirEndereco($id_endereco)
    {
        $sql = $this->pdo->prepare("DELETE FROM endereco WHERE id = :id");
        $sql->bindValue(":id", $id_endereco);
        $sql->execute();
    }
}
