<?php
// Include the file with database connection
include("connection.php");

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Retrieve the search query from the form submission
    $search_query = $_GET["query"];

    // Perform database query to search for items
    $query = "SELECT * FROM lost_items WHERE name LIKE '%$search_query%' OR description LIKE '%$search_query%'";
    // Execute the query and fetch results
    $result = mysqli_query($con, $query);

    // Check if there are any search results
    if ($result && mysqli_num_rows($result) > 0) {
        // Display search results
        while ($row = mysqli_fetch_assoc($result)) {
            // Display item details
            echo "<p><a href='item_details.php?id={$row['id']}'>{$row['name']}</a></p>";
            echo "<p>{$row['description']}</p>";
            // Add more item details as needed
        }
    } else {
        // No search results found
        echo "No items found.";
    }
}
?>
