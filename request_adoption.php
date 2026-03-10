<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['email']) || $_SESSION['user_role'] !== 'adopter') {
    die("Access denied. You must be logged in as an adopter.");
}

$adopter_email = $_SESSION['email'];
$pet_id = $_POST['pet_id'];

// Prevent duplicate requests for the same pet
$check_query = "SELECT * FROM adoption_requests WHERE adopter_email = ? AND pet_id = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("si", $adopter_email, $pet_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    echo "<script>alert('You have already requested adoption for this pet.'); window.location.href='adopter.php';</script>";
} else {
    $insert_query = "INSERT INTO adoption_requests (adopter_email, pet_id, status) VALUES (?, ?, 'Pending')";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param("si", $adopter_email, $pet_id);

    if ($insert_stmt->execute()) {
        echo "<script>alert('Adoption request sent successfully!'); window.location.href='adopter.php';</script>";
    } else {
        echo "<script>alert('Failed to send request. Please try again.'); window.location.href='adopter.php';</script>";
    }

    $insert_stmt->close();
}

$check_stmt->close();
$conn->close();
?>