/**
 * ============================================================================
 * INDEX.JS
 * Script da página inicial (Landing Page). Verifica a sessão via AJAX para
 * alternar a visualização dos botões (Visitante vs Logado).
 * ============================================================================
 */
document.addEventListener('DOMContentLoaded', function() {
    
    /* --- Verificação Assíncrona de Sessão Ativa --- */
    fetch('session_check.php')
        .then(resposta => resposta.json())
        .then(dados => {
            if (dados.logado) {
                // Remove as opções de visitante (oculta adicionando a classe d-none)
                document.getElementById('navLoggedOut').classList.add('d-none');
                document.getElementById('heroLoggedOut').classList.add('d-none');
                
                // Exibe as opções de usuário logado (remove a classe d-none)
                document.getElementById('navLoggedIn').classList.remove('d-none');
                document.getElementById('heroLoggedIn').classList.remove('d-none');
                
                // Preenche o nome do usuário na navbar
                document.getElementById('userName').textContent = dados.nome;
            }
        })
        .catch(erro => console.info('Sessão inativa. Exibindo página padrão.'));
});