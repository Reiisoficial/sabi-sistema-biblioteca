import discord
from discord.ext import commands
import pytz
from database import Database

class ProgressCog(commands.Cog):
    def __init__(self, bot):
        self.bot = bot
        self.db = Database()
    
    @commands.command()
    async def registrar(self, ctx, minutos: int, *, notas: str = ""):
        if minutos < 1 or minutos > 600:
            await ctx.send("❌ Por favor, registre entre 1 e 600 minutos de estudo.")
            return
        
        self.db.add_study_session(ctx.author.id, minutos, notas if notas else None)
        stats = self.db.get_user_stats(ctx.author.id)
        
        embed = discord.Embed(
            title="✅ Estudo Registrado!",
            description=f"Você registrou **{minutos} minutos** de estudo!",
            color=discord.Color.green()
        )
        
        if notas and notas.strip():
            embed.add_field(name="Notas", value=notas, inline=False)
        
        xp_gained = minutos * 10
        embed.add_field(name="XP Ganho", value=f"+{xp_gained} XP", inline=True)
        embed.add_field(name="Nível Atual", value=f"Nível {stats['level']}", inline=True)
        
        current_level_xp = stats['xp'] % 1000
        progress = current_level_xp / 10
        embed.add_field(
            name="Progresso para o próximo nível",
            value=f"{current_level_xp}/1000 XP ({progress:.1f}%)",
            inline=False
        )
        
        await ctx.send(embed=embed)
    
    @commands.command()
    async def meu_progresso(self, ctx):
        stats = self.db.get_user_stats(ctx.author.id)
        
        if stats['total_minutes'] == 0:
            await ctx.send("📭 Você ainda não registrou nenhum tempo de estudo. Use `!registrar <minutos>` para começar!")
            return
        
        hours = stats['total_minutes'] // 60
        minutes = stats['total_minutes'] % 60
        
        embed = discord.Embed(
            title=f"📊 Progresso de {ctx.author.display_name}",
            color=discord.Color.purple()
        )
        
        embed.add_field(name="⏱️ Tempo Total de Estudo", value=f"{hours}h {minutes}min", inline=False)
        embed.add_field(name="⭐ XP Total", value=f"{stats['xp']} XP", inline=True)
        embed.add_field(name="🏆 Nível", value=f"Nível {stats['level']}", inline=True)
        
        current_level_xp = stats['xp'] % 1000
        progress = current_level_xp / 10
        embed.add_field(
            name="📈 Progresso",
            value=f"{current_level_xp}/1000 XP para o próximo nível ({progress:.1f}%)",
            inline=False
        )
        
        embed.set_thumbnail(url=ctx.author.display_avatar.url)
        
        await ctx.send(embed=embed)
    
    @commands.command()
    async def ranking(self, ctx):
        leaderboard = self.db.get_leaderboard(10)
        
        if not leaderboard:
            await ctx.send("📭 Ainda não há dados de ranking.")
            return
        
        embed = discord.Embed(
            title="🏆 Ranking de Estudantes - SABi CONNECT",
            description="Top 10 estudantes mais dedicados!",
            color=discord.Color.gold()
        )
        
        medals = ['🥇', '🥈', '🥉']
        
        for i, entry in enumerate(leaderboard):
            try:
                user = await self.bot.fetch_user(entry['user_id'])
                name = user.display_name if user else f"Usuário {entry['user_id']}"
            except:
                name = f"Usuário {entry['user_id']}"
            
            medal = medals[i] if i < 3 else f"#{i+1}"
            hours = entry['total_study_minutes'] // 60
            minutes = entry['total_study_minutes'] % 60
            
            embed.add_field(
                name=f"{medal} {name}",
                value=f"Nível {entry['level']} | {entry['xp']} XP | {hours}h {minutes}min estudados",
                inline=False
            )
        
        await ctx.send(embed=embed)

async def setup(bot):
    await bot.add_cog(ProgressCog(bot))
