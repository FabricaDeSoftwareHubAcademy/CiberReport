const TIPOS_IMAGEM_PERMITIDOS = ['image/jpeg', 'image/png', 'image/webp'];
const TAMANHO_MAXIMO_FOTO = 5 * 1024 * 1024;

function atualizarPreviewFoto(input, previewId) {
    const preview = document.getElementById(previewId);
    const imagem = preview?.querySelector('.foto-usuario-imagem');
    const iconePadrao = preview?.querySelector('.foto-usuario-icone-padrao');
    const arquivo = input.files?.[0];

    if (!preview || !imagem || !iconePadrao || !arquivo) return;

    if (!TIPOS_IMAGEM_PERMITIDOS.includes(arquivo.type)) {
        input.value = '';
        alert('Selecione uma imagem JPG, PNG ou WEBP.');
        return;
    }

    if (arquivo.size > TAMANHO_MAXIMO_FOTO) {
        input.value = '';
        alert('A imagem deve ter no máximo 5 MB.');
        return;
    }

    const leitor = new FileReader();
    leitor.addEventListener('load', () => {
        imagem.src = leitor.result;
        imagem.hidden = false;
        imagem.style.display = 'block';
        iconePadrao.hidden = true;
        iconePadrao.style.display = 'none';
    });
    leitor.readAsDataURL(arquivo);
}

const camposFoto = [
    { inputId: 'fotoCadastro', previewId: 'previewFotoCadastro' },
    { inputId: 'fotoEdicao', previewId: 'previewFotoEdicao' }
];

camposFoto.forEach(({ inputId, previewId }) => {
    const input = document.getElementById(inputId);
    input?.addEventListener('change', () => atualizarPreviewFoto(input, previewId));
});

document.querySelectorAll('[data-selecionar-foto]').forEach((botao) => {
    botao.addEventListener('click', () => {
        document.getElementById(botao.dataset.selecionarFoto)?.click();
    });
});
