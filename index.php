<?php 
include 'db.php'; // Ensure this is included at the top 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Malware Detection System</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Intrusion Detection System</h1>
    
    <h2>Upload File for Malware Scan</h2>
    <form action="scan.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="file" required>
        <button type="submit">Scan File</button>
    </form>

    <h2>Ganzan Detected Intrusions</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>IP Address</th>
            <th>Detected Pattern</th>
            <th>File Name</th>
            <th>Timestamp</th>
        </tr>
        <?php
        if (isset($conn)) { // Check if $conn exists before using it
            $result = $conn->query("SELECT * FROM intrusions ORDER BY timestamp DESC");
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['ip_address']}</td>
                        <td>{$row['detected_pattern']}</td>
                        <td>{$row['file_name']}</td>
                        <td>{$row['timestamp']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5' style='color:red;'>Database connection failed.</td></tr>";
        }
        ?>
    </table>
</body>
</html>
