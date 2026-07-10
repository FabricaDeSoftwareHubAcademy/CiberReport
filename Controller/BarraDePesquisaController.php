<?php

class BarraDePesquisaController {
    private $pdo;

    // Recebemos a conexão já pronta ao instanciar
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function buscar($tabela_pesquisa, $colunas_pesquisa) {
        $busca = $_POST['busca'] ?? '';
        
        if (!empty($busca)) {
            $condicoes = [];
            foreach($colunas_pesquisa as $coluna) {
                $condicoes[] = "$coluna LIKE :busca";
            }
        
            $valor_de_busca = implode(' OR ', $condicoes);
        
            // Usamos $this->pdo para acessar a conexão injetada
            $sql = "SELECT * FROM $tabela_pesquisa WHERE $valor_de_busca";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['busca' => "%$busca%"]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return [];
    }
}