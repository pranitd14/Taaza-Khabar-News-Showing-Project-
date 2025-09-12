from flask import Flask, request, jsonify, redirect, make_response, session, render_template_string
from flask_cors import CORS
import threading
import webbrowser
import requests
import os
import json
import sqlite3
from datetime import datetime
from dotenv import load_dotenv

load_dotenv()

API_KEY = os.getenv('API_KEY')
NEWS_API_URL = 'https://newsapi.org/v2/everything'
DB_NAME = 'taaza_khabar.db'

app = Flask(__name__)
app.secret_key = 'taaza_khabar_secret_key'
CORS(app)

# Initialize database
def init_db():
    conn = sqlite3.connect(DB_NAME)
    cursor = conn.cursor()
    
    # Users table
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ''')
    
    # Search history table
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS search_history (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL,
            query TEXT NOT NULL,
            results_count INTEGER,
            timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ''')
    
    # Feedback table
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS feedback (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            email TEXT,
            rating INTEGER NOT NULL,
            message TEXT NOT NULL,
            timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ''')
    
    # Tags table
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS tags (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            tag_name TEXT UNIQUE NOT NULL,
            tag_color TEXT NOT NULL
        )
    ''')
    
    # Insert default tags if table is empty
    cursor.execute('SELECT COUNT(*) FROM tags')
    if cursor.fetchone()[0] == 0:
        default_tags = [
            ('Technology', '#3498db'),
            ('Sports', '#e74c3c'),
            ('Business', '#2ecc71'),
            ('Health', '#f39c12'),
            ('Science', '#9b59b6'),
            ('Entertainment', '#e67e22'),
            ('Politics', '#34495e'),
            ('World', '#1abc9c')
        ]
        cursor.executemany('INSERT INTO tags (tag_name, tag_color) VALUES (?, ?)', default_tags)
    
    conn.commit()
    conn.close()

init_db()

@app.route('/register', methods=['POST'])
def register():
    try:
        data = request.get_json()
        if not data:
            return jsonify({'status': 'error', 'message': 'No data provided'}), 400
            
        username = data.get('username')
        password = data.get('password')
        
        if not username or not password:
            return jsonify({'status': 'error', 'message': 'Username and password required'}), 400
        
        conn = sqlite3.connect(DB_NAME)
        cursor = conn.cursor()
        
        try:
            cursor.execute('INSERT INTO users (username, password) VALUES (?, ?)', (username, password))
            conn.commit()
            return jsonify({'status': 'success', 'message': 'Registration successful'})
        except sqlite3.IntegrityError:
            return jsonify({'status': 'error', 'message': 'Username already exists'})
        finally:
            conn.close()
    except Exception as e:
        return jsonify({'status': 'error', 'message': str(e)}), 500

@app.route('/login', methods=['POST'])
def login():
    try:
        data = request.get_json()
        if not data:
            return jsonify({'status': 'error', 'message': 'No data provided'}), 400
            
        username = data.get('username')
        password = data.get('password')
        
        if not username or not password:
            return jsonify({'status': 'error', 'message': 'Username and password required'}), 400
        
        conn = sqlite3.connect(DB_NAME)
        cursor = conn.cursor()
        cursor.execute('SELECT password FROM users WHERE username = ?', (username,))
        result = cursor.fetchone()
        conn.close()
        
        if result and result[0] == password:
            session['username'] = username
            return jsonify({'status': 'success', 'message': 'Login successful'})
        
        return jsonify({'status': 'error', 'message': 'Invalid credentials'})
    except Exception as e:
        return jsonify({'status': 'error', 'message': str(e)}), 500

@app.route('/logout', methods=['POST'])
def logout():
    session.pop('username', None)
    return jsonify({'status': 'success', 'message': 'Logged out'})

@app.route('/get-news')
def get_news():
    query = request.args.get('q', '').strip() or 'world'
    page = int(request.args.get('page', 1))

    params = {
        'apiKey': API_KEY,
        'q': query,
        'language': 'en',
        'sortBy': 'publishedAt',
        'pageSize': 10,
        'page': page
    }

    try:
        response = requests.get(NEWS_API_URL, params=params)
        response.raise_for_status()
        news_data = response.json()
        
        # Log search to database for logged-in users
        if 'username' in session:
            conn = sqlite3.connect(DB_NAME)
            cursor = conn.cursor()
            cursor.execute('INSERT INTO search_history (username, query, results_count) VALUES (?, ?, ?)',
                         (session['username'], query, len(news_data.get('articles', []))))
            conn.commit()
            conn.close()
        
        return jsonify(news_data)
    except requests.exceptions.RequestException as e:
        return jsonify({'status': 'error', 'message': str(e)}), 500

@app.route('/tags')
def get_tags():
    conn = sqlite3.connect(DB_NAME)
    cursor = conn.cursor()
    cursor.execute('SELECT tag_name, tag_color FROM tags')
    tags = [{'tag_name': row[0], 'tag_color': row[1]} for row in cursor.fetchall()]
    conn.close()
    return jsonify({'status': 'success', 'tags': tags})

