<?php
session_start();
include 'db_connect.php';

// Make sure only adopters can access
if (!isset($_SESSION['email']) || $_SESSION['user_role'] !== 'adopter') {
    die("Access denied.");
}

$query = "SELECT id, name, breed, age, medical_history, adoption_fee, image FROM pets WHERE status = 'Available'";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PAWS | Adopter Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <a href="my_requests.php" class="add-dog-btn" style="float: right;">📄 My Requests</a>
        <div class="brand">🐾 Available Dogs for Adoption</div>
        <div class="pet-list">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='pet-card'>";
                    echo "<img src='" . htmlspecialchars($row['image']) . "' alt='Dog Image'>";
                    echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
                    echo "<p><strong>Breed:</strong> " . htmlspecialchars($row['breed']) . "</p>";
                    echo "<p><strong>Age:</strong> " . htmlspecialchars($row['age']) . " months</p>";
                    echo "<p><strong>Medical:</strong> " . htmlspecialchars($row['medical_history']) . "</p>";
                    echo "<p><strong>Fee:</strong> ₹" . htmlspecialchars($row['adoption_fee']) . "</p>";
                    echo "<form action='request_adoption.php' method='POST'>";
                    echo "<input type='hidden' name='pet_id' value='" . $row['id'] . "'>";
                    echo "<button class='adopt-btn'>Request Adoption</button>";
                    echo "</form>";
                    echo "</div>";
                }
            } else {
                echo "<p style='text-align:center;'>No dogs available at the moment. Check back soon!</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>