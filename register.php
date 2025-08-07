<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"], $_POST["password"])) {
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $check = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    echo "<!DOCTYPE html><html><head>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body>";

    if (mysqli_num_rows($check) > 0) {
        echo "<script>
            Swal.fire({
              icon: 'error',
              title: 'Username Already Exists',
              text: 'Please try another.',
            }).then(() => {
              window.location.href = 'index.php';
            });
          </script>";
    } else {
        $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
        if (mysqli_query($conn, $sql)) {
            echo "<script>
                Swal.fire({
                  icon: 'success',
                  title: 'Registered Successfully!',
                  text: 'Please login to continue.',
                }).then(() => {
                  window.location.href = 'index.php';
                });
              </script>";
        } else {
            echo "<script>
                Swal.fire({
                  icon: 'error',
                  title: 'Registration Failed',
                  text: 'Something went wrong.',
                }).then(() => {
                  window.location.href = 'index.php';
                });
              </script>";
        }
    }

    echo "</body></html>";
    exit;
} else {
    header("Location: index.php");
    exit;
}
?>
