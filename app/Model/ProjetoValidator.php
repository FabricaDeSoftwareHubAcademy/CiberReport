<?php

/**
 * Erro de validação do formulário de projeto, com uma mensagem por campo.
 *
 * As chaves seguem os nomes do formulário: "nome", "empresa_id", ... e, para
 * os blocos de pentest, "pentests.{índice}.{campo}" (ex.: "pentests.0.escopo").
 * O modal usa essas chaves para mostrar cada mensagem no próprio campo.
 */
class ValidacaoProjetoException extends Exception
{
    public function __construct(public readonly array $erros)
    {
        parent::__construct(implode(' ', $erros));
    }
}

class ProjetoValidator
{
    private const ABORDAGENS = ['BLACK BOX', 'GRAY BOX', 'WHITE BOX'];
    private const AMBIENTES = ['DESENVOLVIMENTO', 'HOMOLOGACAO', 'PRODUCAO'];
    private const SIGILOS = ['INTERNO', 'EXTERNO'];
    private const STATUS = ['PLANEJADO', 'EM_ANDAMENTO', 'PAUSADO', 'CONCLUIDO', 'CANCELADO'];

    private const MAX_NOME = 80;          // projeto.nome VARCHAR(80)
    private const MAX_TEXTO = 2000;       // resumo, restrições e escopo do pentest
    private const MAX_REFERENCIA = 255;   // projeto_pentest.referencia VARCHAR(255)

    public static function processarCadastro(array $dados): array
    {
        $dados['data_fim_real'] = null;
        $dados['status'] = 'PLANEJADO';

        $erros = [];
        $dadosLimpos = self::sanitizar($dados, $erros);
        self::validarRegras($dadosLimpos, $erros);
        self::lancarSeHouverErros($erros);

        return $dadosLimpos;
    }

    public static function processarEdicao(array $dados): array
    {
        $erros = [];
        $dadosLimpos = self::sanitizar($dados, $erros);

        $id = filter_var($dados['id'] ?? null, FILTER_VALIDATE_INT);
        if ($id === false || $id <= 0) {
            $erros['geral'] = 'Não foi possível identificar o projeto a editar. Feche o modal e abra a edição novamente.';
        }

        self::validarRegras($dadosLimpos, $erros);
        self::lancarSeHouverErros($erros);
        $dadosLimpos['id'] = $id;

        return $dadosLimpos;
    }

    private static function lancarSeHouverErros(array $erros): void
    {
        if (count($erros) > 0) {
            throw new ValidacaoProjetoException($erros);
        }
    }

