<?php
session_start();
include("includes/db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check if fields are empty
    if (empty($email) || empty($password)) {
        echo "<script>alert('Please fill in all fields');</script>";
    } else {

        // Prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {

                // Prevent session fixation attack
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: ./admin.php");
                } else {
                    header("Location: ./index.php");
                }
                exit();

            } else {
                echo "<script>alert('Invalid password');</script>";
            }

        } else {
            echo "<script>alert('No account found');</script>";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="css/auth.css">
</head>
<body>

<div class="auth-container">
  <h2>Welcome Back</h2>
  <p>Login to access the Invention Portal</p>

  <form method="POST">
    <div class="input-group">
      <input type="email" name="email" required>
      <label>Email Address</label>
    </div>

    <div class="input-group">
      <input type="password" name="password" required>
      <label>Password</label>
    </div>

    <button type="submit" class="btn">Login</button>
  </form>

  <div class="switch-text">
    New here? <a href="signup.php">Create an Account</a>
  </div>
</div>

</body>
</html>
