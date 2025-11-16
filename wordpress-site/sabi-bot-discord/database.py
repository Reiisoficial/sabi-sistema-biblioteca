import sqlite3
from datetime import datetime, timedelta
import os
import pytz

class Database:
    def __init__(self, db_name='sabibot.db'):
        self.db_name = db_name
        self.init_database()
    
    def get_connection(self):
        return sqlite3.connect(self.db_name)
    
    def init_database(self):
        conn = self.get_connection()
        cursor = conn.cursor()
        
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS reminders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                message TEXT NOT NULL,
                remind_at TIMESTAMP NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                sent BOOLEAN DEFAULT 0
            )
        ''')
        
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS events (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                channel_id INTEGER NOT NULL,
                creator_id INTEGER NOT NULL,
                title TEXT NOT NULL,
                description TEXT,
                event_time TIMESTAMP NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                notified BOOLEAN DEFAULT 0
            )
        ''')
        
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS study_sessions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                duration_minutes INTEGER NOT NULL,
                date DATE NOT NULL,
                notes TEXT
            )
        ''')
        
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS user_xp (
                user_id INTEGER PRIMARY KEY,
                xp INTEGER DEFAULT 0,
                level INTEGER DEFAULT 1,
                total_study_minutes INTEGER DEFAULT 0
            )
        ''')
        
        conn.commit()
        conn.close()
    
    def add_reminder(self, user_id, message, remind_at):
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute(
            'INSERT INTO reminders (user_id, message, remind_at) VALUES (?, ?, ?)',
            (user_id, message, remind_at)
        )
        conn.commit()
        reminder_id = cursor.lastrowid
        conn.close()
        return reminder_id
    
    def get_pending_reminders(self):
        conn = self.get_connection()
        conn.row_factory = sqlite3.Row
        cursor = conn.cursor()
        brasilia_tz = pytz.timezone('America/Sao_Paulo')
        now = datetime.now(brasilia_tz).strftime('%Y-%m-%d %H:%M:%S')
        cursor.execute(
            'SELECT * FROM reminders WHERE sent = 0 AND remind_at <= ?',
            (now,)
        )
        reminders = [dict(row) for row in cursor.fetchall()]
        conn.close()
        return reminders
    
    def mark_reminder_sent(self, reminder_id):
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute('UPDATE reminders SET sent = 1 WHERE id = ?', (reminder_id,))
        conn.commit()
        conn.close()
    
    def get_user_reminders(self, user_id):
        conn = self.get_connection()
        conn.row_factory = sqlite3.Row
        cursor = conn.cursor()
        cursor.execute(
            'SELECT * FROM reminders WHERE user_id = ? AND sent = 0 ORDER BY remind_at',
            (user_id,)
        )
        reminders = [dict(row) for row in cursor.fetchall()]
        conn.close()
        return reminders
    
    def delete_reminder(self, reminder_id, user_id):
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute('DELETE FROM reminders WHERE id = ? AND user_id = ?', (reminder_id, user_id))
        deleted = cursor.rowcount
        conn.commit()
        conn.close()
        return deleted > 0
    
    def add_event(self, channel_id, creator_id, title, description, event_time):
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute(
            'INSERT INTO events (channel_id, creator_id, title, description, event_time) VALUES (?, ?, ?, ?, ?)',
            (channel_id, creator_id, title, description, event_time)
        )
        conn.commit()
        event_id = cursor.lastrowid
        conn.close()
        return event_id
    
    def get_upcoming_events(self):
        conn = self.get_connection()
        conn.row_factory = sqlite3.Row
        cursor = conn.cursor()
        brasilia_tz = pytz.timezone('America/Sao_Paulo')
        now = datetime.now(brasilia_tz).strftime('%Y-%m-%d %H:%M:%S')
        future = (datetime.now(brasilia_tz) + timedelta(minutes=5)).strftime('%Y-%m-%d %H:%M:%S')
        cursor.execute(
            'SELECT * FROM events WHERE notified = 0 AND event_time BETWEEN ? AND ?',
            (now, future)
        )
        events = [dict(row) for row in cursor.fetchall()]
        conn.close()
        return events
    
    def mark_event_notified(self, event_id):
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute('UPDATE events SET notified = 1 WHERE id = ?', (event_id,))
        conn.commit()
        conn.close()
    
    def get_channel_events(self, channel_id):
        conn = self.get_connection()
        conn.row_factory = sqlite3.Row
        cursor = conn.cursor()
        brasilia_tz = pytz.timezone('America/Sao_Paulo')
        now = datetime.now(brasilia_tz).strftime('%Y-%m-%d %H:%M:%S')
        cursor.execute(
            'SELECT * FROM events WHERE channel_id = ? AND event_time >= ? ORDER BY event_time',
            (channel_id, now)
        )
        events = [dict(row) for row in cursor.fetchall()]
        conn.close()
        return events
    
    def add_study_session(self, user_id, duration_minutes, notes=None):
        conn = self.get_connection()
        cursor = conn.cursor()
        brasilia_tz = pytz.timezone('America/Sao_Paulo')
        today = datetime.now(brasilia_tz).date()
        cursor.execute(
            'INSERT INTO study_sessions (user_id, duration_minutes, date, notes) VALUES (?, ?, ?, ?)',
            (user_id, duration_minutes, today, notes)
        )
        conn.commit()
        conn.close()
        self.add_xp(user_id, duration_minutes)
    
    def get_user_stats(self, user_id):
        conn = self.get_connection()
        cursor = conn.cursor()
        
        cursor.execute(
            'SELECT SUM(duration_minutes) as total FROM study_sessions WHERE user_id = ?',
            (user_id,)
        )
        total_minutes = cursor.fetchone()[0] or 0
        
        cursor.execute(
            'SELECT xp, level FROM user_xp WHERE user_id = ?',
            (user_id,)
        )
        result = cursor.fetchone()
        xp = result[0] if result else 0
        level = result[1] if result else 1
        
        conn.close()
        return {
            'total_minutes': total_minutes,
            'xp': xp,
            'level': level
        }
    
    def add_xp(self, user_id, minutes):
        conn = self.get_connection()
        cursor = conn.cursor()
        
        xp_gained = minutes * 10
        
        cursor.execute('SELECT xp, level FROM user_xp WHERE user_id = ?', (user_id,))
        result = cursor.fetchone()
        
        if result:
            current_xp = result[0]
            current_level = result[1]
            new_xp = current_xp + xp_gained
            new_level = 1 + (new_xp // 1000)
            
            cursor.execute(
                'UPDATE user_xp SET xp = ?, level = ?, total_study_minutes = total_study_minutes + ? WHERE user_id = ?',
                (new_xp, new_level, minutes, user_id)
            )
        else:
            new_xp = xp_gained
            new_level = 1 + (new_xp // 1000)
            cursor.execute(
                'INSERT INTO user_xp (user_id, xp, level, total_study_minutes) VALUES (?, ?, ?, ?)',
                (user_id, new_xp, new_level, minutes)
            )
        
        conn.commit()
        conn.close()
        return new_level
    
    def get_leaderboard(self, limit=10):
        conn = self.get_connection()
        conn.row_factory = sqlite3.Row
        cursor = conn.cursor()
        cursor.execute(
            'SELECT user_id, xp, level, total_study_minutes FROM user_xp ORDER BY xp DESC LIMIT ?',
            (limit,)
        )
        leaderboard = [dict(row) for row in cursor.fetchall()]
        conn.close()
        return leaderboard
