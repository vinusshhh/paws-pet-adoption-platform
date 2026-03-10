<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['email']) || $_SESSION['user_role'] !== 'adopter') {
    die("Access denied.");
}

$adopter_email = $_SESSION['email'];

$query = "
    SELECT 
        p.name AS dog_name,
        p.image,
        ar.status,
        u.name AS giver_name,
        u.contact AS giver_contact,
        u.location AS giver_location
    FROM adoption_requests ar
    JOIN pets p ON ar.pet_id = p.id
    JOIN users u ON p.giver_email = u.email
    WHERE ar.adopter_email = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $adopter_email);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Adoption Requests</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #ff9a9e, #fad0c4);
            padding: 40px 20px;
            margin: 0;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        h2 {
            color: #ff6f61;
            text-align: center;
            margin-bottom: 30px;
        }

        .request-card {
            background: #fff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .dog-img {
            width: 150px;
            height: 120px;
            object-fit: cover;
            border-radius: 10px;
            background-color: #f5f5f5;
        }

        .info {
            flex-grow: 1;
        }

        .info p {
            margin: 5px 0;
            font-size: 15px;
        }

        .status {
            font-weight: bold;
            color: #ff6f61;
        }

        .accepted {
            color: green;
        }

        .rejected {
            color: red;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>My Adoption Requests</h2>

    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='request-card'>";
            echo "<img src='" . htmlspecialchars($row['image']) . "' alt='Dog Image' class='dog-img'>";
            echo "<div class='info'>";
            echo "<p><strong>Dog:</strong> " . htmlspecialchars($row['dog_name']) . "</p>";
            echo "<p><strong>Status:</strong> <span class='status " . strtolower($row['status']) . "'>" . $row['status'] . "</span></p>";

            if ($row['status'] === 'Accepted') {
                echo "<p><strong>Giver Name:</strong> " . htmlspecialchars($row['giver_name']) . "</p>";
                echo "<p><strong>Contact:</strong> " . htmlspecialchars($row['giver_contact']) . "</p>";
                echo "<p><strong>Location:</strong> " . htmlspecialchars($row['giver_location']) . "</p>";
            }

            echo "</div></div>";
        }
    } else {
        echo "<p style='text-align:center;'>No adoption requests made yet.</p>";
    }

    $stmt->close();
    $conn->close();
    ?>
</div>

</body>
</html>