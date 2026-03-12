# Sistema de Gestão de Seguros (SGS)

O **SGS** é uma plataforma centralizada para a gestão de seguros corporativos, desenvolvida para substituir processos manuais e fragmentados por uma solução segura, escalável e automatizada.

## 🚀 Funcionalidades Principais

O sistema abrange todo o ciclo de vida dos seguros corporativos:

* **Gestão de Apólices**: Cadastro de seguros (Vida, Auto, Cyber, etc.) com controle de vigência e upload de documentos.
* **Alertas Automáticos**: Emissão de alertas de vencimento programados para 60 e 30 dias de antecedência.
* **Módulo de Boletos**: Gestão financeira vinculada às apólices, incluindo cálculo automático de valores e impostos (IOF).
* **Gestão de Parceiros**: Cadastro centralizado de Corretores e Seguradoras.

## 💻 Telas do Sistema

Essas são as principais telas implementadas no Sistema de Gestão de Seguros:

* **Página Principal**: Mostra uma visão geral e apresentação do sistema SGS.
* **Página Cadastro de Usuário**: Página para que novos usuários façam o cadastro (Obs.: Por estar em fase de testes está habilitado criação de perfil Administrador).
* **Página de Login**: Página para realizar login e acessar a área segura do sistema para gerenciar os seguros e apólices.
* **Página de Edição de Perfil**: Página para editar ou excluir a conta.
* **Página de Controle Administrativo**: Permite verificar os usuários cradastrados, incluindo exclusão. E baixar relatórios (Usuário, Parceiros e Apólices).
* **Página de Cadastro de Apólices**: Permite ver as suas apólices cadastradas e cadastrar nova apólice.
* **Página de Cadastro de Parceiros**: Página para cadastro de Seguradora e Corretora de Seguros.

## 📂 Estrutura do Projeto

A organização dos diretórios segue o padrão abaixo:

* `/assets`: Recursos estáticos do sistema.
    * `/css`: Estilos customizados (proibido o uso de CSS inline).
    * `/docs`: Pasta com o Arquivo enviado com a ideia inicial do projeto.
    * `/js`: Scripts para comportamento e lógica de interface.
    
* `/config`: Arquivos de configuração e conexão com o Banco de Dados MySQL utilizando PDO.
* `/pages`: Telas do sistema (Home, Apólices, Relatórios, etc.).
* `/uploads`: Local onde são armazenados os arquivos carregados do tipo PDF na pagina.
* `/database`: Scripts SQL para criação do banco de dados.

## 🛠️ Configuração Técnica

### Pré-requisitos
* Servidor Web (Apache/Nginx) com suporte a **PHP**.
* Banco de Dados **MySQL**.

### Instalação
1.  Importe o arquivo `database/bd_sgs.sql` para o seu gerenciador MySQL.
2.  Configure as credenciais de acesso ao banco no arquivo `config/db.php`.
3.  O sistema possui um administrador padrão para testes iniciais:
    * **Login**: `admin@sgs.com`
    * **Senha**: `123456`

## 👥 Perfis de Usuário

1.  **Administrador**: Possui acesso total, gerencia usuários e monitora a conformidade global.
2.  **Segurado (Cliente) / Corretor**: Acesso às suas próprias apólices, renovações, orçamentos e sinistros.

## 📄 Equipe do Projeto

* Eduardo Antônio da Silva
* Eduardo Rosa Afonso
* Lucas Pinheiro
* Pedro Henrique Ferreira Simões
* Rogério Anastácio
