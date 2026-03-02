/**
 * ============================================================================
 * PARCEIROS.JS
 * Script responsável pela gestão do módulo de parceiros (Seguradoras/Corretoras)
 * e integração com a busca assíncrona.
 * ============================================================================
 */

/* --- Preenchimento do Modal de Edição --- */
function preencherModalEditar(id, nome, cnpj, email, telefone, tipo) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nome').value = nome;
    document.getElementById('edit_cnpj').value = cnpj;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_telefone').value = telefone;
    document.getElementById('edit_tipo').value = tipo;
}

document.addEventListener("DOMContentLoaded", function() {
    const inputBusca = document.getElementById('inputBuscaParceiro');
    const tabelaBody = document.getElementById('tabelaParceirosBody');

    // Interrompe a execução caso os elementos não existam
    if (!inputBusca || !tabelaBody) return;

    /* --- Busca Dinâmica de Parceiros --- */
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
                    
                    let tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>#${p.id}</td>
                        <td class="fw-bold">${p.nome}</td>
                        <td>${p.cnpj}</td>
                        <td>${p.email}</td>
                        <td>${p.telefone}</td>
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