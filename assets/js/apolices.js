/**
 * ============================================================================
 * APOLICES.JS
 * Script responsável pela gestão do módulo de apólices, englobando o preenchimento
 * de modais, cálculo de IOF em tempo real e busca assíncrona (AJAX).
 * ============================================================================
 */

/* --- Preenchimento do Modal de Edição --- */
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

document.addEventListener('DOMContentLoaded', function() {
    
    /* --- Calculadora Automática de IOF --- */
    const TAXA_IOF = 0.0738; 

    function ativarCalculo(idPremio, idTotal) {
        const inputPremio = document.getElementById(idPremio);
        const inputTotal = document.getElementById(idTotal);

        if (inputPremio && inputTotal) {
            inputPremio.addEventListener('input', function() {
                let premioLiquido = parseFloat(this.value);
                
                // Validação de entrada numérica positiva
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

    // Atribuição dos listeners para os formulários de cadastro e edição
    ativarCalculo('premio_liquido', 'valor_total');
    ativarCalculo('edit_premio_liquido', 'edit_valor_total');

    /* --- Busca Dinâmica de Apólices --- */
    const inputBusca = document.getElementById('inputBuscaApolice');
    const tabelaBody = document.getElementById('tabelaApolicesBody');

    if (inputBusca && tabelaBody) {
        inputBusca.addEventListener('keyup', function() {
            const termoBusca = inputBusca.value;

            fetch('buscar_apolices.php?q=' + encodeURIComponent(termoBusca))
                .then(resposta => resposta.json())
                .then(apolices => {
                    tabelaBody.innerHTML = '';

                    // Tratamento de lista vazia
                    if (apolices.erro || apolices.length === 0) {
                        tabelaBody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted">Nenhuma apólice encontrada.</td></tr>';
                        return;
                    }

                    // Renderização de resultados
                    apolices.forEach(a => {
                        let badgeStatus = '';
                        if (a.status_apolice === 'vigente') badgeStatus = '<span class="badge bg-success">Vigente</span>';
                        else if (a.status_apolice === 'vencida') badgeStatus = '<span class="badge bg-danger">Vencida</span>';
                        else badgeStatus = '<span class="badge bg-secondary">Cancelada</span>';

                        let btnPdf = a.arquivo_apolice ? `<a href="${a.arquivo_apolice}" target="_blank" class="btn btn-sm btn-outline-info" title="Ver PDF"><i class="bi bi-file-pdf"></i></a>` : '';
                        
                        let arquivoParam = a.arquivo_apolice ? a.arquivo_apolice : '';
                        let btnEditar = `<button class="btn btn-sm btn-outline-primary" 
                            onclick="preencherModalEditarApolice(${a.id}, '${a.numero_apolice}', '${a.tipo_seguro}', ${a.seguradora_id}, ${a.corretora_id}, ${a.premio_liquido}, ${a.valor_total}, '${a.data_inicio_original}', '${a.data_fim_original}', '${a.status_apolice}', '${arquivoParam}')"
                            data-bs-toggle="modal" data-bs-target="#modalEditarApolice" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>`;

                        let btnExcluir = `<form method="POST" action="" class="d-inline" onsubmit="return confirm('Excluir apólice ${a.numero_apolice}?');">
                            <input type="hidden" name="acao" value="excluir">
                            <input type="hidden" name="id" value="${a.id}">
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir"><i class="bi bi-trash"></i></button>
                        </form>`;

                        let tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="fw-bold">${a.numero_apolice}</td>
                            <td>${a.tipo_seguro}</td>
                            <td>${a.seguradora_nome}</td>
                            <td>${a.data_inicio} a <br><strong class="text-danger">${a.data_fim}</strong></td>
                            <td>R$ ${a.valor_total_formatado}</td>
                            <td>${badgeStatus}</td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    ${btnPdf}
                                    ${btnEditar}
                                    ${btnExcluir}
                                </div>
                            </td>
                        `;
                        tabelaBody.appendChild(tr);
                    });
                });
        });
    }

    // --- LÓGICA DE GERAÇÃO AUTOMÁTICA DO NÚMERO DA APÓLICE ---
    const selectTipoNovo = document.getElementById('novo_tipo_seguro');
    const inputNumeroNovo = document.getElementById('novo_numero_apolice');

    if (selectTipoNovo && inputNumeroNovo) {
        function gerarNumeroApolice() {
            // Se não tiver tipo selecionado, deixa em branco
            if (!selectTipoNovo.value) {
                inputNumeroNovo.value = '';
                return;
            }

            // Dicionário de Prefixos (Fácil identificação visual)
            const prefixos = {
                'Auto': 'AUT',
                'Vida': 'VID',
                'RCG': 'RCG',
                'Cyber': 'CYB',
                'Riscos Operacionais': 'ROP',
                'D&O': 'DNO'
            };

            let prefixo = prefixos[selectTipoNovo.value] || 'SEG';
            let data = new Date();
            let ano = data.getFullYear();
            let mes = String(data.getMonth() + 1).padStart(2, '0'); // Garante 2 dígitos (ex: 03)
            let aleatorio = Math.floor(10000 + Math.random() * 90000); // Número de 5 dígitos
            
            // Monta o código final (Ex: AUT-202603-54321)
            inputNumeroNovo.value = `${prefixo}-${ano}${mes}-${aleatorio}`;
        }

        // Gera o número toda vez que o usuário trocar o tipo de seguro
        selectTipoNovo.addEventListener('change', gerarNumeroApolice);
    }
});