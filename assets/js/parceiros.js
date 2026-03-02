/**
 * ============================================================================
 * PARCEIROS.JS
 * Gestão do módulo de parceiros: Buscas assíncronas e Máscaras de Input.
 * ============================================================================
 */

/* --- Funções de Máscara de Entrada --- */
function aplicarMascaraCNPJ(v) {
    v = v.replace(/\D/g, "");                           // Remove tudo o que não é dígito
    v = v.substring(0, 14);                             // Limita a 14 dígitos numéricos
    v = v.replace(/^(\d{2})(\d)/, "$1.$2");             // Coloca ponto entre o segundo e o terceiro dígitos
    v = v.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3"); // Coloca ponto entre o quinto e o sexto dígitos
    v = v.replace(/\.(\d{3})(\d)/, ".$1/$2");           // Coloca uma barra entre o oitavo e o nono dígitos
    v = v.replace(/(\d{4})(\d)/, "$1-$2");              // Coloca um hífen depois do bloco de quatro dígitos
    return v;
}

function aplicarMascaraTelefone(v) {
    v = v.replace(/\D/g, "");                  // Remove tudo o que não é dígito
    v = v.substring(0, 11);                    // Limita a 11 caracteres (DD + 9 dígitos)
    v = v.replace(/^(\d{2})(\d)/g, "($1) $2"); // Coloca parênteses em volta dos dois primeiros dígitos
    v = v.replace(/(\d)(\d{4})$/, "$1-$2");    // Coloca hífen antes dos últimos 4 dígitos
    return v;
}

/* --- Preenchimento do Modal de Edição --- */
function preencherModalEditar(id, nome, cnpj, email, telefone, tipo) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nome').value = nome;
    document.getElementById('edit_cnpj').value = aplicarMascaraCNPJ(cnpj); // Já aplica máscara ao abrir
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_telefone').value = aplicarMascaraTelefone(telefone); // Já aplica máscara ao abrir
    document.getElementById('edit_tipo').value = tipo;
}

document.addEventListener("DOMContentLoaded", function() {
    
    /* --- Ativação Dinâmica das Máscaras --- */
    const inputsCnpj = document.querySelectorAll('input[name="cnpj"]');
    const inputsTelefone = document.querySelectorAll('input[name="telefone"]');

    inputsCnpj.forEach(input => {
        input.addEventListener('input', function(e) {
            e.target.value = aplicarMascaraCNPJ(e.target.value);
        });
    });

    inputsTelefone.forEach(input => {
        input.addEventListener('input', function(e) {
            e.target.value = aplicarMascaraTelefone(e.target.value);
        });
    });

    /* --- Busca Dinâmica de Parceiros (AJAX) --- */
    const inputBusca = document.getElementById('inputBuscaParceiro');
    const tabelaBody = document.getElementById('tabelaParceirosBody');

    if (!inputBusca || !tabelaBody) return;

    inputBusca.addEventListener('keyup', function() {
        const termoBusca = inputBusca.value;

        fetch('buscar_parceiros.php?q=' + encodeURIComponent(termoBusca))
            .then(resposta => resposta.json())
            .then(parceiros => {
                tabelaBody.innerHTML = '';

                // Tratamento de lista vazia
                if (parceiros.erro || parceiros.length === 0) {
                    tabelaBody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted">Nenhum parceiro encontrado.</td></tr>';
                    return;
                }

                // Renderização de resultados
                parceiros.forEach(p => {
                    let badgeTipo = p.tipo === 'seguradora' ? '<span class="badge bg-info text-dark">Seguradora</span>' : '<span class="badge bg-success">Corretora</span>';
                    
                    // Aplica máscara visualmente na tabela na hora da busca
                    let cnpjFormatado = aplicarMascaraCNPJ(p.cnpj);
                    let telefoneFormatado = aplicarMascaraTelefone(p.telefone);

                    let tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>#${p.id}</td>
                        <td class="fw-bold">${p.nome}</td>
                        <td>${cnpjFormatado}</td>
                        <td>${p.email}</td>
                        <td>${telefoneFormatado}</td>
                        <td>${badgeTipo}</td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <button class="btn btn-sm btn-outline-primary" onclick="preencherModalEditar(${p.id}, '${p.nome}', '${p.cnpj}', '${p.email}', '${p.telefone}', '${p.tipo}')" data-bs-toggle="modal" data-bs-target="#modalEditarParceiro"><i class="bi bi-pencil"></i></button>
                                <form method="POST" action="" onsubmit="return confirm('Tem certeza que deseja excluir o parceiro?');">
                                    <input type="hidden" name="acao" value="excluir">
                                    <input type="hidden" name="id" value="${p.id}">
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    `;
                    tabelaBody.appendChild(tr);
                });
            });
    });
});