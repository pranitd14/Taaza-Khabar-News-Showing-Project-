<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Taaza Khabar</title>
  <link rel="icon" href="https://i.ibb.co/WpBT07Gy/logo.png" />
  <link rel="stylesheet" href="/static/style.css" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    :root {
      --bg: #f0f2f5;
      --text: #2c3e50;
      --card: #ffffff;
      --scrollbar-track-color: #e0e0e0;
      --scrollbar-thumb-gradient: linear-gradient(135deg, #007bff, #17a2b8);
    }

    ::-webkit-scrollbar {
      width: 12px;
    }

    ::-webkit-scrollbar-track {
      background: var(--scrollbar-track-color);
    }

    ::-webkit-scrollbar-thumb {
      background: var(--scrollbar-thumb-gradient);
      border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: var(--scrollbar-thumb-gradient);
      opacity: 0.8;
    }

    .dark-mode {
      --bg: #1e1e1e;
      --text: #f0f2f5;
      --card: #2c2c2c;
      --scrollbar-track-color: #2d3436;
      --scrollbar-thumb-gradient: linear-gradient(135deg, #a8ff78, #78ffd6);
      background-color: #ffffff;
      color: #333333;
    }

    body {
      font-family: "Segoe UI", sans-serif;
      margin: 0;
      color: var(--text);
      background-color: var(--bg);
      position: relative;
      z-index: 0;
      overflow-x: hidden;
    }

    body::before {
      content: "";
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background-image: url("https://i.ibb.co/jZkfGx0R/background.png");
      background-size: cover;
      background-repeat: no-repeat;
      background-attachment: fixed;
      z-index: -1;
      transition: filter 0.3s ease;
    }

    body.dark-mode::before {
      filter: invert(1) hue-rotate(180deg);
    }

    header {
      background-color: #ffffff59;
      color: white;
      padding: 1rem;
      position: relative;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .header-left {
      display: flex;
      align-items: center;
    }

    .header-right {
      display: flex;
      align-items: center;
    }

    .header-center {
      display: flex;
      flex-direction: column;
      align-items: center;
      flex: 1;
    }

    header img {
      height: 150px;
      vertical-align: middle;
      margin-right: 8px;
    }

    #searchBar {
      margin-top: 1rem;
    }

    #searchInput {
      padding: 0.5rem;
      width: 60%;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 1rem;
      transition: border-color 0.3s ease;
    }

    #searchButton,
    #refreshButton,
    #themeToggle {
      padding: 0.5rem 1rem;
      margin: 0.2rem;
      border-radius: 5px;
      border: none;
      font-size: 1rem;
      cursor: pointer;
      transition: background-color 0.3s ease;
      color: white;
    }

    #searchButton {
      background-color: #3498db;
      display: flex-row;
      justify-content: center;
      align-items: center;
    }

    #searchButton:hover {
      background-color: #2980b9;
    }

    /* 🔄 Refresh Floating Button */
    #refreshButton {
      background: linear-gradient(135deg, #3498db, #2ecc71);
      position: fixed;
      bottom: 1rem;
      right: 1.4rem;
      border-radius: 50%;
      height: 60px;
      width: 60px;
      border: none;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.3);
      cursor: pointer;
      transition: all 0.3s ease;
      z-index: 1100;
      animation: pulse 2.5s infinite;
    }

    /* Pulse animation */
    @keyframes pulse {
      0% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(52, 152, 219, 0.6);
      }

      70% {
        transform: scale(1.05);
        box-shadow: 0 0 0 12px rgba(52, 152, 219, 0);
      }

      100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(52, 152, 219, 0);
      }
    }

    /* Hover: glow + rotate */
    #refreshButton:hover {
      transform: scale(1.15) rotate(15deg);
      background: linear-gradient(135deg, #2ecc71, #3498db);
      box-shadow: 0 8px 22px rgba(0, 0, 0, 0.4);
    }

    /* Icon styling */
    #refreshButton img {
      height: 28px;
      width: 28px;
      filter: drop-shadow(0 0 2px rgba(0, 0, 0, 0.3));
      transition: transform 0.4s ease;
      object-fit: contain;
      /* prevents stretching */
      margin: 0;
      /* remove default spacing */
      display: block;
      /* remove inline-gap */
    }

    /* Click effect: rotate icon */
    #refreshButton:active img {
      transform: rotate(360deg);
    }

    /* 🌙 Dark mode */
    body.dark-mode #refreshButton {
      background: linear-gradient(135deg, #a8ff78, #78ffd6);
      box-shadow: 0 6px 18px rgba(255, 255, 255, 0.15);
    }

    body.dark-mode #refreshButton img {
      filter: invert(1);
    }

    /* 🏷️ Tooltip */
    #refreshButton::after {
      content: "Refresh News";
      position: absolute;
      bottom: 70px;
      /* position above button */
      right: 50%;
      transform: translateX(50%);
      background: rgba(0, 0, 0, 0.75);
      color: #fff;
      padding: 6px 10px;
      border-radius: 6px;
      font-size: 0.85rem;
      white-space: nowrap;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.3s ease, transform 0.3s ease;
    }

    /* Tooltip arrow */
    #refreshButton::before {
      content: "";
      position: absolute;
      bottom: 58px;
      right: 50%;
      transform: translateX(50%);
      border-width: 6px;
      border-style: solid;
      border-color: rgba(0, 0, 0, 0.75) transparent transparent transparent;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    /* Show tooltip on hover */
    #refreshButton:hover::after,
    #refreshButton:hover::before {
      opacity: 1;
      transform: translateX(50%) translateY(-4px);
    }


    #themeToggle img {
      height: 30px;
      width: 30px;
    }

    #themeToggle {
      background-color: #8d44ad00;
      position: absolute;
      top: 1rem;
      right: 1rem;
    }

    #themeToggle:hover {
      background-color: #ffffff00;
    }

    /* Login Modal Styles */
    .login-btn {
      background-color: #e74c3c;
      color: white;
      padding: 0.5rem 1rem;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 1rem;
      transition: background-color 0.3s ease;
    }

    .login-btn:hover {
      background-color: #c0392b;
    }

    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(5px);
    }

    .modal-content {
      background-color: var(--card);
      margin: 5% auto;
      padding: 2rem;
      border-radius: 10px;
      width: 90%;
      max-width: 450px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
      position: relative;
    }

    .welcome-user {
      font-weight: bold;
      color: var(--text);
    }

    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
      position: absolute;
      top: 10px;
      right: 15px;
    }

    .close:hover,
    .close:focus {
      color: var(--text);
    }

    .login-form {
      margin-top: 1rem;
    }

    .form-group {
      margin-bottom: 1rem;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: var(--text);
    }

    .form-group input {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 1rem;
      background-color: var(--bg);
      color: var(--text);
      box-sizing: border-box;
    }

    .form-group input:focus {
      outline: none;
      border-color: #3498db;
      box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
    }

    .login-submit {
      width: 100%;
      background-color: #3498db;
      color: white;
      padding: 0.75rem;
      border: none;
      border-radius: 5px;
      font-size: 1rem;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .login-submit:hover {
      background-color: #2980b9;
    }

    .login-header {
      text-align: center;
      margin-bottom: 2rem;
    }

    .login-logo {
      height: 80px;
      margin-bottom: 1rem;
    }

    .login-title {
      margin-bottom: 0.5rem;
      color: var(--text);
      font-size: 1.8rem;
      font-weight: 600;
    }

    .login-subtitle {
      color: #666;
      font-size: 1rem;
      margin: 0;
    }

    .register-link {
      text-align: center;
      margin-top: 1rem;
      padding-top: 1rem;
      border-top: 1px solid #eee;
    }

    .register-link a {
      color: #3498db;
      text-decoration: none;
    }

    .register-link a:hover {
      text-decoration: underline;
    }

    #loading {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 4rem 1rem;
      font-size: 1.2rem;
    }

    .spinner {
      width: 40px;
      height: 40px;
      border: 5px solid #ccc;
      border-top: 5px solid #3498db;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin-bottom: 1rem;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }

    #news {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 1rem;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 1.5rem;
    }

    .news-card {
      background: var(--card);
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      display: flex;
      flex-direction: column;
      transition: transform 0.2s ease;
    }

    .news-card:hover {
      transform: translateY(-4px);
    }

    .news-image {
      width: 100%;
      height: 180px;
      object-fit: cover;
      background-color: #eee;
    }

    .news-content {
      padding: 1rem;
      flex: 1;
    }

    .news-content h3 {
      margin: 0 0 0.5rem;
      font-size: 1.1rem;
    }

    .news-content p {
      font-size: 0.95rem;
      color: var(--text);
    }

    .news-content a {
      display: inline-block;
      margin-top: 0.5rem;
      color: #3498db;
      text-decoration: none;
    }

    .news-content a:hover {
      text-decoration: underline;
    }

    footer {
      text-align: center;
      color: #888;
      padding: 1rem;
      margin-top: 2rem;
    }

    /* Adding some basic styles for the new buttons for demonstration */
    #user-session {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .welcome-user {
      font-weight: bold;
    }

    .dashboard-btn,
    .logout-btn {
      padding: 8px 15px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      color: white;
    }

    .dashboard-btn {
      background-color: #27ae60;
    }

    .logout-btn {
      background-color: #e74c3c;
    }

    @media (max-width: 730px) {
      header {
        flex-direction: column;
        align-items: center;
      }

      header img {
        height: 100px;
      }

      #searchBar {
        width: 80%;
        margin-top: 1rem;
      }

      #searchInput {
        width: 100%;
      }

      #searchButton,
      #refreshButton {
        width: 40%;
        margin: 0.5rem;
      }

      .header-center {
        width: 100%;
        text-align: center;
      }
    }
  </style>
