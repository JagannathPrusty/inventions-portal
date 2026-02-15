<?php
include("includes/db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if (empty($email) || empty($_POST['password'])) {
        echo "<script>alert('All fields are required');</script>";
    } else {

        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "<script>alert('Email already registered');</script>";
        } else {

            $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'user')");
            $stmt->bind_param("ss", $email, $password);

            if ($stmt->execute()) {
                echo "<script>alert('Signup successful!'); window.location='login.php';</script>";
            } else {
                echo "Error: " . $stmt->error;   // 👈 SHOWS REAL ERROR
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Sign Up</title>
  <link rel="stylesheet" href="css/auth.css" />
</head>
<body>

  <div class="auth-container">
    <h2>Create Account</h2>
    <p>Join the World Invention Archive</p>

    <form method="POST" >
      <div class="input-group">
        <input type="email" name="email" required />
        <label>Email Address</label>
      </div>

      <div class="input-group">
        <input type="password" name="password" required />
        <label>Password</label>
      </div>

      <button type="submit" class="btn">Sign Up</button>
    </form>

    <div class="switch-text">
      Already have an account? <a href="login.php">Login</a>
    </div>
  </div>

</body>
</html>

