<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Online Course Registration System</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Welcome to the Online Course Registration System</h2>

<?php if (isset($_SESSION["user_id"])): ?>
  <p>Welcome back, <strong><?php echo htmlspecialchars($_SESSION["full_name"]); ?></strong>!</p>
  <p>
    <a href="dashboard.php">Go to Dashboard</a> |
    <a href="logout.php">Logout</a>
  </p>
<?php else: ?>
  <p>
    <a href="login.php">Login</a> |
    <a href="register.php">Register</a>
  </p>
<?php endif; ?>

</body>
</html>

