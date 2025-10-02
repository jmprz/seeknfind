<?php
// Include the file with database connection
include("connection.php");

// Check if the item ID is provided in the URL query parameters
if (isset($_GET['id'])) {
    // Retrieve the item ID from the URL
    $item_id = $_GET['id'];

    // Perform database query to retrieve item details based on ID
    $query = "SELECT * FROM lost_items WHERE id = $item_id";
    // Execute the query
    $result = mysqli_query($con, $query);

    // Check if the query was successful and if there is exactly one matching item
    if ($result && mysqli_num_rows($result) === 1) {
        // Fetch the item details from the result
        $item = mysqli_fetch_assoc($result);

        // Display the item details
        echo "<img src='{$item['picture_path']}' alt='Lost item picture'>";
        echo "<h1>{$item['name']}</h1>";
        echo "<p>Description: {$item['description']}</p>";
        echo "<p>Date Found: {$item['date_found']}</p>";
        echo "<p>Time Found: {$item['time_found']}</p>";
        echo "<p>Location Found: {$item['location_found']}</p>";
        // Add more item details as needed
    } else {
        // No item found with the provided ID
        echo "Item not found.";
    }
} else {
    // Item ID is not provided in the URL
    echo "Item ID is missing.";
}
?>
