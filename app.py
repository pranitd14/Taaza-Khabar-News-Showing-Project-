from flask import Flask, request, jsonify, redirect, make_response
from flask_cors import CORS
import threading
import webbrowser
import requests
import os
from dotenv import load_dotenv

load_dotenv()

API_KEY = os.getenv('API_KEY')
NEWS_API_URL = 'https://newsapi.org/v2/everything'

app = Flask(__name__)
CORS(app)

@app.route('/login', methods=['POST'])
def login():
    username = request.form.get('username')
    if username:
        resp = make_response(redirect("http://localhost/taazakhabar/index.php"))
        resp.set_cookie('username', username)
        return resp
    return jsonify({'error': 'Invalid username'}), 400

@app.route('/get-news')
def get_news():
    query = request.args.get('q', '').strip() or 'world'

    params = {
        'apiKey': API_KEY,
        'q': query,
        'language': 'en',
        'sortBy': 'publishedAt',
        'pageSize': 20
    }

    try:
        response = requests.get(NEWS_API_URL, params=params)
        response.raise_for_status()
        return jsonify(response.json())
    except requests.exceptions.RequestException as e:
        return jsonify({'status': 'error', 'message': str(e)}), 500

def open_browser():
    webbrowser.open_new("http://localhost/taazakhabar/index.php")

if __name__ == '__main__':
    threading.Timer(1.0, open_browser).start()
    app.run(port=5000)
