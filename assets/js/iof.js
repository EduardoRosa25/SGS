/* ==========================================
   SGS - SCRIPT PRINCIPAL DO SISTEMA
   ========================================== */

// Espera todo o HTML carregar antes de rodar os scripts
document.addEventListener('DOMContentLoaded', function() {

// --------------------------------------------------------
    // MÓDULO: CÁLCULO DE IOF (Página de Apólices)
    // --------------------------------------------------------
    const inputPremio = document.getElementById('premio_liquido');
    const inputTotal = document.getElementById('valor_total');

    // A variável que você sugeriu (escrita em maiúsculo pois é uma constante fixa)
    const TAXA_IOF = 0.0738; 

    if (inputPremio && inputTotal) {
        
        inputPremio.addEventListener('input', function() {
            let premioLiquido = parseFloat(this.value);

            if (!isNaN(premioLiquido) && premioLiquido > 0) {
                
                // A matemática fica limpa e faz todo o sentido ler:
                let valorIof = premioLiquido * TAXA_IOF;
                let valorTotalCalculado = premioLiquido + valorIof;
                
                inputTotal.value = valorTotalCalculado.toFixed(2);
            } else {
                inputTotal.value = '';
            }
        });
    }

});