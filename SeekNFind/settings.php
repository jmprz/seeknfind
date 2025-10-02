<?php 
session_start();

include("connection.php");
include("functions.php");

$user_data = check_login($con);
$full_name = $user_data['first_name'] . ' ' . $user_data['last_name'];
$account_type = $user_data['account_type'];
$user_id = $user_data['user_id'];
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
            <a class="snf_btn btn tablinks fs-4 fw-semibold rounded-3 mb-3 active" id="defaultOpen" onclick="openTab(event, 'settings-content')">
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
<!-- Settings -->
<div id="settings-content" class="tab-pane fade show active">
    <main class="row overflow-auto">
        <div class="col pt-4">
            <h1 class="fw-bold">Settings</h1>
            <div class="container p-0 mt-3 me-5">
                <!-- Dark Mode Card -->
                <div class="card p-3 mb-4">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="bx bx-moon" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1">Dark Mode</h5>
                            <p class="mb-0" id="darkModeStatus">Dark Mode is Off</p>
                        </div>
                        <div>
                            <div class="form-check form-switch">
                                <input class="form-check-input custom-switch" type="checkbox" id="theme-toggle" style="transform: scale(1.5);">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card p-3 mb-4">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                        <i class="ri-android-line" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1">SeekNFind Android</h5>
                            <p class="mb-0">Download the Android App</p>
                        </div>
                        <div class="ms-auto">
                        <i class='bx bxs-chevron-right' style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>

                <!-- FAQs Card -->
                <div class="card p-3 mb-4" data-bs-toggle="modal" data-bs-target="#faqsModal" style="cursor: pointer;">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                        <i class="ri-question-line" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1">FAQs</h5>
                            <p class="mb-0">Frequently Asked Questions</p>
                        </div>
                        <div class="ms-auto">
                        <i class='bx bxs-chevron-right' style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>

                <!-- About SeekNFind Card -->
                <div class="card p-3 mb-4" data-bs-toggle="modal" data-bs-target="#aboutModal" style="cursor: pointer;">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                        <i class="ri-information-line" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1">About SeekNFind</h5>
                            <p class="mb-0">Version 1.0</p>
                        </div>
                        <div class="ms-auto">
                        <i class='bx bxs-chevron-right' style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
</div>

<!-- FAQs Modal -->
<div class="modal fade" id="faqsModal" tabindex="-1" aria-labelledby="faqsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="faqsModalLabel">FAQs</h5>
            </div>
            <div class="modal-body">
                <!-- Add FAQs content here -->
                <p> <span class="fw-semibold">Question:</span> Is SeekNFind free to use? <br><span class="fw-semibold">Answer:</span> It is 100% free web and mobile application.</p>
                <p><span class="fw-semibold">Question: </span>Does the mobile application support iOS?<br>
                <span class="fw-semibold">Answer: </span> As of the moment, it is only available for Android devices.</p>
                <p><span class="fw-semibold">Question: </span> Can student / faculty members from different college use SeekNFind?<br>
                <span class="fw-semibold">Answer:</span> No, it is only for students & faculty members of College of Computing Studies (CCS).</p>
                <p><span class="fw-semibold">Question:</span> What can I do if my item was found, but I don't have pictures (proof) for verification?<br>
                <span class="fw-semibold">Answer: </span> You can send a reference picture that resembles the item and give other evidences.</p>
                <p><span class="fw-semibold">Question:</span> Can I go directly to the Lost & Found room to claim an item?<br>
                <span class="fw-semibold">Answer:</span> Make sure that you submit a verification request from the app before going to the Lost & Found room so that the admins will be notified and can assist you in claiming your item efficiently. </p>
                   <!-- Card with email and clipboard icons -->
                   <p class="mt-3">Do you have other concerns? Feel free to message</p>
                   <div class="card email_card p-3 mb-4">
        <div class="d-flex align-items-center">
        <i class='bx bx-envelope' style="font-size: 2rem;"></i>
            <div class="email-address mx-3">app@gmail.com</div>
    </div>
    </div>
                
                <div class="d-grid gap-2">
            <button type="button" class="btn snf_report" data-bs-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
    </div>
