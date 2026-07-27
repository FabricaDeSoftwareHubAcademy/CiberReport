<?php

class ProjetoValidator
{
    public static function processarCadastro(array $dados)
    {
        $dadosLimpos = self::sanitizar($dados);
        self::validarRegras($dadosLimpos);
        return $dadosLimpos;
    }
    private static function sanitizar(array $dados): array
    {
        $nome = filter_var($dados['nome'], FILTER_SANITIZE_SPECIAL_CHARS);
        $empresa_id = filter_var($dados['empresa_id'], FILTER_VALIDATE_INT);
        $data_inicio = filter_var($dados['data_inicio'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $data_fim_prevista = filter_var($dados['data_fim_prevista'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $data_fim_real = filter_var($dados['data_fim_real'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $horas_contratadas = filter_var($dados['horas_contratadas'] ?? '', FILTER_VALIDATE_FLOAT);
        $tipo = filter_var($dados['tipo'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $nivel_sigilo = filter_var($dados['nivel_sigilo'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $escopo = filter_var($dados['escopo'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $alvo = filter_var($dados['alvo'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $contrato = filter_var($dados['contrato'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $restricao = filter_var($dados['restricao'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

        return [
            'nome' => $nome,
            'empresa_id' => $empresa_id,
            'data_inicio' => $data_inicio,
            'data_fim_prevista' => $data_fim_prevista,
            'data_fim_real' => $data_fim_real,
            'horas_contratadas' => $horas_contratadas,
            'tipo' => $tipo,
            'nivel_sigilo' => $nivel_sigilo,
            'escopo' => $escopo,
            'alvo' => $alvo,
            'contrato' => $contrato,
            'restricao' => $restricao,
        ];
    }
    
    private static function validarRegras(array $dados_limpo): void
    {
        $erros = [];

        if (empty($nome)) {
            $erros[] = 'O nome do projeto é obrigatório.';
        }
        if (empty($empresa_id)) {
            $erros[] = 'A empresa é obrigatória.';
        }
        if (empty($horas_contratadas)) {
            $erros[] = 'As horas contratadas são obrigatórias.';
        }
        if (empty($tipo)) {
            $erros[] = 'O tipo do projeto é obrigatório.';
        }
        if (empty($nivel_sigilo)) {
            $erros[] = 'O nível de sigilo é obrigatório.';
        }
        if (empty($escopo)) {
            $erros[] = 'O escopo é obrigatório.';
        }
        if (empty($alvo)) {
            $erros[] = 'O alvo é obrigatório.';
        }

        if (count($erros) > 0) {
            throw new Exception(implode("<br>", $erros));
        }
    }
}
