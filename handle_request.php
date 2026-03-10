<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['email']) || $_SESSION['user_role'] !== 'giver') {
    die("Access denied.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];

    if ($action === 'accept') {
        $update = "
            UPDATE adoption_requests ar
            JOIN pets p ON ar.pet_id = p.id
            SET ar.status = 'Accepted', p.status = 'Adopted'
            WHERE ar.id = ?
        ";
    } elseif ($action === 'reject') {
        $update = "UPDATE adoption_requests SET status = 'Rejected' WHERE id = ?";
    }

    $stmt = $conn->prepare($update);
    $stmt->bind_param("i", $request_id);

    if ($stmt->execute()) {
        echo "<script>alert('Request updated successfully.'); window.location.href='giver_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error updating request.'); window.location.href='giver_dashboard.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>