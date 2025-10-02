<?php
session_start();
include("connection.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_id = $_POST['item_id'];

    // Fetch the item data from the database
    $query = "SELECT * FROM lost_items WHERE item_id = '$item_id'";
    $result = mysqli_query($con, $query);
    $item = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lost Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Lost Item</h2>
    <form action="update_lost_item.php" method="post">
        <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['item_id']); ?>">
        <div class="mb-3">
            <label for="user_id" class="form-label">User ID</label>
            <input type="text" class="form-control" id="user_id" name="user_id" value="<?php echo htmlspecialchars($item['user_id']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="submitter_name" class="form-label">Submitter Name</label>
            <input type="text" class="form-control" id="submitter_name" name="submitter_name" value="<?php echo htmlspecialchars($item['submitter_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($item['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($item['description']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="picture_path" class="form-label">Picture Path</label>
            <input type="text" class="form-control" id="picture_path" name="picture_path" value="<?php echo htmlspecialchars($item['picture_path']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="date_found" class="form-label">Date Found</label>
            <input type="date" class="form-control" id="date_found" name="date_found" value="<?php echo htmlspecialchars($item['date_found']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="time_found" class="form-label">Time Found</label>
            <input type="time" class="form-control" id="time_found" name="time_found" value="<?php echo htmlspecialchars($item['time_found']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="location_found" class="form-label">Location Found</label>
            <input type="text" class="form-control" id="location_found" name="location_found" value="<?php echo htmlspecialchars($item['location_found']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="location_found" class="form-label">Status</label>
            <input type="text" class="form-control" id="status" name="status" value="<?php echo htmlspecialchars($item['status']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="item_type" class="form-label">Item Type</label>
            <input type="text" class="form-control" id="item_type" name="item_type" value="<?php echo htmlspecialchars($item['item_type']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
</body>
</html>
