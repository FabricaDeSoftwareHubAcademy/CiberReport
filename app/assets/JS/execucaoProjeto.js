// Dados demonstrativos da referência. A tela ainda não possui integração com o banco.
(() => {
  const chart = document.getElementById('grafico-vulns');
  const severities = [
    { id: 'alta', count: 9, color: 'var(--cor-amarelo)' },
    { id: 'media', count: 11, color: 'var(--cor-azul-destaques)' },
    { id: 'critica', count: 5, color: 'var(--cor-vermelho-destaque)' },
    { id: 'baixa', count: 30, color: 'var(--cor-verde)' }
  ];
  const total = severities.reduce((sum, severity) => sum + severity.count, 0);
  let angle = 0;
  const segments = severities.map(severity => {
    const end = angle + severity.count / total * 360;
    const midpoint = ((angle + end) / 2 - 38) * Math.PI / 180;
    const label = document.createElement('span');
    label.className = 'n';
    label.id = `qtd-${severity.id}`;
    label.textContent = severity.count;
    label.style.left = `${50 + Math.sin(midpoint) * 44}%`;
    label.style.top = `${50 - Math.cos(midpoint) * 44}%`;
    chart.appendChild(label);
    const segment = `${severity.color} ${angle}deg ${end}deg`;
    angle = end;
    return segment;
  });
  chart.style.background = `conic-gradient(from -38deg, ${segments.join(',')})`;
  const steps = {
    injecao: [['Planejamento', true], ['Teste de Injeção SQL', true], ['Validação dos resultados', true]],
    rede: [['Planejamento', true], ['Descoberta de hosts', true], ['Varredura de portas', true], ['Varredura de Vulnerabilidades', true]],
    autenticacao: [['Planejamento', true], ['Teste de sessão', false], ['Teste de credenciais', false], ['Teste de Injeção SQL', true], ['Teste de permissões', false], ['Varredura de Vulnerabilidades', false], ['Teste de recuperação de senha', false], ['Teste de Autenticação', false], ['Validação dos resultados', false]]
  };
  const panel = document.getElementById('lista-testes');
  const buttons = [...document.querySelectorAll('.checklist-expandir')];
  const detailRow = document.getElementById('linha-testes');
  let selected = 'autenticacao';
  let expanded = true;
  function renderSteps(key) {
    panel.replaceChildren();
    panel.setAttribute('aria-label', `Etapas: ${buttons.find(button => button.closest('tr').dataset.item === key).textContent}`);
    steps[key].forEach(([name, checked], index) => {
      const item = document.createElement('div');
      item.className = 'projeto-teste';
      const label = document.createElement('label');
      const checkbox = document.createElement('input');
      checkbox.type = 'checkbox';
      checkbox.checked = checked;
      checkbox.addEventListener('change', () => { steps[key][index][1] = checkbox.checked; });
      label.append(checkbox, document.createTextNode(name));
      const link = document.createElement('a');
      const alvo = buttons.find(button => button.closest('tr').dataset.item === key)
        .closest('tr').cells[5].textContent.trim();
      link.href = '#';
      link.addEventListener('click', event => {
        event.preventDefault();
        const modal = document.getElementById('modal-execucao-vuln');
        if (!modal) return;
        modal.querySelector('#execucao-item').replaceChildren(new Option(name, name));
        modal.querySelector('#execucao-alvo').replaceChildren(new Option(alvo, alvo));
        modal.showModal();
      });
      link.setAttribute('aria-label', `Ver vulnerabilidades: ${name}`);
      const icon = document.createElement('i');
      icon.className = 'fa-solid fa-bug';
      icon.setAttribute('aria-hidden', 'true');
      link.appendChild(icon);
      item.append(label, link);
      panel.appendChild(item);
    });
  }
  buttons.forEach(button => button.addEventListener('click', () => {
    const key = button.closest('tr').dataset.item;
    const open = selected !== key || !expanded;
    buttons.forEach(item => item.setAttribute('aria-expanded', String(open && item === button)));
    selected = key;
    expanded = open;
    if (open) renderSteps(key);
    syncDetails();
  }));
  renderSteps(selected);
  // A linha de detalhes pertence ao item, mas não entra na paginação/filtros.
  // A classe tabela-linha-vazia já é excluída pelo componente TabelaCore.
  function syncDetails() {
    const row = document.querySelector(`tr[data-item="${selected}"]`);
    const visible = expanded && !row.hidden && row.style.display !== 'none';
    if (row.nextElementSibling !== detailRow) row.after(detailRow);
    if (detailRow.hidden === visible) detailRow.hidden = !visible;
    buttons.forEach(button => {
      const value = String(visible && button.closest('tr') === row);
      if (button.getAttribute('aria-expanded') !== value) button.setAttribute('aria-expanded', value);
    });
  }
  syncDetails();
  new MutationObserver(syncDetails).observe(document.getElementById('tabela-checklist'), {
    subtree: true, childList: true, attributes: true, attributeFilter: ['style', 'hidden']
  });

  const dialog = document.getElementById('modal-cronometro');
  const toggleTimer = document.getElementById('cronometro-alternar');
  const clock = document.getElementById('cronometro-tempo');
  const testSelect = document.getElementById('cronometro-item');
  const targetSelect = document.getElementById('cronometro-alvo');
  const timerLabel = toggleTimer.querySelector('span');
  const timerIcon = toggleTimer.querySelector('i');
  const rows = [...document.querySelectorAll('tr[data-item]')];
  rows.forEach(row => {
    testSelect.add(new Option(row.querySelector('.checklist-expandir').textContent, row.dataset.item));
  });
  const timers = new Map();
  let timerKey = null;
  function elapsed(timer) {
    return timer.elapsed + (timer.started === null ? 0 : Date.now() - timer.started);
  }
  function updateClock() {
    if (!timerKey) return;
    const timer = timers.get(timerKey);
    const seconds = Math.floor(elapsed(timer) / 1000);
    clock.textContent = [Math.floor(seconds / 3600), Math.floor(seconds / 60) % 60, seconds % 60]
      .map(value => String(value).padStart(2, '0')).join(':');
    const running = timer.started !== null;
    clock.hidden = !running && timer.elapsed === 0;
    timerLabel.textContent = running ? 'Pausar Teste' : (timer.elapsed > 0 ? 'Continuar Teste' : 'Iniciar Teste');
    timerIcon.className = running ? 'fa-solid fa-pause' : 'fa-solid fa-play';
  }
  function pauseTimer(key) {
    const timer = timers.get(key);
    if (!timer || timer.started === null) return;
    timer.elapsed = elapsed(timer);
    timer.started = null;
    document.querySelector(`tr[data-item="${key}"] .projeto-relogio`).classList.remove('em-contagem');
  }
  function selectTest(key) {
    timerKey = key;
    testSelect.value = key;
    const row = rows.find(item => item.dataset.item === key);
    targetSelect.replaceChildren(new Option(row.cells[5].textContent.trim(), row.cells[5].textContent.trim()));
    if (!timers.has(key)) timers.set(key, { elapsed: 0, started: null });
    updateClock();
  }
  testSelect.addEventListener('change', () => selectTest(testSelect.value));
  document.querySelectorAll('.projeto-relogio').forEach(button => button.addEventListener('click', () => {
    selectTest(button.closest('tr').dataset.item);
    dialog.showModal();
  }));
  document.addEventListener('click', event => {
    const link = event.target.closest('a[href*="vulnerabilidades"]');
    if (!link) return;
    event.preventDefault();
    const row = link.closest('tr[data-item]');
    const modal = document.getElementById('modal-execucao-vuln');
    if (!row || !modal) return;
    modal.querySelector('#execucao-item').replaceChildren(new Option(row.querySelector('.checklist-expandir').textContent.trim()));
    modal.querySelector('#execucao-alvo').replaceChildren(new Option(row.cells[5].textContent.trim()));
    modal.showModal();
  });
  toggleTimer.addEventListener('click', () => {
    const timer = timers.get(timerKey);
    if (timer.started === null) {
      timer.started = Date.now();
      document.querySelector(`tr[data-item="${timerKey}"] .projeto-relogio`).classList.add('em-contagem');
    } else pauseTimer(timerKey);
    updateClock();
  });
  document.getElementById('cronometro-fechar').addEventListener('click', () => dialog.close());
  setInterval(() => { if (dialog.open) updateClock(); }, 1000);

  document.querySelectorAll('.projeto-concluir').forEach(button => {
    const row = button.closest('tr');
    const badge = row.querySelector('.projeto-status');
    button.setAttribute('aria-pressed', String(badge.classList.contains('concluido')));
    button.addEventListener('click', () => {
      const complete = !badge.classList.contains('concluido');
      badge.classList.toggle('concluido', complete);
      badge.classList.toggle('andamento', !complete);
      badge.textContent = complete ? 'Concluído' : 'Em andamento';
      button.setAttribute('aria-pressed', String(complete));
      if (complete) pauseTimer(row.dataset.item);
      steps[row.dataset.item].forEach(step => { step[1] = complete; });
      if (selected === row.dataset.item) renderSteps(selected);
      window.TabelaCore?.recarregar();
      updateClock();
    });
  });
})();
