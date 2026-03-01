/* ==========================================
   SGS - SCRIPT DO MÓDULO DE PARCEIROS
   ========================================== */

function preencherModalEditar(id, nome, cnpj, email, telefone, tipo) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nome').value = nome;
    document.getElementById('edit_cnpj').value = cnpj;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_telefone').value = telefone;
    document.getElementById('edit_tipo').value = tipo;
}