<?php
session_start();
include("includes/db.php");

// ✅ Check if admin is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}

// ✅ Handle CSV Upload
if (isset($_POST['upload_csv'])) {
  $file = $_FILES['csv_file']['tmp_name'];

  if ($_FILES['csv_file']['error'] === 0 && ($handle = fopen($file, "r")) !== FALSE) {
    fgetcsv($handle); // skip header row
    $count = 0;
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      $name = mysqli_real_escape_string($conn, trim($data[0]));
      $inventor = mysqli_real_escape_string($conn, trim($data[1]));
      $country = mysqli_real_escape_string($conn, trim($data[2]));
      $uses = mysqli_real_escape_string($conn, trim($data[3]));
      $video_url = mysqli_real_escape_string($conn, trim($data[4]));

      // convert youtube URL to embed
      if (strpos($video_url, "watch?v=") !== false) {
        $video_url = str_replace("watch?v=", "embed/", $video_url);
      } elseif (strpos($video_url, "youtu.be/") !== false) {
        $video_url = str_replace("youtu.be/", "youtube.com/embed/", $video_url);
      }

      $conn->query("INSERT INTO inventions (name, inventor, country, uses, video_url)
                    VALUES ('$name','$inventor','$country','$uses','$video_url')");
      $count++;
    }
    fclose($handle);
    echo "<script>alert('$count inventions uploaded successfully!'); window.location='admin.php';</script>";
  } else {
    echo "<script>alert('Error uploading file');</script>";
  }
}

// ✅ Handle Manual Add
if (isset($_POST['add_invention'])) {
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $inventor = mysqli_real_escape_string($conn, $_POST['inventor']);
  $country = mysqli_real_escape_string($conn, $_POST['country']);
  $uses = mysqli_real_escape_string($conn, $_POST['uses']);
  $video_url = mysqli_real_escape_string($conn, $_POST['video_url']);

  if (strpos($video_url, "watch?v=") !== false) {
    $video_url = str_replace("watch?v=", "embed/", $video_url);
  } elseif (strpos($video_url, "youtu.be/") !== false) {
    $video_url = str_replace("youtu.be/", "youtube.com/embed/", $video_url);
  }

  if (!empty($name) && !empty($inventor) && !empty($video_url)) {
    $query = "INSERT INTO inventions (name, inventor, country, uses, video_url)
              VALUES ('$name','$inventor','$country','$uses','$video_url')";
    if ($conn->query($query)) {
      echo "<script>alert('Invention added successfully!'); window.location='admin.php';</script>";
    }
  } else {
    echo "<script>alert('Please fill all required fields!');</script>";
  }
}

// ✅ Delete invention
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $conn->query("DELETE FROM inventions WHERE id=$id");
  echo "<script>alert('Invention deleted successfully!'); window.location='admin.php';</script>";
  exit();
}

