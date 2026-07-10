<?php

class GerenUsuario
{
    private $pdo;
    public $msgErro = '';

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function listarUsuario()
    {
        $sql = $this->pdo->prepare('SELECT * FROM usuario');
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarUsuario($id)
    {
        $sql = $this->pdo->prepare("SELECT * FROM usuario WHERE id = :id");
        $sql->bindValue(":id", $id);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function cadastrarUsuario($nome, $telefone, $cpf, $cargo, $especialidade, $email, $perfil_id)
    {
        $sql = $this->pdo->prepare(
            "INSERT INTO usuario (perfil_id, nome, cpf, email, senha, telefone, cargo, especialidade) 
            VALUES (:perfil_id, :nome, :cpf, :email, :senha, :telefone, :cargo, :especialidade)"
        );
        $sql->bindValue(":perfil_id", $perfil_id);
        $sql->bindValue(":nome", $nome);
        $sql->bindValue(":cpf", $cpf);
        $sql->bindValue(":email", $email);
        $sql->bindValue(":telefone", $telefone);
        $sql->bindValue(":cargo", $cargo);
        $sql->bindValue(":especialidade", $especialidade);
        $sql->execute();

        return $this->pdo->lastInsertId();
    }
    
    public function atualizarFotoUsuario($id, $foto)
    {
        $sql = $this->pdo->prepare("UPDATE usuario SET foto = :foto WHERE id = :id");
        $sql->bindValue(":foto", $foto);
        $sql->bindValue(":id", $id);
        $sql->execute();
    }

    public function atualizarDadosEmpresa($nome, $telefone, $cpf, $cargo, $especialidade, $email, $perfil_id)
    {
        $sql = $this->pdo->prepare("UPDATE usuario SET nome = :nome, telefone = :telefone, cargo = :cargo, email = :email, cpf = :cpf, responsavel = :responsavel WHERE id = :id");
        $sql->bindValue(":nome", $nome);
        $sql->bindValue(":telefone", $telefone);
        $sql->bindValue(":cpf", $cpf);
        $sql->bindValue(":cargo", $cargo);
        $sql->bindValue(":especialidade", $especialidade);
        $sql->bindValue(":email", $email);
        $sql->bindValue(":perfil", $perfil_id);
        $sql->execute();
        return $this->pdo->lastInsertId();
    }


    public function excluirUsuario($id)
    {
        $sql = $this->pdo->prepare("DELETE FROM usuario WHERE id = :id");
        $sql->bindValue(":id", $id);
        $sql->execute();
    }

    public function alterarStatusUsuario($id, $status)
    {
        $sql = $this->pdo->prepare("UPDATE usuario SET habilitado = :habilitado WHERE id = :id");
        $sql->bindValue(":habilitado", $status);
        $sql->bindValue(":id", $id);
        $sql->execute();
    }
}

?>