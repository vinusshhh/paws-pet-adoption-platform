<?php
session_start();
include 'db_connect.php';

// ✅ Only allow givers
if (!isset($_SESSION['email']) || $_SESSION['user_role'] !== 'giver') {
    die("Access denied.");
}

$giver_email = $_SESSION['email'];

// 🔹 Fetch dogs uploaded by this giver
$query = "SELECT id, name, breed, age, image FROM pets WHERE giver_email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $giver_email);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giver Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(to right, #FF7F50, #FF6F61);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            max-width: 1000px;
            margin: auto;
        }
        .brand {
            font-size: 2.2rem;
            font-weight: bold;
            color: #ff6f61;
            margin-bottom: 20px;
            text-align: center;
        }
        .dog-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        .dog-card {
            background: #fff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .dog-img {
            max-width: 100%;
            max-height: 200px;
            object-fit: contain;
            border-radius: 10px;
            background: #f5f5f5;
            margin-bottom: 15px;
        }
        .adopt-btn {
            padding: 10px 15px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            color: white;
            background-color: #ff6f61;
            transition: 0.3s;
        }
        .adopt-btn:hover {
            background-color: #e05a50;
        }
        .adopt-btn.red {
            background-color: red;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="brand">🐶 Your Listed Dogs</div>
    <a href="giver.html" class="adopt-btn">+ Add New Dog</a>

    <section class="dog-list">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='dog-card'>";
                echo "<img src='" . htmlspecialchars($row['image']) . "' alt='Dog Image' class='dog-img'>";
                echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
                echo "<p><strong>Breed:</strong> " . htmlspecialchars($row['breed']) . "</p>";
                echo "<p><strong>Age:</strong> " . htmlspecialchars($row['age']) . " months</p>";
                echo "<form action='delete_dog.php' method='POST' onsubmit=\"return confirm('Delete this dog?');\">";
                echo "<input type='hidden' name='dog_id' value='" . htmlspecialchars($row['id']) . "'>";
                echo "<button type='submit' class='adopt-btn red'>Delete</button>";
                echo "</form>";
                echo "</div>";
            }
        } else {
            echo "<p>No dogs added yet.</p>";
        }
        ?>
    </section>

    <h2 style="color:#ff6f61; text-align:center;">📩 Incoming Adoption Requests</h2>

    <section class="dog-list">
        <?php
        // 🔹 Fetch adoption requests
        $query = "
            SELECT ar.id AS request_id, u.name AS adopter_name, u.email AS adopter_email, 
                   p.name AS dog_name, p.image AS dog_image
            FROM adoption_requests ar
            JOIN pets p ON ar.pet_id = p.id
            JOIN users u ON ar.adopter_email = u.email
            WHERE p.giver_email = ? AND ar.status = 'Pending'
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $giver_email);
        $stmt->execute();
        $requests = $stmt->get_result();

        if ($requests->num_rows > 0) {
            while ($row = $requests->fetch_assoc()) {
                echo "<div class='dog-card'>";
                echo "<img src='" . $row['dog_image'] . "' alt='Dog Image' class='dog-img'>";
                echo "<h3>" . htmlspecialchars($row['dog_name']) . "</h3>";
                echo "<p><strong>Requested by:</strong> " . htmlspecialchars($row['adopter_name']) . " (" . htmlspecialchars($row['adopter_email']) . ")</p>";
                echo "<form action='handle_request.php' method='POST' style='margin-top:10px;'>";
                echo "<input type='hidden' name='request_id' value='" . $row['request_id'] . "'>";
                echo "<button type='submit' name='action' value='accept' class='adopt-btn'>Accept</button> ";
                echo "<button type='submit' name='action' value='reject' class='adopt-btn' style='background-color: grey;'>Reject</button>";
                echo "</form>";
                echo "</div>";
            }
        } else {
            echo "<p style='text-align:center;'>No pending adoption requests.</p>";
        }

        $stmt->close();
        $conn->close();
        ?>
    </section>
</div>

</body>
</html>