// ✅ Fetch all inventions
$result = $conn->query("SELECT * FROM inventions ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - World Invention Archive</title>
  <link rel="stylesheet" href="css/styles.css">
  <style>
  body {
    background: linear-gradient(to bottom right, #00111a, #002233);
    color: #fff;
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    text-align: center;
  }
.admin-header {
  background: #01244b;
  padding: 20px 5%;
  color: white;
  box-shadow: 0 4px 10px rgba(0, 198, 255, 0.2);
  position: relative;
  z-index: 10;
}

.admin-header-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 400px; /* Adds space between left, center, and right */
  flex-wrap: wrap;
}

/* Left Title */
.left-title h1 {
  font-size: 1.5rem;
  color: #00c6ff;
  margin: 0;
}

/* Center Title */
.center-title {
  flex: 1;
  text-align: center;
}
.center-title h1 {
  font-size: 1.4rem;
  color: #ffffff;
  margin: 0;
}

/* Right Button */
.right-button {
  text-align: right;
}
.logout-btn {
  background: linear-gradient(45deg, #00c6ff, #0072ff);
  color: white;
  padding: 10px 20px;
  border-radius: 25px;
  text-decoration: none;
  font-weight: 500;
  transition: 0.3s;
}
.logout-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0, 198, 255, 0.4);
}





  /* 👇 Reduced top margin so form starts right after header */
  section {
    margin: 20px auto;
    width: 80%;
    max-width: 1000px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(0,198,255,0.3);
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 0 15px rgba(0,198,255,0.15);
  }

  /* Optional: first section starts immediately below header */
  section:first-of-type {
    margin-top: 10px;
  }

  input, textarea {
    width: 90%;
    padding: 10px;
    margin: 10px 0;
    border-radius: 8px;
    border: none;
    background: rgba(255,255,255,0.1);
    color: #fff;
    font-size: 1rem;
  }

  button {
    background: linear-gradient(45deg, #00c6ff, #0072ff);
    border: none;
    color: white;
    padding: 10px 25px;
    border-radius: 25px;
    cursor: pointer;
    transition: 0.3s;
    
  }

  button:hover {
    background: linear-gradient(45deg, #0072ff, #00c6ff);
    transform: translateY(-3px);
  }

  .admin-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
    margin: 40px auto;
    width: 90%;
  }

  .card {
    background: #0a0a0a;
    border: 1px solid rgba(0,198,255,0.2);
    border-radius: 12px;
    padding: 15px;
    box-shadow: 0 0 15px rgba(0,198,255,0.2);
    transition: all 0.3s ease;
  }

  .card:hover {
    transform: scale(1.03);
    box-shadow: 0 0 25px rgba(0,198,255,0.3);
  }

  iframe {
    width: 100%;
    height: 180px;
    border-radius: 8px;
    border: none;
    margin-bottom: 10px;
  }

  .delete-btn {
    display: inline-block;
    background: #ff4444;
    color: white;
    padding: 6px 16px;
    border-radius: 6px;
    text-decoration: none;
    margin-top: 10px;
    transition: 0.3s;
  }

  .delete-btn:hover {
    background: #ff6666;
  }
</style>

</head>
<body>

 <header class="admin-header">
  <div class="admin-header-container">
    <div class="left-title">
      <h1>⚙️ Admin Dashboard</h1>
    </div>

    <div class="center-title">
      <h1>Welcome, Admin 👋</h1>
    </div>

    <div class="right-button">
      <a href="logout.php" class="logout-btn">Logout</a>
    </div>
  </div>
</header>



  <!-- ✅ Add Single Invention -->
  <section>
    <h2>➕ Add a Single Invention</h2>
    <form method="POST">
      <input type="text" name="name" placeholder="Invention Name" required><br>
      <input type="text" name="inventor" placeholder="Inventor" required><br>
      <input type="text" name="country" placeholder="Country"><br>
      <textarea name="uses" placeholder="Describe the uses or purpose of the invention..." rows="3"></textarea><br>
      <input type="text" name="video_url" placeholder="YouTube Video URL (https://...)" required><br>
      <button type="submit" name="add_invention">Add Invention</button>
    </form>
  </section>

  <!-- ✅ Upload CSV -->
  <section>
    <h2>📁 Bulk Upload via CSV</h2>
    <form method="POST" enctype="multipart/form-data">
      <p><small>Ensure CSV columns: name, inventor, country, uses, video_url</small></p>
      <input type="file" name="csv_file" accept=".csv" required><br><br>
      <button type="submit" name="upload_csv">Upload CSV</button>
    </form>
  </section>

  <!-- ✅ Display Uploaded Inventions -->
  <section>
    <h2>🧠 All Uploaded Inventions</h2>
    <div class="admin-grid">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card">
          <iframe src="<?= htmlspecialchars($row['video_url']) ?>" allowfullscreen></iframe>
          <h3><?= htmlspecialchars($row['name']) ?></h3>
          <p><b>Inventor:</b> <?= htmlspecialchars($row['inventor']) ?></p>
          <p><b>Country:</b> <?= htmlspecialchars($row['country']) ?></p>
          <p><?= htmlspecialchars($row['uses']) ?></p>
          <a href="?delete=<?= $row['id'] ?>" class="delete-btn">🗑️ Delete</a>
        </div>
      <?php endwhile; ?>
    </div>
  </section>

</body>
</html>
