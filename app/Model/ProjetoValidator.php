<?php

class ProjetoValidator
{
    public static function processarCadastro(array $dados): array
    {
        $dados['data_fim_real'] = null;
        $dados['status'] = 'PLANEJADO';

        $dadosLimpos = self::sanitizar($dados);
        self::validarRegras($dadosLimpos);

        return $dadosLimpos;
    }

    public static function processarEdicao(array $dados): array
    {
        $dadosLimpos = self::sanitizar($dados);
        $id = filter_var($dados['id'] ?? null, FILTER_VALIDATE_INT);

        if ($id === false || $id <= 0) {
            throw new Exception('O ID do projeto é obrigatório para edição.');
        }

        self::validarRegras($dadosLimpos);
        $dadosLimpos['id'] = $id;

        return $dadosLimpos;
    }

    private static function sanitizar(array $dados): array
    {
        return [
            'nome' => trim((string) ($dados['nome'] ?? '')),
            'empresa_id' => filter_var($dados['empresa_id'] ?? null, FILTER_VALIDATE_INT),
            'data_inicio' => self::normalizarData($dados['data_inicio'] ?? null, 'início'),
            'data_fim_prevista' => self::normalizarData($dados['data_fim_prevista'] ?? null, 'fim prevista'),
            'data_fim_real' => self::normalizarData($dados['data_fim_real'] ?? null, 'fim real'),
            'horas_contratadas' => filter_var(
                $dados['horas_contratadas'] ?? null,
                FILTER_VALIDATE_FLOAT
            ),
            'modalidade' => trim((string) ($dados['modalidade'] ?? '')),
            'nivel_sigilo' => trim((string) ($dados['nivel_sigilo'] ?? '')),
            'escopo' => trim((string) ($dados['escopo'] ?? '')),
            'contrato' => trim((string) ($dados['contrato'] ?? '')),
            'restricao' => trim((string) ($dados['restricao'] ?? '')),
            'status' => trim((string) ($dados['status'] ?? 'PLANEJADO')),
            'lider_tecnico_id' => filter_var($dados['lider_tecnico_id'] ?? null, FILTER_VALIDATE_INT),
            'alvos' => self::sanitizarAlvos($dados['alvos'] ?? []),
            'tipos_pentest_ids' => self::sanitizarIds($dados['tipos_pentest_ids'] ?? []),
            'analistas_ids' => self::sanitizarIds($dados['analistas_ids'] ?? []),
        ];
    }

    private static function sanitizarAlvos($alvos): array
    {
        if (!is_array($alvos)) {
            return [];
        }

        $alvos = array_map(fn($alvo) => trim((string) $alvo), $alvos);
        $alvos = array_filter($alvos, fn($alvo) => $alvo !== '');

        return array_values(array_unique($alvos));
    }

    private static function sanitizarIds($ids): array
    {
        if (!is_array($ids)) {
            return [];
        }

        $ids = array_map(fn($id) => filter_var($id, FILTER_VALIDATE_INT), $ids);
        $ids = array_filter($ids, fn($id) => $id !== false && $id > 0);

        return array_values(array_unique($ids));
    }

    private static function normalizarData(mixed $valor, string $campo): ?string
    {
        $dataInformada = trim((string) ($valor ?? ''));

        if ($dataInformada === '') {
            return null;
        }

        $data = DateTimeImmutable::createFromFormat('!Y-m-d', $dataInformada);
        $errosData = DateTimeImmutable::getLastErrors();
        $formatoInvalido = $errosData !== false
            && ($errosData['warning_count'] > 0 || $errosData['error_count'] > 0);

        if ($data === false || $formatoInvalido || $data->format('Y-m-d') !== $dataInformada) {
            throw new Exception("A data de {$campo} é inválida.");
        }

        return $dataInformada;
    }

    private static function validarRegras(array $dadosLimpos): void
    {
        $erros = [];

        if ($dadosLimpos['nome'] === '') {
            $erros[] = 'O nome do projeto é obrigatório.';
        }

        if ($dadosLimpos['empresa_id'] === false || $dadosLimpos['empresa_id'] <= 0) {
            $erros[] = 'A empresa é obrigatória.';
        }

        if ($dadosLimpos['horas_contratadas'] === false || $dadosLimpos['horas_contratadas'] <= 0) {
            $erros[] = 'As horas contratadas devem ser maiores que zero.';
        }

        if ($dadosLimpos['modalidade'] === '') {
            $erros[] = 'A modalidade do projeto é obrigatória.';
        }

        if ($dadosLimpos['nivel_sigilo'] === '') {
            $erros[] = 'O nível de sigilo é obrigatório.';
        }

        if ($dadosLimpos['escopo'] === '') {
            $erros[] = 'O escopo é obrigatório.';
        }

        if ($dadosLimpos['lider_tecnico_id'] === false || (int) $dadosLimpos['lider_tecnico_id'] <= 0) {
            $erros[] = 'O líder técnico é obrigatório.';
        }

        if (empty($dadosLimpos['tipos_pentest_ids'])) {
            $erros[] = 'Selecione ao menos um tipo de pentest.';
        }

        $modalidadesPermitidas = ['BLACK BOX', 'GRAY BOX', 'WHITE BOX'];
        $sigilosPermitidos = ['INTERNO', 'EXTERNO'];
        $statusPermitidos = ['PLANEJADO', 'EM_ANDAMENTO', 'PAUSADO', 'CONCLUIDO', 'CANCELADO'];

        if (!in_array($dadosLimpos['modalidade'], $modalidadesPermitidas, true)) {
            $erros[] = 'A modalidade do projeto é inválida.';
        }

        if (!in_array($dadosLimpos['nivel_sigilo'], $sigilosPermitidos, true)) {
            $erros[] = 'O nível de sigilo é inválido.';
        }

        if (!in_array($dadosLimpos['status'], $statusPermitidos, true)) {
            $erros[] = 'O status do projeto é inválido.';
        }

        if (
            $dadosLimpos['data_inicio'] !== null
            && $dadosLimpos['data_fim_prevista'] !== null
            && $dadosLimpos['data_fim_prevista'] < $dadosLimpos['data_inicio']
        ) {
            $erros[] = 'A data de fim prevista não pode ser anterior à data de início.';
        }

        if (
            $dadosLimpos['data_inicio'] !== null
            && $dadosLimpos['data_fim_real'] !== null
            && $dadosLimpos['data_fim_real'] < $dadosLimpos['data_inicio']
        ) {
            $erros[] = 'A data de fim real não pode ser anterior à data de início.';
        }

        if (count($erros) > 0) {
            throw new Exception(implode('<br>', $erros));
        }
    }
}