</head>

<body>
  <header>
    <img src="https://i.ibb.co/WpBT07Gy/logo.png" alt="Taaza Khabar Logo" loading="eager">
    <div class="header-left" id="user-session"></div>
    <div class="header-center">
      <div id="searchBar">
        <input type="text" id="searchInput" placeholder="Search news..." />
        <button id="searchButton" onclick="searchNews()">Search</button>
        <button id="refreshButton" onclick="refreshNews()"><img src="https://i.ibb.co/G4hjNp9v/sync.png" alt="Refresh" loading="eager" /></button>
      </div>
    </div>
    <div class="header-right">
      <button id="themeToggle" onclick="toggleTheme()">
        <img src="https://i.ibb.co/9HnYydFV/theme-toggle.png" alt="Toggle Theme" loading="eager" />
      </button>
    </div>
  </header>
  <?php if (isset($_SESSION['username'])): ?>
    <script>
      window.addEventListener("DOMContentLoaded", () => {
        const sessionContainer = document.getElementById("user-session");
        sessionContainer.innerHTML = `
            <span class="welcome-user">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
            <form method="POST" action="logout.php" style="display:inline;">
              <button type="submit" class="logout-btn">Logout</button>
            </form>
          `;
      });
    </script>
    </div>
  <?php else: ?>
    <div id="loginModal" class="modal" style="display: none;">
      <div class="modal-content">
        <span class="close" onclick="closeLoginModal()">&times;</span>
        <div class="login-header">
          <img src="https://i.ibb.co/WpBT07Gy/logo.png" alt="Taaza Khabar Logo" style="height: 100px;" loading="eager">
          <h2 class="login-title">Welcome Back</h2>
          <p class="login-subtitle">Sign in to your account</p>
        </div>
        <form class="login-form" method="POST" action="login.php">
          <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required />
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required />
          </div>
          <button type="submit" class="login-submit">Sign In</button>
        </form>
        <div class="register-link">
          <p>
            Don't have an account?
            <a href="#" onclick="showRegisterForm()">Register here</a>
          </p>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Login Modal -->
  <div id="loginModal" class="modal" style="display: none;">
    <div class="modal-content">
      <span class="close" onclick="closeLoginModal()">&times;</span>
      <div class="login-header">
        <img src="/static/media/logo.png" alt="Taaza Khabar Logo" class="login-logo" loading="eager" />
        <h2 class="login-title">Welcome Back</h2>
        <p class="login-subtitle">Sign in to your account</p>
      </div>
      <form class="login-form" method="POST" action="login.php">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" required />
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required />
        </div>
        <button type="submit" class="login-submit">Sign In</button>
      </form>
      <div class="register-link">
        <p>
          Don't have an account?
          <a href="#" onclick="showRegisterForm()">Register here</a>
        </p>
      </div>
    </div>
  </div>

  <div id="loading">
    <div class="spinner"></div>
    <p>Loading <strong>Taaza Khabar</strong>...</p>
  </div>

  <div id="news"></div>

  <footer>&copy; 2025 Taaza Khabar</footer>

  <script>
    function displayNews(data) {
      document.getElementById("loading").style.display = "none";
      const newsContainer = document.getElementById("news");
      newsContainer.style.display = "grid";
      newsContainer.innerHTML = "";

      if (
        data.status === "ok" &&
        Array.isArray(data.articles) &&
        data.articles.length > 0
      ) {
        data.articles.forEach((article) => {
          const card = document.createElement("div");
          card.className = "news-card";

          const imageUrl =
            article.urlToImage ||
            "https://via.placeholder.com/400x200?text=No+Image";

          card.innerHTML = `
            <img class="news-image" src="${imageUrl}" alt="News Image" loading="eager">
            <div class="news-content">
              <h3>${article.title}</h3>
              <p>${article.description || "No description available."}</p>
              <a href="${article.url}" target="_blank">Read More →</a>
            </div>
          `;
          newsContainer.appendChild(card);
        });
      } else {
        newsContainer.innerHTML = '<p style="text-align:center;">No news available right now.</p>';
      }
    }

    function searchNews() {
      const query = document.getElementById("searchInput").value.trim();
      if (!query) {
        alert("Please enter a search term");
        return;
      }
      fetchNews(query);
    }

    function refreshNews() {
      document.getElementById("searchInput").value = "";
      fetchNews();
    }

    function toggleTheme() {
      document.body.classList.toggle("dark-mode");
    }

    function openLoginModal() {
      document.getElementById("loginModal").style.display = "block";
    }

    function closeLoginModal() {
      document.getElementById("loginModal").style.display = "none";
      document.querySelector(".login-form").reset();
    }

    function updateLoginButton() {
      const sessionContainer = document.getElementById("user-session");
      sessionContainer.innerHTML =
        '<button class="login-btn" onclick="openLoginModal()">Login</button>';
    }

    function showRegisterForm() {
      const modalContent = document.querySelector(".modal-content");
      modalContent.innerHTML = `
                <span class="close" onclick="closeLoginModal()">&times;</span>
                <div class="login-header">
                    <img src="/media/logo.png" alt="Taaza Khabar Logo" class="login-logo" loading="eager">
                    <h2 class="login-title">Create Account</h2>
                </div>
                <form class="login-form" method="POST" action="register.php">
                    <div class="form-group">
                        <label for="reg-username">Username</label>
                        <input type="text" id="reg-username" name="username" required minlength="3">
                    </div>
                    <div class="form-group">
                        <label for="reg-password">Password</label>
                        <input type="password" id="reg-password" name="password" required minlength="6">
                    </div>
                    <button type="submit" class="login-submit">Create Account</button>
                </form>
                <div class="register-link">
                    <p>Already have an account? <a href="#" onclick="showLoginForm()">Sign in here</a></p>
                </div>
            `;
    }

    window.onclick = function(event) {
      const modal = document.getElementById("loginModal");
      if (event.target === modal) {
        closeLoginModal();
      }
    };

    // Initialize Page
    document.addEventListener("DOMContentLoaded", () => {
      updateLoginButton();
      fetchNews();
    });

    // Fetching news
    function fetchNews(query = 'world') {
      document.getElementById("loading").style.display = "flex";
      document.getElementById("news").style.display = "none";
      document.getElementById("news").innerHTML = "";

      fetch(`http://localhost:5000/get-news?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
          console.log("Fetched data:", data);
          document.getElementById("loading").style.display = "none";
          document.getElementById("news").style.display = "grid";

          if (data.status === "ok" && Array.isArray(data.articles) && data.articles.length > 0) {
            displayNews(data);
          } else {
            document.getElementById("news").innerHTML = "⚠️ No articles found.";
          }
        })
        .catch(error => {
          console.error("Fetch error:", error);
          document.getElementById("loading").style.display = "none";
          document.getElementById("news").style.display = "block";
          document.getElementById("news").innerHTML = "⚠️ Failed to load news.";
        });
    }
  </script>
</body>

</html>