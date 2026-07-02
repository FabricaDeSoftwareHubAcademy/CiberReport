<?php
class ChecklistModel
{
    private $pdo;
    public $msgErro = "";

    public function conectar($nome_banco, $host, $usuario, $senha)
{
    try {
        $this->pdo = new PDO(
            "mysql:host=" . $host . ";dbname=" . $nome_banco,
            $usuario,
            $senha
        );

        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    } catch (PDOException $erro) {
        $this->msgErro = $erro->getMessage();
    }
}

    public function listar()
    {
        

        $sql = $this->pdo->prepare("
        SELECT * FROM checklist 
        ORDER BY nome
    ");

        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar($id)
    {
        
        $sql = $this->pdo->prepare("SELECT * FROM checklist WHERE id = :id");
        $sql->bindValue(":id", $id);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function cadastrar($nome, $descricao, $categoria, $itens)
    {
        

        $sql = $this->pdo->prepare("
        INSERT INTO checklist 
        (nome, descricao, categoria, habilitado)
        VALUES 
        (:nome, :descricao, :categoria, 1)
    ");

        $sql->bindValue(":nome", $nome);
        $sql->bindValue(":descricao", $descricao);
        $sql->bindValue(":categoria", $categoria);
        $sql->execute();

        $checklist_id = $this->pdo->lastInsertId();

        foreach ($itens as $item) {
            $titulo = trim($item);

            if ($titulo === '') {
                continue;
            }

            $sqlItem = $this->pdo->prepare("
            INSERT INTO checklist_item
            (checklist_id, titulo, obrigatorio, habilitado)
            VALUES
            (:checklist_id, :titulo, 1, 1)
        ");

            $sqlItem->bindValue(":checklist_id", $checklist_id);
            $sqlItem->bindValue(":titulo", $titulo);
            $sqlItem->execute();
        }

        return $checklist_id;
    }

    public function excluir($id)
    {

        $sqlItens = $this->pdo->prepare("DELETE FROM checklist_item WHERE checklist_id = :id");
        $sqlItens->bindValue(":id", $id);
        $sqlItens->execute();

        $sql = $this->pdo->prepare("DELETE FROM checklist WHERE id = :id");
        $sql->bindValue(":id", $id);
        $sql->execute();
    }

    public function alterarStatus($id, $status)
    {
        

        $sql = $this->pdo->prepare("UPDATE checklist SET habilitado = :habilitado WHERE id = :id");
        $sql->bindValue(":habilitado", $status);
        $sql->bindValue(":id", $id);
        $sql->execute();
    }

    public function atualizar($id, $nome, $descricao, $categoria, $itens)
    {
        

        $sql = $this->pdo->prepare("
        UPDATE checklist 
        SET nome = :nome, descricao = :descricao, categoria = :categoria
        WHERE id = :id
    ");

        $sql->bindValue(":nome", $nome);
        $sql->bindValue(":descricao", $descricao);
        $sql->bindValue(":categoria", $categoria);
        $sql->bindValue(":id", $id);
        $sql->execute();

        $sqlDelete = $this->pdo->prepare("DELETE FROM checklist_item WHERE checklist_id = :id");
        $sqlDelete->bindValue(":id", $id);
        $sqlDelete->execute();

        foreach ($itens as $item) {
            $titulo = trim($item);

            if ($titulo === '') {
                continue;
            }

            $sqlItem = $this->pdo->prepare("
            INSERT INTO checklist_item
            (checklist_id, titulo, obrigatorio, habilitado)
            VALUES
            (:checklist_id, :titulo, 1, 1)
        ");

            $sqlItem->bindValue(":checklist_id", $id);
            $sqlItem->bindValue(":titulo", $titulo);
            $sqlItem->execute();
        }

        return true;
    }

    public function buscarComItens($id)
{
    

    $sql = $this->pdo->prepare("SELECT * FROM checklist WHERE id = :id");
    $sql->bindValue(":id", $id);
    $sql->execute();

    $checklist = $sql->fetch(PDO::FETCH_ASSOC);

    if (!$checklist) {
        return false;
    }

    $sqlItens = $this->pdo->prepare("
        SELECT * FROM checklist_item 
        WHERE checklist_id = :id 
        ORDER BY id
    ");
    $sqlItens->bindValue(":id", $id);
    $sqlItens->execute();

    $checklist['itens'] = $sqlItens->fetchAll(PDO::FETCH_ASSOC);

    return $checklist;
}

public function listarCategorias()
{
    $sql = $this->pdo->prepare("
        SELECT DISTINCT categoria
        FROM checklist
        WHERE categoria IS NOT NULL
          AND categoria <> ''
        ORDER BY categoria
    ");

    $sql->execute();

    return $sql->fetchAll(PDO::FETCH_COLUMN);
}

}
