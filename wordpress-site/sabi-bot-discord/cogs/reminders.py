import discord
from discord.ext import commands
from datetime import datetime, timedelta
import re
import pytz
from database import Database

class RemindersCog(commands.Cog):
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
    async def lembrete(self, ctx, tempo: str, *, mensagem: str):
        delta = self.parse_time(tempo)
        if not delta:
            await ctx.send("❌ Formato de tempo inválido! Use: 30m, 2h, ou 1d")
            return
        
        brasilia_tz = pytz.timezone('America/Sao_Paulo')
        remind_at = datetime.now(brasilia_tz) + delta
        self.db.add_reminder(ctx.author.id, mensagem, remind_at.strftime('%Y-%m-%d %H:%M:%S'))
        
        embed = discord.Embed(
            title="✅ Lembrete Criado!",
            description=f"**Mensagem:** {mensagem}\n**Quando:** {remind_at.strftime('%d/%m/%Y %H:%M')}",
            color=discord.Color.green()
        )
        await ctx.send(embed=embed)
    
    @commands.command()
    async def meus_lembretes(self, ctx):
        reminders = self.db.get_user_reminders(ctx.author.id)
        
        if not reminders:
            await ctx.send("📭 Você não tem lembretes pendentes.")
            return
        
        embed = discord.Embed(
            title="⏰ Seus Lembretes",
            color=discord.Color.blue()
        )
        
        for reminder in reminders:
            remind_time = datetime.strptime(reminder['remind_at'], '%Y-%m-%d %H:%M:%S')
            embed.add_field(
                name=f"ID: {reminder['id']} - {remind_time.strftime('%d/%m/%Y %H:%M')}",
                value=reminder['message'],
                inline=False
            )
        
        await ctx.send(embed=embed)
    
    @commands.command()
    async def cancelar_lembrete(self, ctx, reminder_id: int):
        if self.db.delete_reminder(reminder_id, ctx.author.id):
            await ctx.send(f"✅ Lembrete #{reminder_id} cancelado!")
        else:
            await ctx.send("❌ Lembrete não encontrado ou você não tem permissão para cancelá-lo.")

async def setup(bot):
    await bot.add_cog(RemindersCog(bot))
