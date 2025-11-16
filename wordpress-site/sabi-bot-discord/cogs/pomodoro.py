import discord
from discord.ext import commands
import asyncio

class PomodoroCog(commands.Cog):
    def __init__(self, bot):
        self.bot = bot
        self.active_timers = {}
    
    @commands.command()
    async def pomodoro(self, ctx):
        await self.start_pomodoro_timer(ctx, 25, 5)
    
    @commands.command()
    async def pomodoro_custom(self, ctx, estudo: int, pausa: int):
        if estudo < 1 or estudo > 120:
            await ctx.send("❌ Tempo de estudo deve estar entre 1 e 120 minutos!")
            return
        if pausa < 1 or pausa > 60:
            await ctx.send("❌ Tempo de pausa deve estar entre 1 e 60 minutos!")
            return
        
        await self.start_pomodoro_timer(ctx, estudo, pausa)
    
    async def start_pomodoro_timer(self, ctx, study_minutes, break_minutes):
        user_id = ctx.author.id
        
        if user_id in self.active_timers:
            await ctx.send("⚠️ Você já tem um timer ativo! Use `!parar_pomodoro` primeiro.")
            return
        
        self.active_timers[user_id] = True
        
        embed = discord.Embed(
            title="🍅 Pomodoro Iniciado!",
            description=f"**Tempo de estudo:** {study_minutes} minutos\n**Tempo de pausa:** {break_minutes} minutos",
            color=discord.Color.red()
        )
        embed.set_footer(text="Bons estudos!")
        await ctx.send(embed=embed)
        
        await asyncio.sleep(study_minutes * 60)
        
        if user_id not in self.active_timers:
            return
        
        try:
            embed = discord.Embed(
                title="⏰ Tempo de Pausa!",
                description=f"Você estudou por {study_minutes} minutos!\nAgora descanse por {break_minutes} minutos.",
                color=discord.Color.green()
            )
            await ctx.author.send(embed=embed)
            await ctx.send(f"{ctx.author.mention} Hora da pausa! 🎉")
        except:
            await ctx.send(f"{ctx.author.mention} Hora da pausa! 🎉 (Não consegui enviar DM)")
        
        await asyncio.sleep(break_minutes * 60)
        
        if user_id not in self.active_timers:
            return
        
        try:
            embed = discord.Embed(
                title="🍅 Pausa Terminada!",
                description=f"Sua pausa de {break_minutes} minutos acabou!\nPronto para mais um ciclo de estudos?",
                color=discord.Color.blue()
            )
            await ctx.author.send(embed=embed)
            await ctx.send(f"{ctx.author.mention} Pausa terminada! Bora estudar! 📚")
        except:
            await ctx.send(f"{ctx.author.mention} Pausa terminada! Bora estudar! 📚")
        
        if user_id in self.active_timers:
            del self.active_timers[user_id]
    
    @commands.command()
    async def parar_pomodoro(self, ctx):
        user_id = ctx.author.id
        if user_id in self.active_timers:
            del self.active_timers[user_id]
            await ctx.send("⏹️ Timer Pomodoro parado!")
        else:
            await ctx.send("❌ Você não tem nenhum timer ativo.")

async def setup(bot):
    await bot.add_cog(PomodoroCog(bot))
