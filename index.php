<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 1️⃣ Check if the user exists
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // 2️⃣ Verify password
        if (password_verify($password, $user['password'])) {
            // Store user data in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role']; // Giver or Adopter
            $_SESSION['email'] = $user['email']; // ✅ Store email in session

            // 3️⃣ Redirect based on role
            if ($user['role'] === 'giver') {
                header("Location: giver.html");
                exit();
            } elseif ($user['role'] === 'adopter') {
                header("Location: adopter.php");
                exit();
            }
        } else {
            echo "<script>alert('Incorrect password!'); window.location.href='index.html';</script>";
        }
    } else {
        echo "<script>alert('User not found!'); window.location.href='index.html';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
