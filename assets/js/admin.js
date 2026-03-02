/**
 * ============================================================================
 * ADMIN.JS
 * Script responsável pela gestão assíncrona (AJAX) da listagem de usuários.
 * ============================================================================
 */
document.addEventListener("DOMContentLoaded", function() {    
    const inputBusca = document.getElementById('inputBuscaUsuario');
    const tabelaBody = document.getElementById('tabelaUsuariosBody');
    
    if (!inputBusca || !tabelaBody) return;

    const idUsuarioLogado = tabelaBody.getAttribute('data-usuariologado');

    /* --- Busca Dinâmica de Usuários --- */
    inputBusca.addEventListener('keyup', function() {
        const termoBusca = inputBusca.value;

        fetch('buscar_usuarios.php?q=' + encodeURIComponent(termoBusca))
            .then(resposta => resposta.json())
            .then(usuarios => {
                tabelaBody.innerHTML = '';

                if (usuarios.erro || usuarios.length === 0) {
                    tabelaBody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">Nenhum usuário encontrado.</td></tr>';
                    return;
                }

                usuarios.forEach(user => {
                    let corBadge = user.perfil === 'admin' ? 'danger' : 'secondary';
                    let badgePerfil = `<span class="badge bg-${corBadge}">${user.perfil.toUpperCase()}</span>`;
                    
                    let botaoAcao = '';
                    if (user.id != idUsuarioLogado) {
                        botaoAcao = `<a href="admin.php?excluir_id=${user.id}" class="btn btn-sm btn-outline-danger" title="Excluir" onclick="return confirm('Excluir este usuário permanentemente?');"><i class="bi bi-trash"></i></a>`;
                    } else {
                        botaoAcao = `<span class="text-muted small align-self-center">Você</span>`;
                    }

                    let tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>#${user.id}</td>
                        <td class="fw-bold">${user.nome}</td>
                        <td>${user.email}</td>
                        <td>${badgePerfil}</td>
                        <td>${user.data_cadastro}</td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                ${botaoAcao}
                            </div>
                        </td>
                    `;
                    tabelaBody.appendChild(tr);
                });
            });
    });
});