@app.route('/feedback', methods=['POST'])
def submit_feedback():
    data = request.get_json()
    conn = sqlite3.connect(DB_NAME)
    cursor = conn.cursor()
    cursor.execute('INSERT INTO feedback (name, email, rating, message) VALUES (?, ?, ?, ?)',
                   (data.get('name', ''), data.get('email', ''), data.get('rating'), data.get('message')))
    conn.commit()
    conn.close()
    return jsonify({'status': 'success', 'message': 'Thank you for your feedback!'})

@app.route('/search-history')
def get_search_history():
    if 'username' not in session:
        return jsonify({'status': 'error', 'message': 'Not logged in'})
    
    conn = sqlite3.connect(DB_NAME)
    cursor = conn.cursor()
    cursor.execute('SELECT query, results_count, timestamp FROM search_history WHERE username = ? ORDER BY timestamp DESC LIMIT 10',
                   (session['username'],))
    history = [{'query': row[0], 'results_count': row[1], 'timestamp': row[2]} for row in cursor.fetchall()]
    conn.close()
    return jsonify({'status': 'success', 'history': history})

@app.route('/db-view')
def view_database():
    conn = sqlite3.connect(DB_NAME)
    cursor = conn.cursor()
    
    # Get all tables data
    tables = {}
    
    # Users
    cursor.execute('SELECT * FROM users')
    tables['users'] = cursor.fetchall()
    
    # Search History
    cursor.execute('SELECT * FROM search_history ORDER BY timestamp DESC')
    tables['search_history'] = cursor.fetchall()
    
    # Feedback
    cursor.execute('SELECT * FROM feedback ORDER BY timestamp DESC')
    tables['feedback'] = cursor.fetchall()
    
    # Tags
    cursor.execute('SELECT * FROM tags')
    tables['tags'] = cursor.fetchall()
    
    conn.close()
    
    html = f'''
    <html>
    <head><title>Database Viewer - Taaza Khabar</title>
    <style>
        body {{ font-family: Arial, sans-serif; margin: 20px; }}
        table {{ border-collapse: collapse; width: 100%; margin: 20px 0; }}
        th, td {{ border: 1px solid #ddd; padding: 8px; text-align: left; }}
        th {{ background-color: #f2f2f2; }}
        h2 {{ color: #333; }}
        .back-btn {{ background: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }}
    </style>
    </head>
    <body>
        <h1>Taaza Khabar Database</h1>
        <a href="/" class="back-btn">← Back to App</a>
        
        <h2>Users ({len(tables['users'])} records)</h2>
        <table>
            <tr><th>ID</th><th>Username</th><th>Password</th><th>Created At</th></tr>
            {''.join([f'<tr><td>{row[0]}</td><td>{row[1]}</td><td>***</td><td>{row[3]}</td></tr>' for row in tables['users']])}
        </table>
        
        <h2>Search History ({len(tables['search_history'])} records)</h2>
        <table>
            <tr><th>ID</th><th>Username</th><th>Query</th><th>Results Count</th><th>Timestamp</th></tr>
            {''.join([f'<tr><td>{row[0]}</td><td>{row[1]}</td><td>{row[2]}</td><td>{row[3]}</td><td>{row[4]}</td></tr>' for row in tables['search_history']])}
        </table>
        
        <h2>Feedback ({len(tables['feedback'])} records)</h2>
        <table>
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Rating</th><th>Message</th><th>Timestamp</th></tr>
            {''.join([f'<tr><td>{row[0]}</td><td>{row[1] or "Anonymous"}</td><td>{row[2] or "N/A"}</td><td>{"⭐" * row[3]}</td><td>{row[4][:50]}...</td><td>{row[5]}</td></tr>' for row in tables['feedback']])}
        </table>
        
        <h2>Tags ({len(tables['tags'])} records)</h2>
        <table>
            <tr><th>ID</th><th>Tag Name</th><th>Color</th><th>Preview</th></tr>
            {''.join([f'<tr><td>{row[0]}</td><td>{row[1]}</td><td>{row[2]}</td><td><span style="background:{row[2]};color:white;padding:4px 8px;border-radius:4px;">{row[1]}</span></td></tr>' for row in tables['tags']])}
        </table>
    </body>
    </html>
    '''
    return html

@app.route('/')
def index():
    try:
        with open('index.html', 'r', encoding='utf-8') as f:
            return f.read()
    except FileNotFoundError:
        return "<h1>Error: index.html not found</h1><p>Make sure index.html is in the same directory as app.py</p>"

def open_browser():
    webbrowser.open_new("http://localhost:5000")

if __name__ == '__main__':
    print("Starting Taaza Khabar with SQLite database...")
    threading.Timer(1.0, open_browser).start()
    app.run(port=5000, debug=True)
