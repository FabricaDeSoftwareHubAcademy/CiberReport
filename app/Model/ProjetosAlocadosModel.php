<?php

namespace Model;

require_once __DIR__ . "/Projeto.php";

use PDO;

class ProjetosAlocadosModel
{
    private $pdo;
    private $projeto;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->projeto = new Projeto($pdo);
    }

    public function listarProjetosAlocados()
    {
        $projetos = $this->projeto->listarDados();

        foreach ($projetos as &$projeto) {
            $projeto['responsavel_tecnico'] = $this->buscarResponsavelTecnico($projeto['id']);
            $projeto['tipo_teste'] = $this->buscarTiposPentest($projeto['id']);
            $projeto['status_exibicao'] = $this->calcularStatusExibicao($projeto['status'], $projeto['data_fim_prevista']);
        }

        return $projetos;
    }

    public function buscarProjetoDetalhado($id)
    {
        $sql = $this->pdo->prepare(
            "SELECT projeto.*, empresa.nome_fantasia
               FROM projeto
               INNER JOIN empresa ON projeto.empresa_id = empresa.id
              WHERE projeto.id = :id
                AND projeto.habilitado = 1"
        );
        $sql->bindValue(':id', $id, PDO::PARAM_INT);
        $sql->execute();
        $projeto = $sql->fetch(PDO::FETCH_ASSOC);

        if (!$projeto) {
            return [];
        }

        $projeto['responsavel_tecnico'] = $this->buscarResponsavelTecnico($projeto['id']);
        $projeto['tipo_teste'] = $this->buscarTiposPentest($projeto['id']);
        $projeto['status_exibicao'] = $this->calcularStatusExibicao($projeto['status'], $projeto['data_fim_prevista']);

        return $projeto;
    }

    public function buscarVulnerabilidadesDoProjeto($projetoId)
    {
        $sql = $this->pdo->prepare(
            "SELECT nome, cvss, descricao_tecnica, severidade_vulnerabilidade
               FROM vulnerabilidade
              WHERE projeto_id = :projeto_id
                AND habilitado = 1
              ORDER BY cvss DESC"
        );
        $sql->bindValue(':projeto_id', $projetoId, PDO::PARAM_INT);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarProjetosAlocados()
    {
        return count($this->projeto->listarDados());
    }

    public function contarProjetosAtrasados()
    {
        $total = 0;
        foreach ($this->projeto->listarDados() as $projeto) {
            $estaAtrasado = $projeto['status'] !== 'CONCLUIDO'
                && $projeto['data_fim_prevista'] !== null
                && $projeto['data_fim_prevista'] < date('Y-m-d');

            if ($estaAtrasado) {
                $total++;
            }
        }
        return $total;
    }

    private function buscarResponsavelTecnico($projetoId)
    {
        $sql = $this->pdo->prepare(
            "SELECT u.nome
               FROM projeto_usuario
               JOIN usuario u ON u.id = projeto_usuario.usuario_id
              WHERE projeto_usuario.projeto_id = :projeto_id
                AND projeto_usuario.papel = 'ESPECIALISTA'
                AND projeto_usuario.habilitado = 1
              LIMIT 1"
        );
        $sql->bindValue(':projeto_id', $projetoId, PDO::PARAM_INT);
        $sql->execute();
        $resultado = $sql->fetch(PDO::FETCH_ASSOC);

        return $resultado['nome'] ?? null;
    }

    private function buscarTiposPentest($projetoId)
    {
        $sql = $this->pdo->prepare(
            "SELECT tp.nome
               FROM projeto_tipo_pentest
               JOIN tipo_pentest tp ON tp.id = projeto_tipo_pentest.tipo_pentest_id
              WHERE projeto_tipo_pentest.projeto_id = :projeto_id
                AND projeto_tipo_pentest.habilitado = 1"
        );
        $sql->bindValue(':projeto_id', $projetoId, PDO::PARAM_INT);
        $sql->execute();
        $linhas = $sql->fetchAll(PDO::FETCH_ASSOC);

        if (empty($linhas)) {
            return null;
        }

        $nomesDosTipos = [];
        foreach ($linhas as $linha) {
            $nomesDosTipos[] = $linha['nome'];
        }

        return implode(', ', $nomesDosTipos);
    }

    private function calcularStatusExibicao($status, $dataFimPrevista)
    {
        if ($status === 'CONCLUIDO') {
            return 'Concluído';
        }

        $prazoVencido = $dataFimPrevista !== null && $dataFimPrevista < date('Y-m-d');
        if ($prazoVencido) {
            return 'Atrasado';
        }

        return 'Aguardando';
    }
}