<?php
/*
    TOAST DE NOTIFICAÇÃO
    =====================
    Componente pronto, sem variáveis — basta incluir uma vez por página,
    antes do </main>:

        include 'Components/toast.php';

    Para exibir um toast em qualquer lugar do JS, basta chamar:

        exibirToast('sucesso', 'Status atualizado com sucesso.');
        exibirToast('info', 'Você pode reordenar as colunas arrastando o cabeçalho.');
        exibirToast('aviso', 'Preencha os campos obrigatórios antes de continuar.');
        exibirToast('erro', 'Não foi possível alterar o status.');

    Tipos disponíveis: 'sucesso', 'info', 'aviso', 'erro'.

    Um título é definido automaticamente para cada tipo. Caso queira um
    título próprio, informe um terceiro argumento:

        exibirToast('sucesso', 'O checklist foi ativado.', 'Tudo certo!');

    A página também precisa carregar o script:
        <script src="../assets/JS/componentes/toast.js"></script>
*/
?>
<div class="toast-container" id="toastContainer" aria-live="polite" aria-atomic="true"></div>
