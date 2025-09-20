<?php
session_start();

// Database connection settings
$servername = "mysql9001.site4now.net";
$username   = "abd9ff_ansh194";      // check in SmarterASP DB panel
$password   = "ansh194@";    // the DB password you created
$dbname     = "db_abd9ff_ansh194";   // must match the database name

// Connect
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Only process if form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $name    = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $phone   = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
    $email   = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $persons = isset($_POST['persons']) ? intval($_POST['persons']) : 0;
    $date    = isset($_POST['date']) ? htmlspecialchars(trim($_POST['date'])) : '';

    // Validate inputs
    $errors = [];
    if (empty($name)) $errors[] = "Name is required.";
    if (empty($phone)) $errors[] = "Phone number is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email required.";
    if ($persons <= 0) $errors[] = "Please select number of persons.";
    if (empty($date)) $errors[] = "Booking date is required.";

    if (empty($errors)) {
        // Prepare & bind statement
        $stmt = $conn->prepare("INSERT INTO bookings (name, phone, email, persons, booking_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssis", $name, $phone, $email, $persons, $date);

        if ($stmt->execute()) {
            $_SESSION['status_message'] = "✅ Thank you, {$name}! Your booking was successful.";
        } else {
            $_SESSION['status_message'] = "❌ Database error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $_SESSION['status_message'] = "❌ Booking failed:<br>" . implode("<br>", $errors);
    }
} else {
    $_SESSION['status_message'] = "❌ Invalid request.";
}

$conn->close();

// Redirect back to index.html at booking section
header("Location: index.html#book_section");
exit();
?>
