<?php include("includes/db.php"); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>World Invention Archive</title>
  <link rel="stylesheet" href="css/styles.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<body>

  <!-- Video Background Section -->
  <div class="video-section">
    <video autoplay muted loop id="bgVideo">
      <source src="assets/bg.mp4" type="video/mp4">
    </video>

    <header>
      <h1>🌍 World Invention Archive</h1>
      <nav>
        <a href="index.php">Home</a>
        <a href="login.php">Login</a>
        <a href="signup.php">Sign Up</a>
      </nav>
    </header>

    <div class="overlay">
      <h2>100 Inventions That Changed the World</h2>
      <p>Discover the greatest human innovations</p>
    </div>
  </div>

  <div class="news-ticker">
    <?php
    $latest = $conn->query("SELECT name, inventor FROM inventions ORDER BY id DESC LIMIT 1");

    if ($latest->num_rows > 0) {
      $row = $latest->fetch_assoc();
      echo "<marquee behavior='scroll' direction='left'>
            🔔 Latest Added: " . htmlspecialchars($row['name']) .
        " by " . htmlspecialchars($row['inventor']) . " 🔔
            </marquee>";
    } else {
      echo "<marquee behavior='scroll' direction='left'>
            🔔 No inventions added yet 🔔
            </marquee>";
    }
    ?>
  </div>


  <!-- Main Content -->
  <main>
    <section class="featured">
      <h2>Featured Inventions</h2>
      <div class="invention-grid">
        <?php
        $query = "SELECT * FROM inventions ORDER BY id DESC LIMIT 6";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
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
          echo "<p class='no-data'>No inventions added yet.</p>";
        }
        ?>
      </div>
      <div style="text-align:center; margin:40px 0;">
  <a href="inventions.php" class="explore-btn">Explore More</a>
</div>

    </section>
  </main>
  <footer class="site-footer">
    <div class="footer-container">

      <div class="footer-section">
        <h3>🌍 World Invention Archive</h3>
        <p>Exploring the greatest innovations that changed humanity forever.</p>
      </div>

      <div class="footer-section">
        <h4>Quick Links</h4>
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="login.php">Login</a></li>
          <li><a href="signup.php">Sign Up</a></li>
        </ul>
      </div>

      <div class="footer-section">
        <h4>Contact</h4>
        <p>Email: support@inventionarchive.com</p>
        <p>Location: Global Innovation Network</p>
      </div>

    </div>

    <div class="footer-bottom">
      © <?php echo date("Y"); ?> World Invention Archive | All Rights Reserved
    </div>
  </footer>



</body>
  

</html>