<?php
namespace Model;
use PDO;

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

    public function cadastrarUsuario($nome, $telefone, $cpf, $cargo, $especialidade, $email, $perfil_id, $senha): bool
    {
        $sql = $this->pdo->prepare(
            "INSERT INTO usuario (perfil_id, nome, cpf, email, senha, telefone, cargo, especialidade) 
            VALUES (:perfil_id, :nome, :cpf, :email, :senha, :telefone, :cargo, :especialidade)"
        );
        $sql->bindValue(":perfil_id", $perfil_id);
        $sql->bindValue(":nome", $nome);
        $sql->bindValue(":cpf", $cpf);
        $sql->bindValue(":senha", $senha);
        $sql->bindValue(":email", $email);
        $sql->bindValue(":telefone", $telefone);
        $sql->bindValue(":cargo", $cargo);
        $sql->bindValue(":especialidade", $especialidade);
        $sql->execute();

        return true;
    }

    public function ListarCargo()
    {
        $sql = $this->pdo->prepare(
            "SELECT cargo FROM usuario"
        );

        $sql->execute();

        return $sql->featchAll(PDO::FETCH_ASSOC);
    }
    
    public function ListarCargoPorId($id)
    {
        $sql = $this->pdo->prepare(
            "SELECT cargo FROM usuario WHERE id = :id"
        );

        $sql->bindValue(":id",$id);

        $sql->execute();

        return $sql->featch(PDO::FETCH_ASSOC);
    }

    
    
    public function atualizarFotoUsuario($id, $foto)
    {
        $sql = $this->pdo->prepare("UPDATE usuario SET foto = :foto WHERE id = :id");
        $sql->bindValue(":foto", $foto);
        $sql->bindValue(":id", $id);
        $sql->execute();
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