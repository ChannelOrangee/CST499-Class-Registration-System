<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "Database.php";
$db = new Database();
$conn = $db->getConnection();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if (!empty($email) && !empty($password)) {

        $sql = "SELECT id, full_name, email, password FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if ($user && password_verify($password, $user["password"])) {
                // Login success: store user info in session
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["full_name"] = $user["full_name"];
                $_SESSION["email"] = $user["email"];

                header("Location: dashboard.php");
                exit;
            } else {
                $message = "Invalid email or password.";
            }
        } else {
            $message = "Database error: could not prepare query.";
        }

    } else {
        $message = "Email and password are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2>Login</h2>

  <?php if ($message !== ""): ?>
    <p><strong><?php echo htmlspecialchars($message); ?></strong></p>
  <?php endif; ?>

  <form method="POST" action="login.php">
    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Login</button>
  </form>

  <p>
    <a href="register.php">Register</a> |
    <a href="index.php">Back to Home</a>
  </p>
</body>
</html>

