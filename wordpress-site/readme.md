# 🚀 SABi - Sistema Acadêmico de Biblioteca Inteligente

![SABi Banner](fotos-sistema/site-sabi/01-pagina-inicial.png)

## 📋 Sobre o Projeto
O **SABi** é um sistema completo de gestão bibliotecária desenvolvido como projeto acadêmico, integrando WordPress para o frontend e um bot personalizado no Discord para comunidade de estudos.

## 🎯 Funcionalidades Principais

### 🌐 Site WordPress
- **🔍 Busca Inteligente** - Localização exata de livros na biblioteca
- **📚 Estante Pessoal** - Gestão individual de leituras e anotações
- **⭐ Sistema de Avaliações** - Ratings em estrelas nos comentários
- **👥 Comunidade Acadêmica** - Interação entre usuários
- **🎓 Áreas do Conhecimento** - Organização por categorias CDD
- **🔐 Painel Administrativo** - Cadastro completo de livros

### 🤖 Bot Discord - SABi Connect
- **🎯 Sistema de Áreas** - Atribuição automática de cargos
- **📊 Ranking de Estudo** - Top 10 usuários mais dedicados
- **⏰ Pomodoro Integrado** - Gestão de tempo de estudo
- **🎪 Gestão de Eventos** - Criação automática de eventos
- **💡 Assistente de Estudos** - Dicas e recursos educacionais

## 🛠️ Tecnologias Utilizadas

### 🌐 Frontend & CMS
- **WordPress** + **Elementor** + **Astra Child Theme**
- **HTML5** + **CSS3** + **JavaScript** + **PHP**
- **WooCommerce** (catálogo de cursos)

### 💾 Backend & Banco de Dados
- **MySQL** - Banco de dados principal
- **PHP** - Lógica personalizada e integrações

### 🤖 Bot Discord
- **Python** + **Discord.py** 
- **SQLite** - Banco do bot
- **Sistema de Cogs** - Arquitetura modular

## 📸 Screenshots

### 🌐 Site SABi - Fluxo Completo do Sistema
| Página Inicial | Busca + Mapa Interativo | Busca por CDD |
|----------------|------------------------|---------------|
| ![Home](fotos-sistema/site-sabi/01-pagina-inicial.png) | ![Busca Mapa](fotos-sistema/site-sabi/02-busca-vazia-mapa-interativo.png) | ![CDD](fotos-sistema/site-sabi/03-resultado-por-cdd.png) |

| Estante Pessoal | Anotações + Avaliações | Painel Admin |
|-----------------|------------------------|--------------|
| ![Estante](fotos-sistema/site-sabi/04-minha-estante-pessoal.png) | ![Anotações](fotos-sistema/site-sabi/05-anotacoes-estante-pessoal-avaliacoes-estrelas.png) | ![Admin](fotos-sistema/site-sabi/06-sabi-connect-admin.png) |

| Cadastro Admin | Sistema de Login |
|----------------|------------------|
| ![Cadastro](fotos-sistema/site-sabi/07-cadastro-admin.png) | ![Login](fotos-sistema/site-sabi/08-login.png) |

### 💬 Discord SABi Connect
| Escolha de Área | Bot de Ajuda |
|-----------------|--------------|
| ![Áreas](fotos-sistema/discord/01-escolha-area.png) | ![Bot](fotos-sistema/discord/02-bot-ajuda.png) |

## 🚀 Como Executar

### 📦 Pré-requisitos
- WordPress 6.0+
- PHP 8.0+
- MySQL 5.7+
- Python 3.8+ (para o bot)

### 🔧 Instalação do Site
1. **Configure o WordPress**
2. **Importe o banco de dados** `database/sabi-academico.sql`
3. **Ative o tema** `astra-child`
4. **Configure os plugins** necessários

### 🤖 Executando o Bot
```bash
cd sabi-bot-discord
pip install -r requirements.txt
python SabiBot.py