    private static function sanitizar(array $dados, array &$erros): array
    {
        return [
            'nome' => trim((string) ($dados['nome'] ?? '')),
            'empresa_id' => filter_var($dados['empresa_id'] ?? null, FILTER_VALIDATE_INT),
            'data_inicio' => self::normalizarData($dados['data_inicio'] ?? null, 'data_inicio', 'de início', $erros),
            'data_fim_prevista' => self::normalizarData($dados['data_fim_prevista'] ?? null, 'data_fim_prevista', 'de término', $erros),
            'data_fim_real' => self::normalizarData($dados['data_fim_real'] ?? null, 'geral', 'de término real', $erros),
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
            $analistasIds = self::sanitizarIds($pentest['analistas_ids'] ?? []);

            return [
                'tipo_pentest_id' => filter_var($pentest['tipo_pentest_id'] ?? null, FILTER_VALIDATE_INT),
                'horas_contratadas' => filter_var($pentest['horas_contratadas'] ?? null, FILTER_VALIDATE_FLOAT),
                'abordagem' => trim((string) ($pentest['abordagem'] ?? '')),
                'ambiente' => trim((string) ($pentest['ambiente'] ?? '')),
                'escopo' => trim((string) ($pentest['escopo'] ?? '')),
                'referencia' => trim((string) ($pentest['referencia'] ?? '')) ?: null,
                'frameworks_ids' => self::sanitizarIds($pentest['frameworks_ids'] ?? []),
                'analistas_ids' => $analistasIds,
                'analistas_horas' => self::sanitizarHorasAnalistas($pentest['analistas_horas'] ?? [], $analistasIds),
            ];
        }, array_values($pentests));
    }

    /**
     * Horas de cada analista do pentest, chaveadas pelo id do usuário
     * (analistas_horas[id] = horas em decimal). Vazio vale 0: as horas do
     * analista podem ficar para depois. Valor inválido ou negativo vira false.
     */
    private static function sanitizarHorasAnalistas($horas, array $analistasIds): array
    {
        $horas = is_array($horas) ? $horas : [];
        $limpas = [];

        foreach ($analistasIds as $id) {
            $valor = trim((string) ($horas[$id] ?? ''));
            $numero = $valor === '' ? 0.0 : filter_var($valor, FILTER_VALIDATE_FLOAT);
            $limpas[$id] = ($numero === false || $numero < 0) ? false : (float) $numero;
        }

        return $limpas;
    }

    /** 80.5 → "80:30:00", no mesmo formato que o usuário digita na tela. */
    private static function formatarHoras(float $horas): string
    {
        $minutos = (int) round($horas * 60);

        return sprintf('%02d:%02d:00', intdiv($minutos, 60), $minutos % 60);
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

    /** Data no formato aaaa-mm-dd, ou null se vazia. Data inválida vira erro no campo e volta como null. */
    private static function normalizarData(mixed $valor, string $chave, string $rotulo, array &$erros): ?string
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
            $erros[$chave] = "A data {$rotulo} é inválida. Escolha a data pelo calendário.";
            return null;
        }

        return $dataInformada;
    }

    /** Tamanho como o navegador conta no maxlength: quebra de linha vale 1, mas chega como "\r\n". */
    private static function tamanhoTexto(string $texto): int
    {
        return mb_strlen(str_replace("\r\n", "\n", $texto));
    }

    private static function validarRegras(array $dadosLimpos, array &$erros): void
    {
        if ($dadosLimpos['empresa_id'] === false || $dadosLimpos['empresa_id'] <= 0) {
            $erros['empresa_id'] = 'Selecione o cliente na lista. Se ele ainda não existir, cadastre pelo botão +.';
        }

        if ($dadosLimpos['nome'] === '') {
            $erros['nome'] = 'Informe o nome do projeto (até ' . self::MAX_NOME . ' caracteres).';
        } elseif (mb_strlen($dadosLimpos['nome']) > self::MAX_NOME) {
            $erros['nome'] = 'O nome do projeto tem mais de ' . self::MAX_NOME . ' caracteres. Abrevie para caber.';
        }

        if (!in_array($dadosLimpos['nivel_sigilo'], self::SIGILOS, true)) {
            $erros['nivel_sigilo'] = 'Escolha o nível de sigilo: Interno ou Externo.';
        }

        if ($dadosLimpos['lider_id'] === false || $dadosLimpos['lider_id'] <= 0) {
            $erros['lider_id'] = 'Selecione o líder técnico do projeto na lista de pentesters.';
        }

        if (
            $dadosLimpos['data_inicio'] !== null
            && $dadosLimpos['data_fim_prevista'] !== null
            && $dadosLimpos['data_fim_prevista'] < $dadosLimpos['data_inicio']
        ) {
            $erros['data_fim_prevista'] = 'A data de término deve ser igual ou posterior à data de início.';
        }

        if (
            $dadosLimpos['data_inicio'] !== null
            && $dadosLimpos['data_fim_real'] !== null
            && $dadosLimpos['data_fim_real'] < $dadosLimpos['data_inicio']
        ) {
            $erros['geral'] = 'A data de término real não pode ser anterior à data de início.';
        }

        if ($dadosLimpos['horas_contratadas'] === false || $dadosLimpos['horas_contratadas'] <= 0) {
            $erros['horas_contratadas'] = 'Informe as horas totais contratadas no formato hh:mm:ss (ex: 80:00:00), maiores que zero.';
        }

        if ($dadosLimpos['escopo'] === '') {
            $erros['escopo'] = 'Descreva o objetivo do projeto contratado (até ' . self::MAX_TEXTO . ' caracteres).';
        } elseif (self::tamanhoTexto($dadosLimpos['escopo']) > self::MAX_TEXTO) {
            $erros['escopo'] = 'O resumo passou de ' . self::MAX_TEXTO . ' caracteres. Resuma o texto; o detalhe fica no contrato anexado.';
        }

        if (self::tamanhoTexto($dadosLimpos['restricao']) > self::MAX_TEXTO) {
            $erros['restricao'] = 'As restrições passaram de ' . self::MAX_TEXTO . ' caracteres. Resuma o texto.';
        }

        if (!in_array($dadosLimpos['status'], self::STATUS, true)) {
            $erros['geral'] = 'O status do projeto é inválido.';
        }

        if (empty($dadosLimpos['pentests'])) {
            $erros['pentests'] = 'Adicione ao menos um pentest ao projeto pelo botão "Adicionar Pentest".';
        }

        self::validarPentests($dadosLimpos['pentests'], $erros);
        self::validarSomaHoras($dadosLimpos, $erros);
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
            $erros['soma_horas_pentests'] = 'A soma das horas dos pentests ultrapassa as horas totais contratadas do projeto. '
                . 'Reduza as horas de algum pentest ou aumente as horas totais no passo 1.';
        }
    }

    /** As horas dos analistas saem das horas do pentest: a soma não pode ultrapassar. */
    private static function validarHorasAnalistas(array $pentest, array &$erros, string $chave): void
    {
        if (in_array(false, $pentest['analistas_horas'], true)) {
            $erros[$chave] = 'Informe as horas de cada analista no formato hh:mm:ss (ex: 20:00:00); deixe em branco se ainda não estiverem definidas.';
            return;
        }

        if ($pentest['horas_contratadas'] === false) {
            return; // o erro das horas do pentest já aparece no campo dele
        }

        $soma = array_sum($pentest['analistas_horas']);
        $tolerancia = 0.01 * (count($pentest['analistas_horas']) + 1); // cada valor chega arredondado em 2 casas

        if ($soma > $pentest['horas_contratadas'] + $tolerancia) {
            $erros[$chave] = 'A soma das horas dos analistas (' . self::formatarHoras($soma) . ') ultrapassa as horas deste pentest ('
                . self::formatarHoras($pentest['horas_contratadas']) . '). Reduza as horas de algum analista ou aumente as do pentest.';
        }
    }

    private static function validarPentests(array $pentests, array &$erros): void
    {
        foreach ($pentests as $indice => $pentest) {
            $campo = fn(string $nome) => "pentests.{$indice}.{$nome}";

            if ($pentest['tipo_pentest_id'] === false || $pentest['tipo_pentest_id'] <= 0) {
                $erros[$campo('tipo_pentest_id')] = 'Selecione o tipo de pentest na lista (ou cadastre um novo pelo +).';
            }

            if ($pentest['horas_contratadas'] === false || $pentest['horas_contratadas'] <= 0) {
                $erros[$campo('horas_contratadas')] = 'Informe as horas deste pentest no formato hh:mm:ss (ex: 40:00:00), maiores que zero.';
            }

            if (!in_array($pentest['abordagem'], self::ABORDAGENS, true)) {
                $erros[$campo('abordagem')] = 'Escolha a abordagem: Black Box, Gray Box ou White Box.';
            }

            if (!in_array($pentest['ambiente'], self::AMBIENTES, true)) {
                $erros[$campo('ambiente')] = 'Escolha o ambiente do teste: Desenvolvimento, Homologação ou Produção.';
            }

            if ($pentest['escopo'] === '') {
                $erros[$campo('escopo')] = 'Descreva o que será testado neste pentest (até ' . self::MAX_TEXTO . ' caracteres).';
            } elseif (self::tamanhoTexto($pentest['escopo']) > self::MAX_TEXTO) {
                $erros[$campo('escopo')] = 'O escopo do pentest passou de ' . self::MAX_TEXTO . ' caracteres. Resuma o texto.';
            }

            if ($pentest['referencia'] !== null && mb_strlen($pentest['referencia']) > self::MAX_REFERENCIA) {
                $erros[$campo('referencia')] = 'A referência passou de ' . self::MAX_REFERENCIA . ' caracteres. Cite só os códigos (ex: CWE-79, CVE-2024-1234).';
            }

            if (empty($pentest['frameworks_ids'])) {
                $erros[$campo('frameworks_ids')] = 'Selecione ao menos uma metodologia na lista.';
            }

            if (empty($pentest['analistas_ids'])) {
                $erros[$campo('analistas_ids')] = 'Adicione ao menos um analista que vai executar este pentest.';
            } else {
                self::validarHorasAnalistas($pentest, $erros, $campo('analistas_ids'));
            }
        }
    }
}
