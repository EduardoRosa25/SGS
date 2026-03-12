# Sistema de Gestão de Seguros (SGS)

O **SGS** é uma plataforma centralizada para a gestão de seguros corporativos, desenvolvida para substituir processos manuais e fragmentados por uma solução segura, escalável e automatizada.

## 🚀 Funcionalidades Principais

O sistema abrange todo o ciclo de vida dos seguros corporativos:

* **Gestão de Apólices**: Cadastro de seguros (Vida, Auto, Cyber, etc.) com controle de vigência e upload de documentos.
* **Alertas Automáticos**: Emissão de alertas de vencimento programados para 60 e 30 dias de antecedência.
* **Módulo de Boletos**: Gestão financeira vinculada às apólices, incluindo cálculo automático de valores e impostos (IOF).
* **Gestão de Sinistros**: Registro de ocorrências, anexos de laudos/fotos e acompanhamento de valores indenizados.
* **Gestão de Parceiros**: Cadastro centralizado de Corretores e Seguradoras.
* **Orçamentos**: Geração de previsões orçamentárias com base no histórico de apólices e boletos realizados.

## 📂 Estrutura do Projeto

A organização dos diretórios segue o padrão abaixo:

* `/assets`: Recursos estáticos do sistema.
    * `/css`: Estilos customizados (proibido o uso de CSS inline).
    * `/js`: Scripts para comportamento e lógica de interface.
    * `/img`: Logotipos e imagens do layout.
* `/config`: Arquivos de configuração e conexão com o Banco de Dados.
* `/pages`: Telas do sistema (dashboard, cadastros, relatórios, etc.).
* `/includes`: Trechos de código reutilizáveis (menus, rodapés).
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
2.  **Segurado / Corretor**: Acesso às suas próprias apólices, renovações, orçamentos e sinistros.

## 📄 Equipe do Projeto

* Eduardo Antônio da Silva
* Eduardo Rosa Afonso
* Lucas Pinheiro
* Pedro Henrique Ferreira Simões
* Rogério Anastácio
