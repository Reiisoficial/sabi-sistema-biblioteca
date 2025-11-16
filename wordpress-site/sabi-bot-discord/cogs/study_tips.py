import discord
from discord.ext import commands
import random

STUDY_TIPS = [
    {
        "title": "📚 Repetição Espaçada",
        "description": "Revise o conteúdo em intervalos crescentes: depois de 1 dia, 3 dias, 1 semana, 2 semanas.",
        "why": "Nosso cérebro retém melhor quando revisitamos informações periodicamente, fortalecendo as conexões neurais."
    },
    {
        "title": "🧠 Técnica Feynman",
        "description": "Explique o assunto em voz alta como se estivesse ensinando para uma criança.",
        "why": "Se você não consegue explicar de forma simples, significa que ainda não entendeu completamente."
    },
    {
        "title": "🗺️ Mapas Mentais",
        "description": "Crie diagramas visuais conectando conceitos relacionados com linhas e cores.",
        "why": "O cérebro processa informações visuais 60.000x mais rápido que texto."
    },
    {
        "title": "🍅 Técnica Pomodoro",
        "description": "Estude 25min com foco total, descanse 5min. Use !pomodoro para começar!",
        "why": "Períodos curtos mantêm a concentração alta e previnem fadiga mental."
    },
    {
        "title": "✍️ Teste Ativo",
        "description": "Em vez de só reler, faça resumos, questionários e explique sem consultar.",
        "why": "Recuperar informações da memória é mais eficaz que reler passivamente."
    },
    {
        "title": "🎯 Método Cornell",
        "description": "Divida a página: Esquerda = perguntas-chave, Direita = notas, Baixo = resumo.",
        "why": "Organiza informações para revisão rápida e estimula pensamento crítico."
    },
    {
        "title": "🧩 Intercalação",
        "description": "Alterne entre diferentes assuntos/tipos de problema ao estudar.",
        "why": "Melhora a capacidade de distinguir conceitos e aplicar conhecimento em novos contextos."
    },
    {
        "title": "💤 Sono e Memória",
        "description": "Durma bem! O sono consolida a memória de longo prazo.",
        "why": "Durante o sono, o cérebro processa e armazena o que você aprendeu durante o dia."
    },
    {
        "title": "📝 Resumos Manuscritos",
        "description": "Escreva resumos à mão em vez de digitar.",
        "why": "Escrever à mão ativa mais áreas do cérebro, melhorando compreensão e retenção."
    },
    {
        "title": "🎵 Ambiente Adequado",
        "description": "Estude em local calmo, iluminado, sem distrações. Desligue notificações!",
        "why": "O ambiente afeta diretamente sua capacidade de concentração e produtividade."
    }
]

class StudyTipsCog(commands.Cog):
    def __init__(self, bot):
        self.bot = bot

    @commands.command()
    async def dicas(self, ctx, numero: int = None):
        if numero is not None:
            if numero < 1 or numero > len(STUDY_TIPS):
                await ctx.send(f"❌ Número inválido! Escolha entre 1 e {len(STUDY_TIPS)}.")
                return

            tip = STUDY_TIPS[numero - 1]
            embed = discord.Embed(
                title=tip['title'],
                description=tip['description'],
                color=discord.Color.blue()
            )
            embed.add_field(name="💡 Por que funciona?", value=tip['why'], inline=False)
            embed.set_footer(text=f"Dica {numero} de {len(STUDY_TIPS)} | Use !dicas para ver todas")
            await ctx.send(embed=embed)

        else:
            embed = discord.Embed(
                title="📚 GUIA DE TÉCNICAS DE ESTUDO CIENTÍFICAS",
                description=(
                    "Antes de ver as técnicas, você pode assistir ao vídeo da Professora Ivone, "
                    "onde ela explica como funciona um grupo de estudo e por que ele ajuda tanto no aprendizado!\n\n"
                    "🎥 **Assista aqui:** [Estudo em Grupo – Professora Ivone](https://youtu.be/JY0McS7PRrA?si=oRuYVZCySXStXcIC)\n\n"
                    "Agora sim, vamos às técnicas!"
                ),
                color=discord.Color.gold()
            )

            for i, tip in enumerate(STUDY_TIPS, 1):
                embed.add_field(
                    name=f"{i}. {tip['title']}",
                    value=tip['description'][:100] + "...",
                    inline=False
                )

            embed.add_field(
                name="📖 Como usar",
                value="Use `!dicas <número>` para ver detalhes de uma técnica específica.\nExemplo: `!dicas 1`",
                inline=False
            )

            embed.set_footer(text="Dica: Use !pomodoro para começar uma sessão de estudo focada!")
            await ctx.send(embed=embed)

    @commands.command()
    async def dica_aleatoria(self, ctx):
        tip = random.choice(STUDY_TIPS)
        embed = discord.Embed(
            title=f"🎲 {tip['title']}",
            description=tip['description'],
            color=discord.Color.purple()
        )
        embed.add_field(name="💡 Por que funciona?", value=tip['why'], inline=False)
        embed.set_footer(text="Use !dicas para ver todas as técnicas disponíveis")
        await ctx.send(embed=embed)


async def setup(bot):
    await bot.add_cog(StudyTipsCog(bot))
