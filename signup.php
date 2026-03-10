<?php
session_start();

include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $location = $_POST['location'];
    $role = $_POST['role'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 1️⃣ Backend check: Ensure passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match! Please try again.'); window.location.href='signup.html';</script>";
        exit(); // Stop execution if passwords don't match
    }

    // Encrypt password after checking match
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 2️⃣ Check if the email already exists
    $check_query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already exists! Please use a different email.'); window.location.href='signup.html';</script>";
    } else {
        // 3️⃣ Insert user only if email doesn't exist
        $insert_query = "INSERT INTO users (name, contact, location, role, email, password) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ssssss", $name, $contact, $location, $role, $email, $hashed_password);

        if ($stmt->execute()) {
            echo "<script>alert('Signup successful!'); window.location.href='index.html';</script>";
        } else {
            echo "<script>alert('Signup failed! Please try again.');</script>";
        }
    }

    $stmt->close();
    $conn->close();
}
?>