</div>

<!-- About SeekNFind Modal -->
<div class="modal fade" id="aboutModal" tabindex="-1" aria-labelledby="aboutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="aboutModalLabel">About SeekNFind</h5>
            </div>
            <div class="modal-body">
                <!-- Add About SeekNFind content here -->
                <p>A web and mobile application designed to help users report and retrieve lost items. 
                    It offers an easy-to-use interface, alerts when items are found, and allows users to 
                    search for items based on specific details, reducing frustration and uncertainty associated 
                    with losing valuable items in the College of Computing Studies.</p>
                    <div class="mt-5">
            <h4 class="fw-semibold">Our Team</h4>
            </div>
            <div class="d-flex align-items-center mb-3">
            <img src="img/group1/perez.jpg" alt="perez" width="50" height="50" class="rounded-circle ms-2">
            <div class="d-flex flex-column align-items-start text-start">
                    <div class="fs-5 fw-semibold ms-3 mt-3">Perez, John Matthew</div>
                    <p class="ms-3">Leader / Programmer</p>
                    </div>
                    </div>
            <div class="d-flex align-items-center mb-3">
            <img src="img/group1/balaguer.jpg" alt="balaguer" width="50" height="50" class="rounded-circle ms-2">
            <div class="d-flex flex-column align-items-start text-start">
                    <div class="fs-5 fw-semibold ms-3 mt-3">Balaguer, Joem Manuel</div>
                    <p class="ms-3">Researcher</p>
                    </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
            <img src="img/group1/domalaon.jpg" alt="domalaon" width="50" height="50" class="rounded-circle ms-2">
            <div class="d-flex flex-column align-items-start text-start">
                    <div class="fs-5 fw-semibold ms-3 mt-3">Domalaon, Rendell</div>
                    <p class="ms-3">Researcher</p>
                    </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
            <img src="img/group1/nolasco.jpg" alt="nolasco" width="50" height="50" class="rounded-circle ms-2">
            <div class="d-flex flex-column align-items-start text-start">
                    <div class="fs-5 fw-semibold ms-3 mt-3">Nolasco, Mel John</div>
                    <p class="ms-3">Researcher</p>
                    </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
            <img src="img/group1/rario.jpg" alt="rario" width="50" height="50" class="rounded-circle ms-2">
            <div class="d-flex flex-column align-items-start text-start">
                    <div class="fs-5 fw-semibold ms-3 mt-3">Rario, Kieran Paul</div>
                    <p class="ms-3">Researcher</p>
                    </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
            <img src="img/group1/roxas.jpg" alt="roxas" width="50" height="50" class="rounded-circle ms-2">
            <div class="d-flex flex-column align-items-start text-start">
                    <div class="fs-5 fw-semibold ms-3 mt-3">Roxas, Rain Rhaili</div>
                    <p class="ms-3">Researcher</p>
                    </div>
                    </div>
                <div class="d-grid gap-2">
            <button type="button" class="btn snf_report" data-bs-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for user item details -->
<div class="modal fade" id="userItemModal<?php echo $row['item_id']; ?>" tabindex="-1" aria-labelledby="userItemModalLabel<?php echo $row['item_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="userItemModalLabel<?php echo $row['item_id']; ?>">Item Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <img src="<?php echo $row['picture_path']; ?>" class="img-fluid" alt="User Item">
                                    <p>Description: <?php echo $row['description']; ?></p>
                                    <p>Date: <?php echo $row['date_found']; ?></p>
                                    <p>Location: <?php echo $row['location_found']; ?></p>
                                </div>
                                <div class="modal-footer">
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn snf_button" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
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
