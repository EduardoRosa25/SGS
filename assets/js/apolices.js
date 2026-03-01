/* ==========================================
   SGS - SCRIPT DO MÓDULO DE APÓLICES
   ========================================== */

// 1. FUNÇÃO PARA PREENCHER O MODAL DE EDIÇÃO
function preencherModalEditarApolice(id, numero, tipo, seguradora, corretora, premio, total, inicio, fim, status, caminho_arquivo) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_numero_apolice').value = numero;
    document.getElementById('edit_tipo_seguro').value = tipo;
    document.getElementById('edit_seguradora_id').value = seguradora;
    document.getElementById('edit_corretora_id').value = corretora;
    document.getElementById('edit_premio_liquido').value = premio;
    document.getElementById('edit_valor_total').value = total;
    document.getElementById('edit_data_inicio').value = inicio;
    document.getElementById('edit_data_fim').value = fim;
    document.getElementById('edit_status_apolice').value = status;
    document.getElementById('caminho_atual').value = caminho_arquivo;
}

// 2. CALCULADORA DE IOF (Roda quando a página carrega)
document.addEventListener('DOMContentLoaded', function() {
    const TAXA_IOF = 0.0738; 

    // Função que aplica a matemática em qualquer input que a gente mandar
    function ativarCalculo(idPremio, idTotal) {
        const inputPremio = document.getElementById(idPremio);
        const inputTotal = document.getElementById(idTotal);

        if (inputPremio && inputTotal) {
            inputPremio.addEventListener('input', function() {
                let premioLiquido = parseFloat(this.value);
                if (!isNaN(premioLiquido) && premioLiquido > 0) {
                    let valorIof = premioLiquido * TAXA_IOF;
                    let valorTotalCalculado = premioLiquido + valorIof;
                    inputTotal.value = valorTotalCalculado.toFixed(2);
                } else {
                    inputTotal.value = '';
                }
            });
        }
    }

    // Ativa o cálculo para o modal de Cadastro
    ativarCalculo('premio_liquido', 'valor_total');
    // Ativa o cálculo para o modal de Edição
    ativarCalculo('edit_premio_liquido', 'edit_valor_total');
});