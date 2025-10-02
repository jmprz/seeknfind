<?php 
session_start();

include("connection.php");
include("functions.php");

$user_data = check_login($con);
$full_name = $user_data['first_name'] . ' ' . $user_data['last_name'];
$account_type = $user_data['account_type'];
$user_id = $user_data['user_id'];

// Retrieve missing items reported by the user
$missing_items_query = "SELECT * FROM report_items WHERE user_id = ? AND item_type = 'lost'";
$stmt_missing = $con->prepare($missing_items_query);
if (!$stmt_missing) {
    die("Error in missing items query: " . $con->error);
}
$stmt_missing->bind_param("i", $user_id);
$stmt_missing->execute();
$missing_items_result = $stmt_missing->get_result();

// Retrieve items posted by the user
$found_items_query = "SELECT * FROM lost_items WHERE user_id = ?";
$stmt_found = $con->prepare($found_items_query);
if (!$stmt_found) {
    die("Error in found items query: " . $con->error);
}
$stmt_found->bind_param("i", $user_id);
$stmt_found->execute();
$found_items_result = $stmt_found->get_result();

// Retrieve claim items by the user
$claim_items_query = "SELECT * FROM lost_items_claim WHERE user_id = ?";
$stmt_claim = $con->prepare($claim_items_query);
if (!$stmt_claim) {
    die("Error in found items query: " . $con->error);
}
$stmt_claim->bind_param("i", $user_id);
$stmt_claim->execute();
$claim_items_result = $stmt_claim->get_result();

