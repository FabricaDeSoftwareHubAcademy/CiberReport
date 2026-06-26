const prevBtn = document.querySelectorAll(".btn-prev");
const nextBtn = document.querySelectorAll(".btn-next");
const progress = document.getElementById("progress");
const formSteps = document.querySelectorAll(".form-step");
const progressSteps = document.querySelectorAll(".progress-step");

let formStepsNum = 0;

nextBtn.forEach(btn => {
    btn.addEventListener("click", () => {
        if (formStepsNum < formSteps.length - 1) {
            formStepsNum++;
            updateFormSteps();
            updateProgressbar();
        }
    });
});

prevBtn.forEach(btn => {
    btn.addEventListener("click", () => {
        if (formStepsNum > 0) {
            formStepsNum--;
            updateFormSteps();
            updateProgressbar();
        }
    });
});

function updateFormSteps() {
    formSteps.forEach(formStep => {
        formStep.classList.remove("form-step-active");
    });
    formSteps[formStepsNum].classList.add("form-step-active");
    updateWizardButtons();
}

function updateWizardButtons() {
    const isFirst = formStepsNum === 0;
    const isLast  = formStepsNum === formSteps.length - 1;

    document.querySelectorAll('[data-botao-passo]').forEach(btn => {
        const papel = btn.dataset.botaoPasso;
        if (papel === 'cancelar') btn.style.display = isFirst  ? '' : 'none';
        if (papel === 'voltar')   btn.style.display = !isFirst ? '' : 'none';
        if (papel === 'avancar')  btn.style.display = !isLast  ? '' : 'none';
        if (papel === 'salvar')   btn.style.display = isLast   ? '' : 'none';
    });
}

function updateProgressbar() {
    progressSteps.forEach((step, idx) => {
        if (idx < formStepsNum + 1) {
            step.classList.add('progress-step-active');
        } else {
            step.classList.remove('progress-step-active');
        }
    });

    const progressActive = document.querySelectorAll(".progress-step-active");
    progress.style.width = ((progressActive.length - 1) / (progressSteps.length - 1) * 100 + "%");
}

window.goToStep = function(idx) {
    formStepsNum = idx;
    updateFormSteps();
    updateProgressbar();
};

updateWizardButtons();