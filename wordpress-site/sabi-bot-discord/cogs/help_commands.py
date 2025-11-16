import discord
from discord.ext import commands

class HelpCommandsCog(commands.Cog):
    def __init__(self, bot):
        self.bot = bot
        self.bot.remove_command('help')
    
    @commands.command(name='ajuda')
    async def ajuda(self, ctx, categoria: str = ""):
        if not categoria:
            embed = discord.Embed(
                title="📚 AJUDA - SABiBot",
                description="Use `!ajuda <categoria>` para ver comandos específicos\n\n**Categorias disponíveis:**",
                color=discord.Color.blue()
            )
            embed.add_field(
                name="📋 geral",
                value="Comandos gerais e de administração",
                inline=False
            )
            embed.add_field(
                name="⏰ lembretes",
                value="Sistema de lembretes personalizados",
                inline=False
            )
            embed.add_field(
                name="📅 eventos",
                value="Agendamento de sessões de estudo",
                inline=False
            )
            embed.add_field(
                name="🍅 pomodoro",
                value="Timer de estudo Pomodoro",
                inline=False
            )
            embed.add_field(
                name="📊 progresso",
                value="Registro de estudos e XP",
                inline=False
            )
            embed.add_field(
                name="🧠 dicas",
                value="Técnicas científicas de estudo e memorização",
                inline=False
            )
            embed.add_field(
                name="📖 sabi",
                value="Informações sobre o Sistema SABi",
                inline=False
            )
            embed.set_footer(text="Exemplo: !ajuda lembretes")
            await ctx.send(embed=embed)
        
        elif categoria.lower() == 'geral':
            embed = discord.Embed(
                title="📋 Comandos Gerais",
                color=discord.Color.green()
            )
            embed.add_field(name="!ajuda [categoria]", value="Mostra esta mensagem de ajuda", inline=False)
            embed.add_field(name="!boasvindas", value="[Admin] Mensagem de boas-vindas", inline=False)
            embed.add_field(name="!gerar_cargos", value="[Admin] Gera mensagem de seleção de áreas", inline=False)
            embed.add_field(name="!adicionar_area", value="[Admin] Adiciona uma nova área de estudo", inline=False)
            embed.add_field(name="!remover_area", value="[Admin] Remove uma área existente", inline=False)
            await ctx.send(embed=embed)
        
        elif categoria.lower() == 'lembretes':
            embed = discord.Embed(
                title="⏰ Comandos de Lembretes",
                color=discord.Color.orange()
            )
            embed.add_field(
                name="!lembrete <tempo> <mensagem>",
                value="Cria um lembrete\nExemplo: `!lembrete 2h Estudar matemática`\nFormatos: 30m, 2h, 1d",
                inline=False
            )
            embed.add_field(
                name="!meus_lembretes",
                value="Lista seus lembretes pendentes",
                inline=False
            )
            embed.add_field(
                name="!cancelar_lembrete <id>",
                value="Cancela um lembrete específico",
                inline=False
            )
            await ctx.send(embed=embed)
        
        elif categoria.lower() == 'eventos':
            embed = discord.Embed(
                title="📅 Comandos de Eventos",
                color=discord.Color.blue()
            )
            embed.add_field(
                name="!agendar <tempo> <título> | <descrição>",
                value="Agenda uma sessão de estudo\nExemplo: `!agendar 3h Estudar Python | Revisar funções`",
                inline=False
            )
            embed.add_field(
                name="!eventos",
                value="Lista eventos agendados neste canal",
                inline=False
            )
            await ctx.send(embed=embed)
        
        elif categoria.lower() == 'pomodoro':
            embed = discord.Embed(
                title="🍅 Comandos Pomodoro",
                color=discord.Color.red()
            )
            embed.add_field(
                name="!pomodoro",
                value="Inicia um timer Pomodoro (25min estudo + 5min pausa)",
                inline=False
            )
            embed.add_field(
                name="!pomodoro_custom <estudo> <pausa>",
                value="Timer personalizado\nExemplo: `!pomodoro_custom 50 10`",
                inline=False
            )
            embed.add_field(
                name="!parar_pomodoro",
                value="Para o timer atual",
                inline=False
            )
            await ctx.send(embed=embed)
        
        elif categoria.lower() == 'progresso':
            embed = discord.Embed(
                title="📊 Comandos de Progresso",
                color=discord.Color.purple()
            )
            embed.add_field(
                name="!registrar <minutos> [notas]",
                value="Registra tempo de estudo\nExemplo: `!registrar 60 Estudei cálculo`",
                inline=False
            )
            embed.add_field(
                name="!meu_progresso",
                value="Mostra suas estatísticas de estudo",
                inline=False
            )
            embed.add_field(
                name="!ranking",
                value="Mostra o ranking de estudantes",
                inline=False
            )
            await ctx.send(embed=embed)
        
        elif categoria.lower() == 'dicas':
            embed = discord.Embed(
                title="🧠 Comandos de Dicas de Estudo",
                color=discord.Color.gold()
            )
            embed.add_field(
                name="!dicas",
                value="Mostra todas as técnicas científicas de estudo disponíveis",
                inline=False
            )
            embed.add_field(
                name="!dicas <número>",
                value="Mostra detalhes de uma técnica específica\nExemplo: `!dicas 1`",
                inline=False
            )
            embed.add_field(
                name="!dica_aleatoria",
                value="Recebe uma dica aleatória de estudo",
                inline=False
            )
            embed.set_footer(text="Use as técnicas científicas para estudar melhor e não esquecer!")
            await ctx.send(embed=embed)
        
        elif categoria.lower() == 'sabi':
            LINK_SABI = "https://wandersonhenriquerei1760743066000.0991967.meusitehostgator.com.br/"
            embed = discord.Embed(
                title="📖 Sistema SABi - Busca Inteligente de Livros",
                description="O **SABi** (Sistema Acadêmico de Biblioteca Inteligente) é uma plataforma criada para facilitar sua vida acadêmica!",
                color=discord.Color.teal()
            )
            embed.add_field(
                name="🔍 Busca Rápida de Livros",
                value="Encontre livros na biblioteca da faculdade em segundos e veja a localização exata nas prateleiras.",
                inline=False
            )
            embed.add_field(
                name="📚 Estante Pessoal",
                value="Organize os livros que você leu, faça anotações e crie sua biblioteca pessoal de estudos.",
                inline=False
            )
            embed.add_field(
                name="🤝 SABi Connect",
                value="Preencha o formulário (nome, email, área) e receba o link do nosso servidor Discord para conectar com pessoas da sua área!",
                inline=False
            )
            embed.add_field(
                name="🌐 Acesse o SABi",
                value=f"[Clique aqui para acessar]({LINK_SABI})",
                inline=False
            )
            embed.set_footer(text="💡 Nota: O SABi mostra a localização dos livros, mas a disponibilidade e empréstimo são feitos pelo sistema interno da faculdade.")
            await ctx.send(embed=embed)
        
        else:
            await ctx.send(f"❌ Categoria `{categoria}` não encontrada. Use `!ajuda` para ver as categorias disponíveis.")

async def setup(bot):
    await bot.add_cog(HelpCommandsCog(bot))
