let passoAtual = 1;
let totalPassos = 4;
//  ✓
// if(passoAtual < totalPassos){
    //   form.addEventListener('submit', (evento) => {
    //     evento.preventDefault();
    //   });
    // }
function Stepper(){
  
  for(let i = 0; i < passoAtual; i++){
    const Stepper = document.getElementById('numero' + i);
    const separador = document.getElementById('descricao' + i);
    const form = document.querySelector('form');
    const circulo = document.querySelector('.stepper-numero')
    
    function previniAtualizacao (evento){
      evento.preventDefault();
    }
    
    if(i < passoAtual){
      Stepper.querySelector('.stepper-numero').classList.add('active');
      circulo.querySelector('span').textContent = '✓';
      if(separador) separador.classList.add('active')  
    } 
    else if(i === passoAtual){
      Stepper.querySelector('.stepper-numero').classList.add('active');
      circulo.querySelector('span').textContent = i;  
    }
  } 
    form.addEventListener('submit',previniAtualizacao);
}


let btn_avanca = document.getElementById('btn-voltar');
let btn_voltar = document.getElementById('btn-avancar');

btn_avanca.addEventListener('click', () =>{
  passoAtual++;
  Stepper()
  console.log("foi apertado")
});
btn_voltar.addEventListener('click' , () => {
  if (passoAtual > 0) {
    passoAtual--;
  }

});