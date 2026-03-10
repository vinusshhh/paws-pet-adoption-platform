<?php
session_start();

include 'db_connect.php';
session_start();
$email = $_SESSION['email']; // Get giver's email from session

// Fetch giver's location
$query = "SELECT location FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$location = $user['location']; // Get location from users table

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $breed = $_POST['breed'];
    $age = $_POST['age'];
    $medical_history = $_POST['medical_history'];
    $adoption_fee = $_POST['adoption_fee'];

    // Upload Image
    $image = $_FILES['image']['name'];
    $target = "uploads/" . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target);

    // Insert pet data along with the giver's location
    $sql = "INSERT INTO pets (name, breed, age, medical_history, adoption_fee, image, giver_email, location) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisdsss", $name, $breed, $age, $medical_history, $adoption_fee, $image, $email, $location);

    if ($stmt->execute()) {
        echo "<script>alert('Pet Added Successfully!'); window.location.href='giver.html';</script>";
    } else {
        echo "<script>alert('Error adding pet. Please try again.');</script>";
    }
}

$stmt->close();
$conn->close();
?>
