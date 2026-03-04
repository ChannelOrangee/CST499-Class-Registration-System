<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "Database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION["user_id"];

$db = new Database();
$conn = $db->getConnection();

$message = "";

// DROP (delete) a class
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["registration_id"])) {
    $regId = (int)$_POST["registration_id"];

    $stmt = $conn->prepare("DELETE FROM registrations WHERE registration_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $regId, $userId);

    if ($stmt->execute()) {
        $message = "✅ Class dropped successfully.";
    } else {
        $message = "⚠️ Something went wrong dropping the class.";
    }

    $stmt->close();
}

// LIST registered classes
$sql = "
SELECT r.registration_id, c.course_code, c.course_title, c.credits, c.term, r.registered_at
FROM registrations r
JOIN courses c ON r.course_id = c.course_id
WHERE r.user_id = ?
ORDER BY c.term, c.course_code
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Classes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>My Registered Classes</h2>

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
            <th>Registered At</th>
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
                <td><?php echo htmlspecialchars($row["registered_at"]); ?></td>
                <td>
                    <form method="POST" action="my_classes.php" style="margin:0;">
                        <input type="hidden" name="registration_id" value="<?php echo (int)$row["registration_id"]; ?>">
                        <button type="submit">Drop</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="6">You are not registered for any classes yet.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<p>
    <a href="dashboard.php">Back to Dashboard</a> |
    <a href="courses.php">Course Catalog</a> |
    <a href="logout.php">Logout</a>
</p>

</body>
</html>

<?php $stmt->close(); ?>