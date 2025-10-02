<?php
session_start();

include("connection.php");
include("functions.php");

$user_data = check_login($con);
$full_name = $user_data['first_name'] . ' ' . $user_data['last_name'];
$account_type = $user_data['account_type'];
$user_id = $user_data['user_id'];
$profile = $user_data['profile_picture'];
$email = isset($user_data['email_address']) ? $user_data['email_address'] : '';
$first_name = $user_data['first_name'];
$last_name = $user_data['last_name'];
$program = isset($user_data['program']) ? $user_data['program'] : '';
$section = isset($user_data['year_section']) ? $user_data['year_section'] : '';
$home_address = isset($user_data['home_address']) ? $user_data['home_address'] : '';
$year = $program . ' ' . $section;

// Count the number of lost items
$missing_items_query = "SELECT COUNT(*) as report_count FROM report_items WHERE user_id='$user_id'";
$missing_items_result = mysqli_query($con, $missing_items_query);
$missing_items_count = mysqli_fetch_assoc($missing_items_result)['report_count'];

// Count the number of found items
$found_items_query = "SELECT COUNT(*) as lost_count FROM lost_items WHERE user_id='$user_id'";
$found_items_result = mysqli_query($con, $found_items_query);
$found_items_count = mysqli_fetch_assoc($found_items_result)['lost_count'];

