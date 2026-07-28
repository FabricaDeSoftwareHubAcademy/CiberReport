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
        $status = filter_var($dados['status'] ?? 'AGUARDANDO', FILTER_SANITIZE_SPECIAL_CHARS);

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
            'status' => $status,
        ];
    }
    
    private static function validarRegras(array $dados_limpo): void
    {
        $erros = [];

        if (empty($dados_limpo['nome'])) {
            $erros[] = 'O nome do projeto é obrigatório.';
        }
        if (empty($dados_limpo['empresa_id'])) {
            $erros[] = 'A empresa é obrigatória.';
        }
        if (empty($dados_limpo['horas_contratadas'])) {
            $erros[] = 'As horas contratadas são obrigatórias.';
        }
        if (empty($dados_limpo['tipo'])) {
            $erros[] = 'O tipo do projeto é obrigatório.';
        }
        if (empty($dados_limpo['nivel_sigilo'])) {
            $erros[] = 'O nível de sigilo é obrigatório.';
        }
        if (empty($dados_limpo['escopo'])) {
            $erros[] = 'O escopo é obrigatório.';
        }
        if (empty($dados_limpo['alvo'])) {
            $erros[] = 'O alvo é obrigatório.';
        }

        if (count($erros) > 0) {
            throw new Exception(implode("<br>", $erros));
        }

        $tipos_permitidos = ['BLACK BOX', 'GRAY BOX', 'WHITE BOX'];
        $sigilos_permitidos = ['INTERNO', 'EXTERNO'];
        $status_permitidos = ['AGUARDANDO', 'EM_ANDAMENTO', 'ENCERRADO', 'INATIVADO'];

        if(!in_array($dados_limpo['tipo'], $tipos_permitidos)){
            $erros[] = 'O tipo do projeto é inválido.';
        }

        if(!in_array($dados_limpo['nivel_sigilo'], $sigilos_permitidos)){
            $erros[] = 'O nível de sigilo é inválido.';
        }
        
        if(!in_array($dados_limpo['status'], $status_permitidos)){
            $erros[] = 'O status do projeto é inválido.';
        }

        if(count($erros) > 0){
            throw new Exception(implode("<br>", $erros));
        }
    }

    public static function processarEdicao(array $dados)
    {
        $dadosLimpos = self::sanitizar($dados);
        self::validarRegras($dadosLimpos);
        $id = filter_var($dados['id'] ?? NULL, FILTER_VALIDATE_INT);

        if (!$id){
            throw new Exception('O ID do projeto é obrigatório para edição');
        }
        $dadosLimpos['id'] = $id;

        return $dadosLimpos;
    }
}
