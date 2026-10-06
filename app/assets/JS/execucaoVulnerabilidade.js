const modalExecucao = document.getElementById('modal-execucao-vuln');
const shellExecucao = modalExecucao?.querySelector('.vuln-shell');
if (shellExecucao) {
  const fechar = document.createElement('button');
  fechar.type = 'button'; fechar.className = 'modal-vuln-fechar'; fechar.textContent = '×';
  fechar.setAttribute('aria-label', 'Fechar'); shellExecucao.appendChild(fechar);
  fechar.addEventListener('click', () => modalExecucao.close());
}
document.getElementById('fechar-execucao-vuln')?.addEventListener('click', () => modalExecucao.close());
document.getElementById('form-execucao-vuln').addEventListener('submit', (event) => {
  event.preventDefault();
  modalExecucao.close();
});
