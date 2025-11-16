import discord
from discord.ext import commands
from datetime import datetime, timedelta
import re
import pytz
from database import Database

class EventsCog(commands.Cog):
    def __init__(self, bot):
        self.bot = bot
        self.db = Database()
    
    def parse_time(self, time_str):
        pattern = r'(\d+)([mhd])'
        match = re.match(pattern, time_str.lower())
        if not match:
            return None
        
        amount = int(match.group(1))
        unit = match.group(2)
        
        if unit == 'm':
            return timedelta(minutes=amount)
        elif unit == 'h':
            return timedelta(hours=amount)
        elif unit == 'd':
            return timedelta(days=amount)
        return None
    
    @commands.command()
    async def agendar(self, ctx, tempo: str, *, detalhes: str):
        delta = self.parse_time(tempo)
        if not delta:
            await ctx.send("❌ Formato de tempo inválido! Use: 30m, 2h, ou 1d")
            return
        
        parts = detalhes.split('|', 1)
        if len(parts) != 2:
            await ctx.send("❌ Formato inválido! Use: `!agendar <tempo> <título> | <descrição>`")
            return
        
        title = parts[0].strip()
        description = parts[1].strip()
        brasilia_tz = pytz.timezone('America/Sao_Paulo')
        event_time = datetime.now(brasilia_tz) + delta
        
        self.db.add_event(
            ctx.channel.id,
            ctx.author.id,
            title,
            description,
            event_time.strftime('%Y-%m-%d %H:%M:%S')
        )
        
        embed = discord.Embed(
            title="📅 Sessão de Estudo Agendada!",
            description=f"**{title}**\n{description}",
            color=discord.Color.green()
        )
        embed.add_field(name="Quando", value=event_time.strftime('%d/%m/%Y %H:%M'), inline=False)
        embed.add_field(name="Criado por", value=ctx.author.mention, inline=False)
        embed.set_footer(text="Você receberá uma notificação 5 minutos antes!")
        
        await ctx.send(embed=embed)
    
    @commands.command()
    async def eventos(self, ctx):
        events = self.db.get_channel_events(ctx.channel.id)
        
        if not events:
            await ctx.send("📭 Não há eventos agendados neste canal.")
            return
        
        embed = discord.Embed(
            title="📅 Eventos Agendados",
            color=discord.Color.blue()
        )
        
        for event in events[:10]:
            event_time = datetime.strptime(event['event_time'], '%Y-%m-%d %H:%M:%S')
            creator = await self.bot.fetch_user(event['creator_id'])
            embed.add_field(
                name=f"{event['title']} - {event_time.strftime('%d/%m %H:%M')}",
                value=f"{event['description']}\nCriado por: {creator.mention if creator else 'Desconhecido'}",
                inline=False
            )
        
        await ctx.send(embed=embed)

async def setup(bot):
    await bot.add_cog(EventsCog(bot))
