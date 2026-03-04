<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "database.php";

// Must be logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION["user_id"];

$db = new Database();
$conn = $db->getConnection();

$message = "";

// Handle Register request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["course_id"])) {
    $courseId = (int)$_POST["course_id"];

    $stmt = $conn->prepare("INSERT INTO registrations (user_id, course_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $userId, $courseId);

    try {
        $stmt->execute();
        $message = "✅ You’re registered! Course added to your schedule.";
    } catch (mysqli_sql_exception $e) {
        // Most common error: duplicate registration because of UNIQUE(user_id, course_id)
        $message = "⚠️ You are already registered for that course.";
    }

    $stmt->close();
}

// Pull course catalog
$result = $conn->query("SELECT course_id, course_code, course_title, credits, term FROM courses ORDER BY term, course_code");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Course Catalog</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Course Catalog</h2>

<?php if (!empty($message)): ?>
    <p><strong><?php echo htmlspecialchars($message); ?></strong></p>
<?php endif; ?>

<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>Course Code</th>
            <th>Course Title</th>
            <th>Credits</th>
            <th>Term</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row["course_code"]); ?></td>
                <td><?php echo htmlspecialchars($row["course_title"]); ?></td>
                <td><?php echo htmlspecialchars($row["credits"]); ?></td>
                <td><?php echo htmlspecialchars($row["term"]); ?></td>
                <td>
                    <form method="POST" action="courses.php" style="margin:0;">
                        <input type="hidden" name="course_id" value="<?php echo (int)$row["course_id"]; ?>">
                        <button type="submit">Register</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="5">No courses found in the catalog.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<p>
    <a href="dashboard.php">Back to Dashboard</a> |
    <a href="my_classes.php">My Classes</a> |
    <a href="logout.php">Logout</a>
</p>

</body>
</html>