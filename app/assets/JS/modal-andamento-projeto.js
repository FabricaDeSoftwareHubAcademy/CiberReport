/**
 * modal-andamento-projeto.js
 *
 * Preenche e controla o modal "Andamento do Projeto" (aberto pelo botão
 * Visualizar da tabela). Todos os dados já vêm embutidos no data-attribute
 * do próprio modal (data-andamento), no mesmo padrão dos outros modais desta
 * página — sem chamada ajax.
 */
document.addEventListener('DOMContentLoaded', () => {
    const overlay = document.getElementById('modal-andamento-projeto');
    if (!overlay) return;

    const andamentoPorProjeto = JSON.parse(overlay.dataset.andamento || '{}');

    const SEVERIDADE_LABEL = {
        CRITICA: 'Crítica',
        ALTA: 'Alta',
        MEDIA: 'Média',
        BAIXA: 'Baixa',
        INFO: 'Info',
    };
    const SEVERIDADE_CLASSE = {
        CRITICA: 'critica',
        ALTA: 'alta',
        MEDIA: 'media',
        BAIXA: 'baixa',
        INFO: 'info',
    };
    const PAPEL_LABEL = {
        LIDER: 'Líder Técnico',
        GESTOR: 'Gestor',
        ESPECIALISTA: 'Analista',
    };

    function setText(id, texto) {
        const el = document.getElementById(id);
        if (el) el.textContent = texto;
    }

    function minutosParaTexto(minutos) {
        const h = Math.floor(minutos / 60);
        const m = minutos % 60;
        return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
    }

    function paraTituloCase(texto) {
        return texto.toLowerCase().replace(/(^|\s)\p{L}/gu, letra => letra.toUpperCase());
    }

    function formatarData(isoStr) {
        if (!isoStr) return '—';
        const [y, m, d] = isoStr.split('-');
        return `${d}/${m}/${y}`;
    }

    function formatarDataHora(datetimeStr) {
        if (!datetimeStr) return '—';
        const [dataParte, horaParte] = datetimeStr.split(' ');
        const [y, m, d] = dataParte.split('-');
        const hora = horaParte ? horaParte.slice(0, 5) : '';
        return `${d}/${m}/${y}${hora ? ', ' + hora : ''}`;
    }

    function renderizarBarras(containerId, entradas) {
        const container = document.getElementById(containerId);
        if (!container) return;
        container.innerHTML = '';

        const maximo = Math.max(1, ...entradas.map(e => e.valor));

        entradas.forEach(({ rotulo, valor, classe }) => {
            const linha = document.createElement('div');
            linha.className = 'grafico-barras__linha';
            const modificador = classe || 'categoria';
            linha.innerHTML = `
                <span class="grafico-barras__rotulo grafico-barras__rotulo--${modificador}" title="${rotulo}">${rotulo}</span>
                <div class="grafico-barras__trilha">
                    <div class="grafico-barras__fill grafico-barras__fill--${modificador}" style="width: ${(valor / maximo) * 100}%"></div>
                </div>
                <span class="grafico-barras__contagem">${valor}</span>
            `;
            container.appendChild(linha);
        });
    }

    function preencherModal(projeto) {
        const idExibicao = 'PT-' + String(projeto.id).padStart(10, '0');
        setText('and-nome', projeto.nome);
        setText('and-id', idExibicao);
        setText('and-tipos', projeto.tipos_pentest.join(', ') || '—');
        setText('and-sigilo', projeto.nivel_sigilo === 'EXTERNO' ? 'Externo' : 'Interno');
        setText('and-modalidade', paraTituloCase(projeto.modalidade));

        // ---- Resumo secundário ----
        const totalVulns = projeto.vulnerabilidades.length;
        const criticas = projeto.vulnerabilidades_por_severidade.CRITICA || 0;
        const altas = projeto.vulnerabilidades_por_severidade.ALTA || 0;
        setText('and-vulns-total', String(totalVulns));
        setText('and-vulns-extra', `${criticas} críticas · ${altas} altas`);

        setText('and-horas-restantes', minutosParaTexto(projeto.horas_restantes_minutos));

        const checklistPendentes = projeto.checklist_total - projeto.checklist_concluidos;
        setText('and-checklist-pendentes', String(checklistPendentes));
        setText('and-checklist-extra', `${projeto.checklist_total} itens no total`);

        setText('and-prazo', formatarData(projeto.data_fim_prevista));
        setText('and-inicio', 'Início: ' + formatarData(projeto.data_inicio));

        setText('and-badge-vulns', String(totalVulns));
        setText('and-badge-equipe', String(projeto.equipe.length));
        setText('and-badge-checklist', String(projeto.checklist_total));

        // ---- Aba Horas ----
        setText('and-horas-consumidas', minutosParaTexto(projeto.horas_consumidas_minutos));
        const percentual = projeto.horas_contratadas_minutos > 0
            ? (projeto.horas_consumidas_minutos / projeto.horas_contratadas_minutos) * 100
            : 0;
        setText('and-horas-percentual', `${percentual.toFixed(1).replace('.', ',')}% de ${minutosParaTexto(projeto.horas_contratadas_minutos)} horas contratadas`);
        const barra = document.getElementById('and-horas-barra');
        if (barra) barra.style.width = Math.min(100, percentual) + '%';
        setText('and-horas-consumidas-legenda', minutosParaTexto(projeto.horas_consumidas_minutos));
        setText('and-horas-restantes-legenda', minutosParaTexto(projeto.horas_restantes_minutos));

        // ---- Aba Vulnerabilidade ----
        const severidadeEntradas = Object.entries(projeto.vulnerabilidades_por_severidade)
            .filter(([, valor]) => valor > 0)
            .map(([sev, valor]) => ({ rotulo: SEVERIDADE_LABEL[sev] || sev, valor, classe: SEVERIDADE_CLASSE[sev] }));
        renderizarBarras('and-vulns-por-severidade', severidadeEntradas);

        const categoriaEntradas = Object.entries(projeto.vulnerabilidades_por_categoria)
            .map(([cat, valor]) => ({ rotulo: cat, valor, classe: null }));
        renderizarBarras('and-vulns-por-categoria', categoriaEntradas);

        const listaVulns = document.getElementById('and-vulns-lista');
        if (listaVulns) {
            listaVulns.innerHTML = '';
            projeto.vulnerabilidades.forEach(v => {
                const card = document.createElement('div');
                card.className = 'vuln-card';
                const classeSev = SEVERIDADE_CLASSE[v.severidade_vulnerabilidade] || 'info';
                card.innerHTML = `
                    <div class="vuln-card__cabecalho">
                        <span class="vuln-card__nome">${v.nome}</span>
                        <div class="vuln-card__badges">
                            <span class="badge badge--${classeSev}">${SEVERIDADE_LABEL[v.severidade_vulnerabilidade] || v.severidade_vulnerabilidade}</span>
                            <span class="badge">CVSS ${v.cvss ?? '—'}</span>
                        </div>
                    </div>
                    <p class="vuln-card__descricao">${v.descricao || ''}</p>
                `;
                listaVulns.appendChild(card);
            });
            if (projeto.vulnerabilidades.length === 0) {
                listaVulns.innerHTML = '<p class="vuln-card__descricao">Nenhuma vulnerabilidade registrada ainda.</p>';
            }
        }

        // ---- Aba Equipe ----
        const listaEquipe = document.getElementById('and-equipe-lista');
        if (listaEquipe) {
            listaEquipe.innerHTML = '';
            projeto.equipe.forEach((membro, idx) => {
                const iniciais = membro.nome.split(' ').filter(Boolean).slice(0, 2).map(p => p[0]).join('').toUpperCase();
                const item = document.createElement('div');
                item.className = 'analista-item';
                item.innerHTML = `
                    <div class="avatar avatar--cor-${idx % 5}">${iniciais}</div>
                    <div class="analista-item__info">
                        <span class="analista-item__nome">${membro.nome}</span>
                        <span class="analista-item__cargo">${PAPEL_LABEL[membro.papel] || membro.papel}</span>
                    </div>
                `;
                listaEquipe.appendChild(item);
            });
        }

        // ---- Aba Checklist ----
        setText('and-checklist-contador', `${projeto.checklist_concluidos} itens concluídos de ${projeto.checklist_total} itens`);
        const listaChecklist = document.getElementById('and-checklist-lista');
        if (listaChecklist) {
            listaChecklist.innerHTML = '';
            projeto.checklist.forEach(item => {
                const concluido = Number(item.concluido) === 1;
                const div = document.createElement('div');
                div.className = 'checklist-item' + (concluido ? ' checklist-item--concluido' : '');
                div.innerHTML = `<span class="checklist-item__label">${item.titulo}</span>`;
                listaChecklist.appendChild(div);
            });
            if (projeto.checklist.length === 0) {
                listaChecklist.innerHTML = '<div class="checklist-item"><span class="checklist-item__label">Nenhum checklist vinculado aos tipos de pentest deste projeto.</span></div>';
            }
        }

        // ---- Aba Log ----
        const listaLog = document.getElementById('and-log-lista');
        if (listaLog) {
            listaLog.innerHTML = '';
            projeto.log.forEach(evento => {
                const item = document.createElement('div');
                item.className = 'log-item';
                item.innerHTML = `
                    <span class="log-item__ponto"></span>
                    <div class="log-item__corpo">
                        <span class="log-item__titulo">${evento.descricao}</span>
                        <span class="log-item__meta">${evento.usuario_nome}, ${formatarDataHora(evento.criado_em)}</span>
                    </div>
                `;
                listaLog.appendChild(item);
            });
            if (projeto.log.length === 0) {
                listaLog.innerHTML = '<div class="log-item"><div class="log-item__corpo"><span class="log-item__titulo">Nenhuma atividade registrada ainda.</span></div></div>';
            }
        }

        const agora = new Date();
        setText('and-atualizacao', `Última atualização: hoje, ${String(agora.getHours()).padStart(2, '0')}:${String(agora.getMinutes()).padStart(2, '0')}`);

        // Sempre reabre na primeira aba
        ativarAba('horas');
    }

    function ativarAba(nomeAba) {
        overlay.querySelectorAll('.modal-tabs__item').forEach(btn => {
            btn.classList.toggle('modal-tabs__item--ativo', btn.dataset.aba === nomeAba);
        });
        overlay.querySelectorAll('.modal-tab-painel').forEach(painel => {
            painel.classList.toggle('modal-tab-painel--ativo', painel.dataset.abaConteudo === nomeAba);
        });
    }

    overlay.querySelectorAll('.modal-tabs__item').forEach(btn => {
        btn.addEventListener('click', () => ativarAba(btn.dataset.aba));
    });

    document.querySelectorAll('[data-modal-target="modal-andamento-projeto"][data-projeto-id]').forEach(botao => {
        botao.addEventListener('click', () => {
            const projeto = andamentoPorProjeto[botao.dataset.projetoId];
            if (!projeto) return;
            preencherModal(projeto);
        });
    });
});
