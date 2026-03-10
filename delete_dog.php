<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['email'])) {
    die("Unauthorized");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['dog_id'])) {
    $dog_id = $_POST['dog_id'];

    // Protect against deleting other people's dogs
    $giver_email = $_SESSION['email'];
    $stmt = $conn->prepare("DELETE FROM pets WHERE id = ? AND giver_email = ?");
    $stmt->bind_param("is", $dog_id, $giver_email);

    if ($stmt->execute()) {
        header("Location: giver_dashboard.php");
        exit();
    } else {
        echo "Error deleting dog.";
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}
?>
