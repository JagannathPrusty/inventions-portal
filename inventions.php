<?php
session_start();
include("includes/db.php");

// If not logged in, redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Inventions</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<h2 style="text-align:center; margin-top:40px;">All Inventions</h2>

<div class="invention-grid">
<?php
$result = $conn->query("SELECT * FROM inventions ORDER BY id DESC");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $name = htmlspecialchars($row['name']);
        $inventor = htmlspecialchars($row['inventor']);
        $country = htmlspecialchars($row['country']);
        $uses = htmlspecialchars($row['uses']);
        $video = htmlspecialchars($row['video_url']);

        echo "
        <div class='card'>
            <iframe src='$video' allowfullscreen></iframe>
            <h3>$name</h3>
            <p><strong>Inventor:</strong> $inventor</p>
            <p><strong>Country:</strong> $country</p>
            <p>$uses</p>
        </div>";
    }
} else {
    echo "<p>No inventions available.</p>";
}
?>
</div>

</body>
</html>
