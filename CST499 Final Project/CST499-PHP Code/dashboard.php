<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2>Dashboard</h2>

  <p>Welcome, <strong><?php echo htmlspecialchars($_SESSION["full_name"]); ?></strong>!</p>
  <p>You are logged in as: <?php echo htmlspecialchars($_SESSION["email"]); ?></p>
<p><a href="logout.php" class="logout-btn">Logout</a></p>
</body>
</html>

<h3>Available Courses</h3>
<ul>
  <li>Introduction to Software Engineering</li>
  <li>Web Application Development</li>
  <li>Database Systems</li>
  <li>Cybersecurity Fundamentals</li>
</ul>

