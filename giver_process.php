<?php
session_start();
if (!isset($_SESSION['email'])) {
    die("Error: User not logged in. Please log in first.");
}

include("db_connect.php"); // ✅ Ensure correct database connection file

$giver_email = $_SESSION['email']; // ✅ Get giver's email from session

// ✅ Fetch giver's location from database (in case it's not in session)
$query = "SELECT location FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $giver_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Error: Giver email not found in database.");
}

$row = $result->fetch_assoc();
$location = $row['location']; // ✅ Use fetched location

// ✅ Handle form inputs securely
$dog_name = $_POST['dog_name'];
$breed = $_POST['breed'];
$age = $_POST['age'];
$medical_history = $_POST['medical_history'];
$adoption_fee = $_POST['adoption_fee'];

// ✅ Handling image upload safely
$target_dir = "uploads/";
$image_name = basename($_FILES["dog_image"]["name"]);
$target_file = $target_dir . time() . "_" . $image_name; // Add timestamp to avoid duplicates

if (!move_uploaded_file($_FILES["dog_image"]["tmp_name"], $target_file)) {
    die("Error: Failed to upload image.");
}

$image = $target_file; // ✅ Store secure image path

// ✅ Insert into database using prepared statement
$query = "INSERT INTO pets (name, breed, age, medical_history, adoption_fee, image, status, giver_email, location) 
          VALUES (?, ?, ?, ?, ?, ?, 'Available', ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssssssss", $dog_name, $breed, $age, $medical_history, $adoption_fee, $image, $giver_email, $location);

if ($stmt->execute()) {
    echo "<script>alert('Dog added successfully!'); window.location.href='giver_dashboard.php';</script>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