// Handle edit profile form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_profile'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $home_address = $_POST['home_address'];
    $program = $_POST['program'];
    $section = $_POST['section'];

    // Handle image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
        $profile_image = $_FILES['profile_image']['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($profile_image);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES['profile_image']['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                // Update the profile image in the database
                $query = "UPDATE user SET profile_picture='$target_file' WHERE user_id='$user_id'";
                mysqli_query($con, $query);
                $profile = $target_file;
            }
        }
    }

    // Handle image deletion
    if (isset($_POST['delete_image']) && $_POST['delete_image'] == '1') {
        $default_image = 'img/default_user.svg';
        $query = "UPDATE user SET profile_picture='$default_image' WHERE user_id='$user_id'";
        mysqli_query($con, $query);
        $profile = $default_image;
    }

    // Update user details in the database
    $query = "UPDATE user SET first_name='$first_name', last_name='$last_name', home_address='$home_address', program='$program', year_section='$section' WHERE user_id='$user_id'";
    if (mysqli_query($con, $query)) {
        header("Location: profile.php");
        die;
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet"/>
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
            <a href="my_items.php" class="snf_btn btn tablinks fs-4 fw-semibold rounded-3 mb-3">
                <i class='ms-2 bx bx-collection'></i><span class="me-3 d-none d-sm-inline">My Items</span>
            </a>
            <a class="snf_btn btn tablinks fs-4 fw-semibold rounded-3 mb-3 d-block d-sm-none" data-bs-toggle="modal" data-bs-target="#mobileItemModal">
                <i class='ms-2 bx bx-plus-circle mt-2'></i><span class="me-3 d-none d-sm-inline">Post</span>
            </a>
            <a href="settings.php" class="snf_btn btn tablinks fs-4 fw-semibold rounded-3 mb-3">
                <i class='ms-2 bx bx-cog'></i><span class="me-3 d-none d-sm-inline">Settings</span>
            </a>
            <a class="snf_btn btn tablinks fs-4 fw-semibold rounded-3 mb-3 active profile_mobile" id="defaultOpen" onclick="openTab(event, 'profile-content')">
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
        <div id="profile-content" class="tab-pane fade show active">
                <div class="row overflow-auto">
                    <div class="container pt-4">
                        <h1 class="fw-bold">Profile</h1>
                        <div class="d-flex justify-content-between align-items-center">
                            <h1></h1>
                            <div class="text-center">
                                <button class="btn snf_view me-2 fs-5 rounded-3" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                    <i class='bx bx-edit me-1'></i>Edit
                                </button>
                                <a class="btn snf_view fs-5 rounded-3" href="logout.php" type="button">
                                    <i class='bx bx-log-out me-1'></i>Log Out
                                </a>
                            </div>
                        </div>
                        <div class="card mt-4">
                            <div class="row g-0">
                                <div class="col-md-4 d-flex justify-content-center align-items-center">
                                    <img src="<?php echo htmlspecialchars($profile); ?>" class="img-fluid profile-image profile-image-fixed" id="profileImage" alt="Profile Image">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body profile-info">
                                        <h1 class="card-title"><?php echo htmlspecialchars($full_name); ?></h1>
                                        <p class="card-text fs-4"><i class="ri-graduation-cap-line me-2"></i><?php echo htmlspecialchars($year); ?></p>
                                        <p class="card-text fs-4"><i class='bx bx-map me-2' ></i><?php echo htmlspecialchars($home_address); ?></p>
                                        <div class="row text-center mt-4">
                    <div class="col-md-6 custom-col">
                    <i class="ri-question-line" style="font-size: 2.5rem;"></i>
                        <p class="count-text">Missing Item</p>
                        <p class="card-text fs-3"><?php echo $missing_items_count; ?></p>
                    </div>
                    <div class="col-md-6 custom-col">
                    <i class="ri-checkbox-circle-line" style="font-size: 2.5rem;"></i>
                        <p class="count-text">Found Item</p>
                        <p class="card-text fs-3"><?php echo $found_items_count; ?></p>
                    </div>
                </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


   <!-- Edit Profile Modal -->
   <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form id="editProfileForm" enctype="multipart/form-data" method="POST" action="">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                                </div>
                                <div class="modal-body">
                                <div class="mb-3">
                                    <label for="customFileInput" class="form-label">Select Profile Image: (Recommended Size: 300px × 300px)</label>
                                    <div class="input-group">
                                        <label class="input-group-btn">
                                            <span class="btn snf_view">
                                                Browse <input type="file" id="profileImageInput" name="profile_image" style="display: none;" onchange="document.getElementById('customFileInputText').value = this.files[0].name;">
                                            </span>
                                        </label>
                                        <input type="text" class="form-control" id="customFileInputText" placeholder="Choose file..." readonly>
                                    </div>
                                    <center><div>
                                    <img id="imagePreview" src="<?php echo htmlspecialchars($profile); ?>" alt="Image Preview" class="rounded-circle preview_image">
                                </div></center>
                                </div>
                                <div class="mb-3">
                                    <input class="form-check-input custom-switch" type="checkbox" id="deleteImage" name="delete_image" value="1">
                                    <label for="deleteImage">Delete current profile image</label>
                                </div>
                                    <div class="mb-3">
                                        <label for="firstName" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="firstName" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="lastName" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="lastName" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="homeAddress" class="form-label">Home Address</label>
                                        <input type="text" class="form-control" id="homeAddress" name="home_address" value="<?php echo htmlspecialchars($home_address); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="program" class="form-label">Program</label>
                                        <input type="text" class="form-control" id="program" name="program" value="<?php echo htmlspecialchars($program); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="section" class="form-label">Year & Section</label>
                                        <input type="text" class="form-control" id="section" name="section" value="<?php echo htmlspecialchars($section); ?>" required>
                                    </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" name="edit_profile" class="btn snf_view">Submit</button>
                                    <button type="button" class="btn snf_report" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div> <!-- End of profile-content -->
        </div>
    </div>
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
<script>

document.getElementById('profileImageInput').addEventListener('change', function(event) {
                var reader = new FileReader();
                reader.onload = function() {
                    var output = document.getElementById('profileImage');
                    var preview = document.getElementById('imagePreview');
                    output.src = reader.result;
                    preview.style.display = 'block';
                    preview.src = reader.result;
                    document.getElementById('customFileInputText').value = event.target.files[0].name;
                };
                reader.readAsDataURL(event.target.files[0]);
            });

            function deleteProfileImage() {
                document.getElementById('profileImage').src = 'img/default_user.svg';
                document.getElementById('imagePreview').src = 'img/default_user.svg';
                var editProfileModal = new bootstrap.Modal(document.getElementById('editProfileModal'));
                editProfileModal.hide();
            }

            var editProfileModal = document.getElementById('editProfileModal');
            editProfileModal.addEventListener('show.bs.modal', function (event) {
                document.getElementById('imagePreview').src = document.getElementById('profileImage').src;
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
    });
});
    </script>

    <script>
         document.getElementById('customFileInput').addEventListener('change', function() {
        document.getElementById('customFileInputText').value = this.files[0].name;
    });
    </script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>