$profile = $user_data['profile_picture'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
<link rel="manifest" href="favicon/site.webmanifest">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <title>SeekNFind</title>
</head>
<body>
<div class="container-fluid overflow-hidden">
    <div class="row vh-100 overflow-auto">
    <div class="col-sm-auto col-12 snf_nav_color d-flex flex-sm-column order-sm-0 order-1 px-sm-2 px-0 mobile-fixed-bottom fixed-nav">
    <div class="d-flex flex-sm-column flex-row flex-grow-1 align-items-sm-start text-dark">
        <div class="nav flex-sm-column flex-row flex-nowrap flex-shrink-1 flex-sm-grow-0 flex-grow-1 mb-sm-auto mb-0 justify-content-center align-items-center align-items-sm-start" id="menu">
        <div class="d-flex align-items-center mb-4">   
        <img class="mb-1 me-1 d-none d-md-block" src="img/logosnf.svg" alt="Logo" width="40px">
        <h2 class="fw-bold d-none d-md-block">SeekNFind</h2>
        </div>
            <a href="index.php" class="snf_btn btn tablinks fs-4 fw-semibold rounded-3 mb-3">
                <i class='ms-2 bx bx-home-alt'></i><span class="me-3 d-none d-sm-inline">Home</span>
            </a>
            <a class="snf_btn btn tablinks fs-4 fw-semibold rounded-3 mb-3 active" id="defaultOpen" onclick="openTab(event, 'dashboard-content')">
                <i class='ms-2 bx bx-collection'></i><span class="me-3 d-none d-sm-inline">My Items</span>
            </a>
            <a class="snf_btn btn tablinks fs-4 fw-semibold rounded-3 mb-3 d-block d-sm-none" data-bs-toggle="modal" data-bs-target="#mobileItemModal">
                <i class='ms-2 bx bx-plus-circle mt-2'></i><span class="me-3 d-none d-sm-inline">Post</span>
            </a>
            <a href="settings.php" class="snf_btn btn tablinks fs-4 fw-semibold rounded-3 mb-3">
                <i class='ms-2 bx bx-cog'></i><span class="me-3 d-none d-sm-inline">Settings</span>
            </a>
            <a href="profile.php" class="snf_btn btn tablinks fs-4 fw-semibold rounded-3 mb-3">
            <img src="<?php echo htmlspecialchars($profile); ?>" alt="hugenerd" width="35" height="35" class="rounded-circle ms-1">
                <div class="d-none d-sm-inline d-flex flex-column align-items-start text-start">
                    <div class="name"><?php echo htmlspecialchars($full_name); ?></div>
                    <div class="account-type"><?php echo htmlspecialchars($account_type); ?></div>
                </div>
            </a>
        </div>
    </div>
</div>
        <div class="col d-flex flex-column h-sm-100 order-sm-1 order-0 content-with-fixed-footer content-area mb-5">
            <div class="tab-content">
<div id="dashboard-content" class="tab-pane fade show active">
<div class="row overflow-auto">
<div class="container pt-4">
<div class="d-flex">
                <h1 class="fw-bold">My Items</h1>
            </div>
    <!-- Filter Dropdown -->
    <div class="d-flex justify-content-between align-items-center">
        <div></div>
        <!-- Filter Dropdown -->
        <form class="d-inline-block">
            <select id="status_filter" class="form-select" style="width: auto;">
                <option value="all">All Items</option>
                <option value="pending">Pending Items</option>
                <option value="approved">Available Items</option>
                <option value="completed">Completed Items</option>
                <option value="missing">Missing Items</option>
            </select>
        </form>
    </div>

<!-- Display All Items -->
<div id="all-items" class="row mt-3">
    <?php if ($missing_items_result->num_rows > 0 || $found_items_result->num_rows > 0 || $claim_items_result->num_rows > 0): ?>
        <div class="container">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-2">
                <?php while ($row = $missing_items_result->fetch_assoc()): ?>
                    <div class="col item-card" data-status="missing">
                        <div class="card mb-3">
                            <div class="card-img-container">
                                <img src="<?php echo htmlspecialchars($row['picture_path']); ?>" class="img-fluid img-thumbnail" alt="User Item">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title fw-semibold">
                                    <?php echo htmlspecialchars($row['name']); ?>
                                    <span class="badge bg-danger text-dark">Missing</span>
                                </h5>
                                <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn snf_btn snf_view fw-semibold" data-bs-toggle="modal" data-bs-target="#userItemModal<?php echo $row['item_id']; ?>">
                                        View Item
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="userItemModal<?php echo $row['item_id']; ?>" tabindex="-1" aria-labelledby="userItemModalLabel<?php echo $row['item_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="userItemModalLabel<?php echo $row['item_id']; ?>"><?php echo htmlspecialchars($row['name']); ?></h5>
                                </div>
                                <div class="modal-body">
                                    <img src="<?php echo htmlspecialchars($row['picture_path']); ?>" class="img-fluid mb-3" alt="User Item">
                                    <h5><i class='bx bx-category-alt me-2'></i>Item Type</h5>
                                    <p><span class="btn rounded-2 bg-danger text-dark">Missing</span></p>
                                    <h5><i class='bx bx-pencil me-2'></i>Description</h5>
                                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                                    <h5><i class='bx bx-calendar me-2'></i>Date Lost</h5>
                                    <p><?php echo htmlspecialchars($row['date_lost']); ?></p>
                                    <h5><i class='bx bx-map me-2'></i>Location Lost</h5>
                                    <p><?php echo htmlspecialchars($row['location_lost']); ?></p>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn snf_report" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
                
                <?php while ($row = $found_items_result->fetch_assoc()): ?>
                    <div class="col item-card" data-status="<?php echo htmlspecialchars($row['status']); ?>">
                        <div class="card mb-3">
                            <div class="card-img-container">
                                <img src="<?php echo htmlspecialchars($row['picture_path']); ?>" class="img-fluid img-thumbnail" alt="User Item">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title fw-semibold">
                                    <?php echo htmlspecialchars($row['name']); ?>
                                    <span class="badge <?php echo $row['status'] == 'pending' ? 'bg-warning text-dark' : ($row['status'] == 'approved' ? 'bg-success' : 'bg-secondary'); ?>">
                                        <?php echo $row['status'] == 'approved' ? 'Found' : ucfirst(htmlspecialchars($row['status'])); ?>
                                    </span>
                                </h5>
                                <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn snf_btn snf_view fw-semibold" data-bs-toggle="modal" data-bs-target="#userItemModal<?php echo $row['item_id']; ?>">
                                        View Item
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="userItemModal<?php echo $row['item_id']; ?>" tabindex="-1" aria-labelledby="userItemModalLabel<?php echo $row['item_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="userItemModalLabel<?php echo $row['item_id']; ?>"><?php echo htmlspecialchars($row['name']); ?></h5>
                                </div>
                                <div class="modal-body">
                                    <img src="<?php echo htmlspecialchars($row['picture_path']); ?>" class="img-fluid mb-3" alt="User Item">
                                    <h5><i class='bx bx-category-alt me-2'></i>Item Type</h5>
                                    <p><span class="btn rounded-2 <?php echo $row['status'] == 'pending' ? 'bg-warning text-dark' : ($row['status'] == 'approved' ? 'bg-success' : 'bg-secondary'); ?>">
                                        <?php echo $row['status'] == 'approved' ? 'Found' : ucfirst($row['status']); ?>
                                    </span></p>
                                    <h5><i class='bx bx-pencil me-2'></i>Description</h5>
                                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                                    <h5><i class='bx bx-calendar me-2'></i>Date Found</h5>
                                    <p><?php echo htmlspecialchars($row['date_found']); ?></p>
                                    <h5><i class='bx bx-map me-2'></i>Location Found</h5>
                                    <p><?php echo htmlspecialchars($row['location_found']); ?></p>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn snf_report" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
                
                <?php while ($row = $claim_items_result->fetch_assoc()): ?>
                    <div class="col item-card" data-status="claim">
                        <div class="card mb-3">
                            <div class="card-img-container">
                                <img src="<?php echo htmlspecialchars($row['picture_path']); ?>" class="img-fluid img-thumbnail" alt="User Item">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title fw-semibold">
                                    <?php echo htmlspecialchars($row['name']); ?>
                                    <span class="badge <?php echo $row['claim_status'] == 'Pending' ? 'bg-warning text-dark' : ($row['claim_status'] == 'Approved' ? 'bg-success' : 'bg-primary'); ?>">
                                        <?php echo $row['claim_status'] == 'Pending' ? 'On Process' : ucfirst(htmlspecialchars($row['claim_status'])); ?>
                                    </span>
                                </h5>
                                <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn snf_btn snf_view fw-semibold" data-bs-toggle="modal" data-bs-target="#claimItemModal<?php echo $row['claim_id']; ?>">
                                        View Claim
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="claimItemModal<?php echo $row['claim_id']; ?>" tabindex="-1" aria-labelledby="claimItemModalLabel<?php echo $row['claim_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="claimItemModalLabel<?php echo $row['claim_id']; ?>"><?php echo htmlspecialchars($row['name']); ?></h5>
                                </div>
                                <div class="modal-body">
                                    <img src="<?php echo htmlspecialchars($row['picture_path']); ?>" class="img-fluid mb-3" alt="User Item">
                                    <h5><i class='bx bx-category-alt me-2'></i>Status</h5>
                                    <p><span class="btn rounded-2 <?php echo $row['claim_status'] == 'Pending' ? 'bg-warning text-dark' : ($row['claim_status'] == 'Approved' ? 'bg-success' : 'bg-primary'); ?>">
                                        <?php echo $row['claim_status'] == 'Pending' ? 'On Process' : ucfirst($row['claim_status']); ?>
                                    </span></p>
                                    <h5><i class='bx bx-pencil me-2'></i>Description</h5>
                                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                                    <h5><i class='bx bx-calendar me-2'></i>Date Claimed</h5>
                                    <p><?php echo htmlspecialchars($row['date_found']); ?></p>
                                    <h5><i class='bx bx-map me-2'></i>Location Claimed</h5>
                                    <p><?php echo htmlspecialchars($row['location_found']); ?></p>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn snf_report" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="container">
            <div class="d-flex justify-content-center">
                <div class="alert alert-danger text-center w-75 mt-3" role="alert">
                    No item/s to show.
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>


              
<!-- Mobile Item Modal -->
<div class="modal fade" id="mobileItemModal" tabindex="-1" aria-labelledby="mobileItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body d-flex flex-column align-items-center">
                <button type="button" class="btn snf_add w-100 mb-3 fs-5 rounded-3" data-bs-toggle="modal" data-bs-target="#uploadItemModal"><i class='bx bx-plus'></i>Add Item</button>
                <button type="button" class="btn snf_report w-100 fs-5 rounded-3" data-bs-toggle="modal" data-bs-target="#reportItemModal"><i class='bx bx-bell'></i>Report Item</button>
            </div>
        </div>
    </div>
</div>
                    
<!-- Add Lost Item Modal -->
<div class="modal fade" id="uploadItemModal" tabindex="-1" aria-labelledby="uploadItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadItemModalLabel">Add Found Item</h5>
            </div>
            <div class="modal-body">
                <form id="uploadForm" action="post.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name:</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description:</label>
                        <textarea class="form-control" id="description" name="description" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date_found" class="form-label">Date Found:</label>
                            <input type="date" class="form-control" id="date_found" name="date_found" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="time_found" class="form-label">Time Found:</label>
                            <input type="time" class="form-control" id="time_found" name="time_found" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="location_found" class="form-label">Location Found:</label>
                        <input type="text" class="form-control" id="location_found" name="location_found" required>
                    </div>
                    <div class="mb-3">
    <label for="customFileInput" class="form-label">Select Image:</label>
    <div class="input-group">
        <label class="input-group-btn">
            <span class="btn snf_view">
                Browse <input type="file" id="file" name="file" style="display: none;" onchange="document.getElementById('customFileInputText').value = this.files[0].name;">
            </span>
        </label>
        <input type="text" class="form-control" id="customFileInputText" placeholder="Choose file..." readonly>
    </div>
</div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn snf_view">Submit</button>
                        <button type="button" class="btn snf_report" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Report Lost Item Modal -->
<div class="modal fade" id="reportItemModal" tabindex="-1" aria-labelledby="reportItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportItemModalLabel">Report Lost Item</h5>
            </div>
            <div class="modal-body">
                <form id="reportForm" action="report.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name:</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description:</label>
                        <textarea class="form-control" id="description" name="description" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date_lost" class="form-label">Date Lost:</label>
                            <input type="date" class="form-control" id="date_lost" name="date_lost" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="time_lost" class="form-label">Time Lost:</label>
                            <input type="time" class="form-control" id="time_lost" name="time_lost" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="location_lost" class="form-label">Location Lost:</label>
                        <input type="text" class="form-control" id="location_lost" name="location_lost" required>
                    </div>
                    <div class="mb-3">
    <label for="customFileInput" class="form-label">Select Image:</label>
    <div class="input-group">
        <label class="input-group-btn">
            <span class="btn snf_view">
                Browse <input type="file" id="file" name="file" style="display: none;" onchange="document.getElementById('customFileInputText').value = this.files[0].name;">
            </span>
        </label>
        <input type="text" class="form-control" id="customFileInputText" placeholder="Choose file..." readonly>
    </div>
</div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn snf_view">Submit</button>
                        <button type="button" class="btn snf_report" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
   // JavaScript to filter items
document.getElementById('status_filter').addEventListener('change', function() {
    var selectedStatus = this.value;
    var items = document.querySelectorAll('.item-card');

    items.forEach(function(item) {
        if (selectedStatus === 'all' || item.getAttribute('data-status') === selectedStatus) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});
    </script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var triggerTabList = [].slice.call(document.querySelectorAll('#myTab button'))
    triggerTabList.forEach(function(triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl)
        triggerEl.addEventListener('click', function(event) {
            event.preventDefault()
            tabTrigger.show()
        })
    })
});
</script>
<script>
    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tab-pane");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].classList.remove("show", "active");
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove("active");
        }
        document.getElementById(tabName).classList.add("show", "active");
        evt.currentTarget.classList.add("active");
    }

    document.getElementById("defaultOpen").click();
