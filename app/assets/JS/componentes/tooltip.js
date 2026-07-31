// Tooltip rápido e reutilizável para qualquer elemento com data-tooltip="texto"
// fora de tabelas (ex.: modais). Dentro de <table>, quem cuida do tooltip
// continua sendo assets/JS/componentes/tabela.js (que também detecta
// automaticamente conteúdo cortado em th/td, algo que este componente não faz).
(function () {
    let tooltip = null;
    let gatilhoAtivo = null;

    const DESLOCAMENTO_CURSOR = 14;

    function obterTooltip() {
        if (!tooltip) {
            tooltip = document.createElement('div');
            tooltip.className = 'tooltip-flutuante';
            document.body.appendChild(tooltip);
        }
        return tooltip;
    }

    function posicionar(x, y) {
        const elemento = obterTooltip();
        const largura = elemento.offsetWidth;
        const altura = elemento.offsetHeight;

        const esquerda = Math.min(x + DESLOCAMENTO_CURSOR, window.innerWidth - largura - 8);
        const topo = Math.min(y + DESLOCAMENTO_CURSOR, window.innerHeight - altura - 8);

        elemento.style.left = `${esquerda}px`;
        elemento.style.top = `${topo}px`;
    }

    function mostrar(alvo, texto, x, y) {
        const elemento = obterTooltip();
        elemento.textContent = texto;
        elemento.classList.add('tooltip-flutuante--visivel');
        posicionar(x, y);
        gatilhoAtivo = alvo;
    }

    function esconder() {
        tooltip?.classList.remove('tooltip-flutuante--visivel');
        gatilhoAtivo = null;
    }

    function aoPassarMouse(evento) {
        if (evento.target.closest('table')) return;

        const gatilho = evento.target.closest('[data-tooltip]');
        if (!gatilho || gatilho === gatilhoAtivo) return;

        mostrar(gatilho, gatilho.dataset.tooltip, evento.clientX, evento.clientY);
    }

    function aoMoverMouse(evento) {
        if (!gatilhoAtivo) return;
        posicionar(evento.clientX, evento.clientY);
    }

    function aoSairMouse(evento) {
        if (!gatilhoAtivo) return;
        if (evento.relatedTarget && gatilhoAtivo.contains(evento.relatedTarget)) return;
        esconder();
    }

    document.addEventListener('mouseover', aoPassarMouse);
    document.addEventListener('mousemove', aoMoverMouse);
    document.addEventListener('mouseout', aoSairMouse);
    window.addEventListener('scroll', esconder, true);
    window.addEventListener('resize', esconder);
})();
