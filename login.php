<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"], $_POST["password"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    echo "<!DOCTYPE html><html><head>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body>";

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row["password"])) {
            $_SESSION["username"] = $row["username"];
            echo "<script>
                Swal.fire({
                  icon: 'success',
                  title: 'Login Successful!',
                  text: 'Welcome back!',
                }).then(() => {
                  window.location.href = 'index.php';
                });
              </script>";
        } else {
            echo "<script>
                Swal.fire({
                  icon: 'error',
                  title: 'Incorrect Password',
                  text: 'Please try again.',
                }).then(() => {
                  window.location.href = 'index.php';
                });
              </script>";
        }
    } else {
        echo "<script>
            Swal.fire({
              icon: 'error',
              title: 'User Not Found',
              text: 'Please register first.',
            }).then(() => {
              window.location.href = 'index.php';
            });
          </script>";
    }

    echo "</body></html>";
    exit;
} else {
    header("Location: index.php");
    exit;
}
?>
