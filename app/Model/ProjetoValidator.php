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
            'nivel_sigilo' => trim((string) ($dados['nivel_sigilo'] ?? '')),
            'escopo' => trim((string) ($dados['escopo'] ?? '')),
            'contrato' => trim((string) ($dados['contrato'] ?? '')),
            'restricao' => trim((string) ($dados['restricao'] ?? '')),
            'status' => trim((string) ($dados['status'] ?? 'PLANEJADO')),
            'lider_id' => filter_var($dados['lider_id'] ?? null, FILTER_VALIDATE_INT),
            'alvos' => self::sanitizarAlvos($dados['alvos'] ?? []),
            'pentests' => self::sanitizarPentests($dados['pentests'] ?? []),
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

    private static function sanitizarPentests($pentests): array
    {
        if (!is_array($pentests)) {
            return [];
        }

        return array_map(function ($pentest) {
            $pentest = is_array($pentest) ? $pentest : [];

            return [
                'tipo_pentest_id' => filter_var($pentest['tipo_pentest_id'] ?? null, FILTER_VALIDATE_INT),
                'horas_contratadas' => filter_var($pentest['horas_contratadas'] ?? null, FILTER_VALIDATE_FLOAT),
                'abordagem' => trim((string) ($pentest['abordagem'] ?? '')),
                'ambiente' => trim((string) ($pentest['ambiente'] ?? '')),
                'escopo' => trim((string) ($pentest['escopo'] ?? '')),
                'referencia' => trim((string) ($pentest['referencia'] ?? '')) ?: null,
                'frameworks_ids' => self::sanitizarIds($pentest['frameworks_ids'] ?? []),
                'analistas_ids' => self::sanitizarIds($pentest['analistas_ids'] ?? []),
            ];
        }, array_values($pentests));
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

        if ($dadosLimpos['nivel_sigilo'] === '') {
            $erros[] = 'O nível de sigilo é obrigatório.';
        }

        if ($dadosLimpos['lider_id'] === false || $dadosLimpos['lider_id'] <= 0) {
            $erros[] = 'O líder técnico do projeto é obrigatório.';
        }

        if ($dadosLimpos['escopo'] === '') {
            $erros[] = 'O resumo do projeto contratado é obrigatório.';
        }

        // O navegador conta quebra de linha como 1 caractere no maxlength, mas envia "\r\n".
        if (mb_strlen(str_replace("\r\n", "\n", $dadosLimpos['escopo'])) > 2000) {
            $erros[] = 'O resumo do projeto contratado deve ter no máximo 2000 caracteres.';
        }

        if (mb_strlen(str_replace("\r\n", "\n", $dadosLimpos['restricao'])) > 2000) {
            $erros[] = 'As restrições devem ter no máximo 2000 caracteres.';
        }

        if (empty($dadosLimpos['pentests'])) {
            $erros[] = 'Adicione ao menos um pentest ao projeto.';
        }

        $sigilosPermitidos = ['INTERNO', 'EXTERNO'];
        $statusPermitidos = ['PLANEJADO', 'EM_ANDAMENTO', 'PAUSADO', 'CONCLUIDO', 'CANCELADO'];

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

        self::validarPentests($dadosLimpos['pentests'], $erros);
        self::validarSomaHoras($dadosLimpos, $erros);

        if (count($erros) > 0) {
            throw new Exception(implode('<br>', $erros));
        }
    }

    /** As horas dos pentests saem das horas totais do projeto: a soma não pode ultrapassar o contratado. */
    private static function validarSomaHoras(array $dadosLimpos, array &$erros): void
    {
        $total = $dadosLimpos['horas_contratadas'];
        if ($total === false || empty($dadosLimpos['pentests'])) {
            return;
        }

        $soma = 0.0;
        foreach ($dadosLimpos['pentests'] as $pentest) {
            $soma += $pentest['horas_contratadas'] === false ? 0.0 : (float) $pentest['horas_contratadas'];
        }

        // Cada valor chega arredondado em 2 casas (o front converte hh:mm:ss para decimal).
        $tolerancia = 0.01 * (count($dadosLimpos['pentests']) + 1);

        if ($soma > $total + $tolerancia) {
            $erros[] = 'A soma das horas dos pentests não pode ultrapassar as horas totais contratadas do projeto.';
        }
    }

    private static function validarPentests(array $pentests, array &$erros): void
    {
        $abordagensPermitidas = ['BLACK BOX', 'GRAY BOX', 'WHITE BOX'];
        $ambientesPermitidos = ['DESENVOLVIMENTO', 'HOMOLOGACAO', 'PRODUCAO'];

        foreach ($pentests as $indice => $pentest) {
            $numero = $indice + 1;

            if ($pentest['tipo_pentest_id'] === false || $pentest['tipo_pentest_id'] <= 0) {
                $erros[] = "Pentest {$numero}: selecione o tipo de pentest.";
            }

            if ($pentest['horas_contratadas'] === false || $pentest['horas_contratadas'] <= 0) {
                $erros[] = "Pentest {$numero}: as horas contratadas devem ser maiores que zero.";
            }

            if (!in_array($pentest['abordagem'], $abordagensPermitidas, true)) {
                $erros[] = "Pentest {$numero}: a abordagem é inválida.";
            }

            if (!in_array($pentest['ambiente'], $ambientesPermitidos, true)) {
                $erros[] = "Pentest {$numero}: o ambiente é inválido.";
            }

            if ($pentest['escopo'] === '') {
                $erros[] = "Pentest {$numero}: o escopo é obrigatório.";
            }

            if (mb_strlen(str_replace("\r\n", "\n", $pentest['escopo'])) > 2000) {
                $erros[] = "Pentest {$numero}: o escopo deve ter no máximo 2000 caracteres.";
            }

            if (empty($pentest['frameworks_ids'])) {
                $erros[] = "Pentest {$numero}: selecione ao menos uma metodologia.";
            }

            if (empty($pentest['analistas_ids'])) {
                $erros[] = "Pentest {$numero}: adicione ao menos um analista.";
            }
        }
    }
}
