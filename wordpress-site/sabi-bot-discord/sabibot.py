import discord
from discord.ext import commands
import os
from dotenv import load_dotenv
import asyncio
from database import Database
from webserver import keep_alive

load_dotenv()

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

intents = discord.Intents.default()
intents.message_content = True
intents.members = True
intents.reactions = True

bot = commands.Bot(command_prefix='!', intents=intents)
db = Database()

@bot.event
async def on_ready():
    print(f"✅ SABiBot está online como {bot.user}")
    print(f"📋 Monitorando o canal de cargos ID: {ID_CANAL_CARGOS}")
    await bot.change_presence(activity=discord.Game(name="Ajudando alunos no SABi 📚"))
    bot.loop.create_task(check_reminders())
    bot.loop.create_task(check_events())

async def check_reminders():
    await bot.wait_until_ready()
    while not bot.is_closed():
        try:
            reminders = db.get_pending_reminders()
            for reminder in reminders:
                user = await bot.fetch_user(reminder['user_id'])
                if user:
                    embed = discord.Embed(
                        title="⏰ LEMBRETE!",
                        description=reminder['message'],
                        color=discord.Color.orange()
                    )
                    await user.send(embed=embed)
                db.mark_reminder_sent(reminder['id'])
        except Exception as e:
            print(f"Erro ao verificar lembretes: {e}")
        await asyncio.sleep(60)

async def check_events():
    await bot.wait_until_ready()
    while not bot.is_closed():
        try:
            events = db.get_upcoming_events()
            for event in events:
                channel = bot.get_channel(event['channel_id'])
                if channel and isinstance(channel, discord.TextChannel):
                    embed = discord.Embed(
                        title="📅 SESSÃO DE ESTUDO COMEÇANDO!",
                        description=f"**{event['title']}**\n{event['description']}",
                        color=discord.Color.blue()
                    )
                    await channel.send(f"@everyone", embed=embed)
                db.mark_event_notified(event['id'])
        except Exception as e:
            print(f"Erro ao verificar eventos: {e}")
        await asyncio.sleep(60)

async def load_cogs():
    await bot.load_extension('cogs.roles')
    await bot.load_extension('cogs.help_commands')
    await bot.load_extension('cogs.reminders')
    await bot.load_extension('cogs.events')
    await bot.load_extension('cogs.pomodoro')
    await bot.load_extension('cogs.progress')
    await bot.load_extension('cogs.study_tips')

async def main():
    token = os.getenv('DISCORD_TOKEN')
    if not token:
        print("❌ ERRO: Token do Discord não encontrado! Configure DISCORD_TOKEN nas Secrets.")
        return
    async with bot:
        await load_cogs()
        await bot.start(token)

if __name__ == "__main__":
    keep_alive()
    asyncio.run(main())
