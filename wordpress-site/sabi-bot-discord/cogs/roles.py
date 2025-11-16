import discord
from discord.ext import commands

REACTION_ROLES = {
    '💻': 1432572942998442094,
    '🏗️': 1432574209325793352,
    '⚕️': 1432574305665028287,
    '⚖️': 1432574405375950868,
    '🍎': 1432574479233585345,
    '💼': 1432574558170517625,
    '📣': 1432574665590706216,
    '🏛️': 1432574747903922297,
}

ID_CANAL_CARGOS = 1432581655964422235
LINK_SABI_INICIAL = "https://wandersonhenriquerei1760743066000.0991967.meusitehostgator.com.br/"

class RolesCog(commands.Cog):
    def __init__(self, bot):
        self.bot = bot
    
    @commands.command()
    @commands.has_permissions(administrator=True)
    async def gerar_cargos(self, ctx):
        if ctx.channel.id != ID_CANAL_CARGOS:
            await ctx.send("❌ Use este comando no canal de seleção de áreas!", delete_after=10)
            return

        embed = discord.Embed(
            title="🎓 SELECIONE SUA ÁREA DE ESTUDO - SABi CONNECT",
            description="Reaja com o emoji correspondente para receber seu cargo.",
            color=discord.Color.green()
        )

        for emoji, role_id in REACTION_ROLES.items():
            role = ctx.guild.get_role(role_id)
            if role:
                embed.add_field(name=f"{emoji} {role.name}", value="────────────", inline=False)
            else:
                embed.add_field(name=f"{emoji} Cargo não encontrado ⚠️", value=f"ID: {role_id}", inline=False)

        embed.set_footer(text="Reaja com o emoji para entrar no grupo 🎓")
        msg = await ctx.send(embed=embed)

        for emoji in REACTION_ROLES.keys():
            await msg.add_reaction(emoji)

        await ctx.send("✅ Mensagem de seleção de cargos criada!", delete_after=5)
    
    @commands.command()
    @commands.has_permissions(administrator=True)
    async def boasvindas(self, ctx):
        embed = discord.Embed(
            title="👋 BEM-VINDO(A) AO SABi CONNECT!",
            description="Sua comunidade acadêmica de conhecimento colaborativo! 📚",
            color=discord.Color.purple()
        )
        
        embed.add_field(
            name="🎓 Como Começar",
            value=(
                "1️⃣ Vá até **#escolha-sua-área** e reaja com o emoji da sua área de estudo\n"
                "2️⃣ Explore os canais específicos da sua área\n"
                "3️⃣ Participe das sessões de estudo em grupo!"
            ),
            inline=False
        )
        
        embed.add_field(
            name="📖 Sistema SABi - Busca de Livros",
            value=(
                f"Use nosso [Sistema SABi]({LINK_SABI_INICIAL}) para:\n"
                "• Buscar livros rapidamente na biblioteca\n"
                "• Ver a localização exata dos livros\n"
                "• Organizar sua estante pessoal com anotações"
            ),
            inline=False
        )
        
        embed.add_field(
            name="🧠 Dicas para Estudar Melhor",
            value=(
                "• Use **!dicas** para ver técnicas científicas de estudo\n"
                "• Use **!pomodoro** para sessões de estudo focadas (25min)\n"
                "• Use **!lembrete** para não esquecer de estudar\n"
                "• Use **!ranking** para ver os estudantes mais dedicados"
            ),
            inline=False
        )
        
        embed.add_field(
            name="✨ Comandos Úteis",
            value="Digite **!ajuda** para ver todos os comandos disponíveis",
            inline=False
        )
        
        embed.set_footer(text="💡 Estude em grupo, ajude os colegas e cresça junto com a comunidade!")
        await ctx.send(embed=embed)
    
    @commands.command()
    @commands.has_permissions(administrator=True)
    async def adicionar_area(self, ctx):
        def check(m): return m.author == ctx.author and m.channel == ctx.channel

        await ctx.send("✏️ Qual é o nome da nova área?")
        try:
            nome_area = (await self.bot.wait_for('message', check=check, timeout=60)).content
        except:
            await ctx.send("⏱️ Tempo esgotado.")
            return

        await ctx.send(f"🔹 Envie o emoji que será usado para a área `{nome_area}`:")
        try:
            emoji_area = (await self.bot.wait_for('message', check=check, timeout=60)).content
        except:
            await ctx.send("⏱️ Tempo esgotado.")
            return

        await ctx.send(f"🔹 Envie o ID do cargo do Discord para a área `{nome_area}`:")
        try:
            role_id = int((await self.bot.wait_for('message', check=check, timeout=60)).content)
        except:
            await ctx.send("❌ ID inválido ou tempo esgotado.")
            return

        REACTION_ROLES[emoji_area] = role_id
        await ctx.send(f"✅ Área **{nome_area}** adicionada! Emoji: {emoji_area} | Cargo ID: {role_id}")
    
    @commands.command()
    @commands.has_permissions(administrator=True)
    async def remover_area(self, ctx):
        if not REACTION_ROLES:
            await ctx.send("⚠️ Não há áreas cadastradas.")
            return
        lista_areas = "\n".join([f"{emoji} → ID: {role_id}" for emoji, role_id in REACTION_ROLES.items()])
        await ctx.send(f"📝 Áreas atuais:\n{lista_areas}\nEnvie o emoji da área que deseja remover:")

        def check(m): return m.author == ctx.author and m.channel == ctx.channel
        try:
            emoji_remover = (await self.bot.wait_for('message', check=check, timeout=60)).content
        except:
            await ctx.send("⏱️ Tempo esgotado.")
            return

        if emoji_remover in REACTION_ROLES:
            REACTION_ROLES.pop(emoji_remover)
            await ctx.send(f"✅ Área com emoji `{emoji_remover}` removida!")
        else:
            await ctx.send("❌ Emoji não encontrado.")
    
    @commands.Cog.listener()
    async def on_raw_reaction_add(self, payload):
        if payload.channel_id != ID_CANAL_CARGOS or payload.user_id == self.bot.user.id:
            return
        emoji = str(payload.emoji)
        if emoji in REACTION_ROLES:
            guild = self.bot.get_guild(payload.guild_id)
            if not guild:
                return
            member = guild.get_member(payload.user_id)
            if not member:
                try:
                    member = await guild.fetch_member(payload.user_id)
                except:
                    return
            role = guild.get_role(REACTION_ROLES[emoji])
            if member and role:
                await member.add_roles(role)
    
    @commands.Cog.listener()
    async def on_raw_reaction_remove(self, payload):
        if payload.channel_id != ID_CANAL_CARGOS or payload.user_id == self.bot.user.id:
            return
        emoji = str(payload.emoji)
        if emoji in REACTION_ROLES:
            guild = self.bot.get_guild(payload.guild_id)
            member = await guild.fetch_member(payload.user_id)
            role = guild.get_role(REACTION_ROLES[emoji])
            if member and role:
                await member.remove_roles(role)

async def setup(bot):
    await bot.add_cog(RolesCog(bot))
