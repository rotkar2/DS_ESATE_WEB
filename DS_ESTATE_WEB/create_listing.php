<?php
session_start();
$conn = new mysqli('localhost', 'root', '5053', 'ds_estate');

$errors = [];
$success_message = '';
if (!isset($_SESSION['user_id'])) {
    $errors[] = 'Πρέπει να συνδεθείτε για να δημιουργήσετε αγγελία.';
} else {
    $user_id = $_SESSION['user_id'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $area = trim($_POST['area']);
    $rooms = trim($_POST['rooms']);
    $price_per_night = trim($_POST['price_per_night']);
    $photo = trim($_FILES['photo']['name']); // Χρησιμοποιούμε $_FILES για τη φωτογραφία

    // Έλεγχος για την ύπαρξη φωτογραφίας
    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Παρακαλώ επιλέξτε μια φωτογραφία για το ακίνητο.';
    }

    // Εισαγωγή δεδομένων στη βάση δεδομένων
    if (empty($errors)) {
        $query = "INSERT INTO listings (title, area, rooms, price_per_night, photo, user_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssiisi", $title, $area, $rooms, $price_per_night, $photo, $user_id);

        // Ανέβασμα της φωτογραφίας στον φάκελο φωτογραφιών (εάν χρειάζεται)

        // Εάν η εισαγωγή είναι επιτυχής
        if ($stmt->execute()) {
            $success_message = 'Η αγγελία αποθηκεύτηκε επιτυχώς!';
            header('Location: feed.php');
            exit();
        } else {
            $errors[] = 'Σφάλμα κατά την αποθήκευση της αγγελίας. Παρακαλώ δοκιμάστε ξανά.';
        }

        $stmt->close();
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Listing</title>
    <link rel="stylesheet" href="creatlist_apiar.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="feed.php">Feed</a></li>
            <li><a href="create_listing.php">Create Listing</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <div class="container">
        <h1>Δημιουργία Αγγελίας</h1>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="success">
                <p><?php echo htmlspecialchars($success_message); ?></p>
            </div>
        <?php endif; ?>

        <form id="create-listing-form" method="post" enctype="multipart/form-data">
            <label for="title">Τίτλος</label>
            <input type="text" id="title" name="title" required>
            <label for="area">Περιοχή</label>
            <input type="text" id="area" name="area" required>
            <label for="rooms">Πλήθος δωματίων</label>
            <input type="number" id="rooms" name="rooms" required>
            <label for="price_per_night">Τιμή ανά διανυκτέρευση</label>
            <input type="number" id="price_per_night" name="price_per_night" required>
            <label for="photo">Φωτογραφία του ακινήτου</label>
            <input type="file" id="photo" name="photo" required>
            <button type="submit" name="create_listing">Δημιουργία Αγγελίας</button>
        </form>
    </div>
    <footer>
        <p>&copy; 2024 DS Estate. All rights reserved.</p>
        <div>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.835434509367!2d144.96315791531654!3d-37.81410797975151!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad642af0f11fd81%3A0xf0727dbf2f010000!2sMelbourne%20CBD%2C%20VIC%2C%20Australia!5e0!3m2!1sen!2sau!4v1579204687807!5m2!1sen!2sau" width="400" height="250" style="border: 50px;" allowfullscreen="" loading="lazy"></iframe>
            <br></br>
            <a href="tel:+123456789">Τηλέφωνο: +123456789</a>
            <a href="mailto:info@dsestate.com">Email: info@dsestate.com</a>
        </div>
    </footer>
</body>
</html>