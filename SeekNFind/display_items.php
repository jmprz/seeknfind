<?php
include("connection.php");
include("functions.php");

// Fetch lost items and their corresponding user names
$query = "SELECT lost_items.*, CONCAT(user.first_name, ' ', user.last_name) AS submitter_name FROM lost_items INNER JOIN user ON lost_items.user_id = user.user_id";
$result = mysqli_query($con, $query);

if ($result) {
    // Display the items
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<div>";
        echo "<h3>{$row['name']}</h3>";
        echo "<img src='{$row['picture_path']}' alt='Lost item picture'>";
        echo "<p>Description: {$row['description']}</p>";
        echo "<p>Date Found: {$row['date_found']}</p>";
        echo "<p>Time Found: {$row['time_found']}</p>";
        echo "<p>Location: {$row['location_found']}</p>";
        echo "<p>Submitted by: {$row['submitter_name']}</p>";
        echo "</div>";
    }
} else {
    echo "No lost items found.";
}
?>