</script>
    <script>
       function clearSearch() {
        window.location.href = 'index.php'; // Replace 'index.php' with the URL of your page displaying regular display items
    }
    </script>
    <script>
     var darkMode = false;

// default to system setting
if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
    darkMode = true;
}

// preference from localStorage should overwrite
if (localStorage.getItem('theme') === 'dark') {
    darkMode = true;
} else if (localStorage.getItem('theme') === 'light') {
    darkMode = false;
}

if (darkMode) {
    document.body.classList.add('dark');
}

document.addEventListener('DOMContentLoaded', () => {
    const themeToggle = document.getElementById('theme-toggle');
    const darkModeStatus = document.getElementById('darkModeStatus');

    // Set the initial state of the switch and the status text
    themeToggle.checked = darkMode;
    darkModeStatus.textContent = darkMode ? "Dark Mode is On" : "Dark Mode is Off";

    themeToggle.addEventListener('change', () => {
        document.body.classList.toggle('dark');
        const isDarkMode = document.body.classList.contains('dark');
        localStorage.setItem('theme', isDarkMode ? 'dark' : 'light');
        darkModeStatus.textContent = isDarkMode ? "Dark Mode is On" : "Dark Mode is Off";

        
                if (window.Android) {
                    window.Android.onThemeChanged(isDarkMode);
                }
            });

            if (window.Android) {
                window.Android.onThemeChanged(darkMode);
            }
});
    </script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>
