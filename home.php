 <?php 
    session_start();
    if (isset($_SESSION['username'])): ?>
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const sessionContainer = document.getElementById("user-session");
        sessionContainer.innerHTML = `
          <span class="welcome-user">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
          <form method="POST" action="logout.php" style="display:inline;">
            <button type="submit" class="logout-btn">Logout</button>
          </form>
        `;
      });
    </script>
<?php endif; ?>