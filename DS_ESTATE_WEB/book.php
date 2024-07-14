<?php
session_start();
$conn = new mysqli('localhost', 'root', '5053', 'ds_estate');

// Έλεγχος αν ο χρήστης είναι συνδεδεμένος
if (!isset($_SESSION['user_id'])) {
    // Αποθηκεύστε το listing_id στη συνεδρία και ανακατευθύνετε τον χρήστη στη σελίδα login
    if (isset($_POST['listing_id'])) {
        $_SESSION['redirect_listing_id'] = $_POST['listing_id'];
    }
    header("Location: login.php?redirect_to=book.php");
    exit();
}

$errors = [];
$success_message = '';

// Έλεγχος σύνδεσης
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Αν το listing_id έχει αποθηκευτεί στη συνεδρία, χρησιμοποιήστε το
if (isset($_SESSION['redirect_listing_id'])) {
    $listing_id = $_SESSION['redirect_listing_id'];
    unset($_SESSION['redirect_listing_id']);
} else {
    // Αν δεν έχει αποθηκευτεί, λάβετε το από τη φόρμα
    if (isset($_POST['listing_id'])) {
        $listing_id = $_POST['listing_id'];
    } else {
        $errors[] = 'Δεν έχει επιλεγεί ακίνητο για κράτηση.';
    }
}

$user_id = $_SESSION['user_id'];

// Εκτέλεση της κράτησης αν έχουν σταλεί δεδομένα POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($listing_id)) {
    // Έλεγχος αν έχουν σταλεί ημερομηνίες και αν είναι έγκυρες
    if (isset($_POST['check_in_date']) && isset($_POST['check_out_date'])) {
        $check_in_date = $_POST['check_in_date'];
        $check_out_date = $_POST['check_out_date'];

        // Έλεγχος αν ο χρήστης έχει ήδη κάνει κράτηση για αυτό το διάστημα
        $query_check = "SELECT * FROM reservations WHERE listing_id = ? AND ((check_in_date <= ? AND check_out_date >= ?) OR (check_in_date <= ? AND check_out_date >= ?))";
        $stmt_check = $conn->prepare($query_check);
        $stmt_check->bind_param("issss", $listing_id, $check_in_date, $check_in_date, $check_out_date, $check_out_date);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $errors[] = 'Το ακίνητο δεν είναι διαθέσιμο για τις επιλεγμένες ημερομηνίες.';
        } else {
            // Εισαγωγή δεδομένων στη βάση δεδομένων
            $query = "INSERT INTO reservations (listing_id, user_id, check_in_date, check_out_date) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iiss", $listing_id, $user_id, $check_in_date, $check_out_date);

            if ($stmt->execute()) {
                $success_message = 'Η κράτηση πραγματοποιήθηκε επιτυχώς!';
            } else {
                $errors[] = 'Σφάλμα κατά την καταχώρηση της κράτησης. Παρακαλώ δοκιμάστε ξανά.';
            }

            $stmt->close();
        }
    } else {
        $errors[] = 'Δεν έχουν καθοριστεί ημερομηνίες για την κράτηση.';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Κράτηση Ακινήτου</title>
    <link rel="stylesheet" href="book.css">
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
        <h1>Κράτηση Ακινήτου</h1>

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

        <form id="booking-form" method="post">
            <input type="hidden" name="listing_id" value="<?php echo htmlspecialchars($listing_id); ?>">
            <label for="check_in_date">Ημερομηνία Άφιξης</label>
            <input type="date" id="check_in_date" name="check_in_date" required>
            <label for="check_out_date">Ημερομηνία Αναχώρησης</label>
            <input type="date" id="check_out_date" name="check_out_date" required>
            <button type="submit">Κάντε Κράτηση</button>
        </form>
    </div>
    <footer>
        <p>&copy; 2024 DS Estate. Με επιφύλαξη παντός δικαιώματος.</p>
        <div>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.835434509367!2d144.96315791531654!3d-37.81410797975151!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad642af0f11fd81%3A0xf0727dbf2f010000!2sMelbourne%20CBD%2C%20VIC%2C%20Australia!5e0!3m2!1sen!2sau!4v1579204687807!5m2!1sen!2sau" width="400" height="250" style="border: 50px;" allowfullscreen="" loading="lazy"></iframe>
            <br></br>
            <a href="tel:+123456789">Τηλέφωνο: +123456789</a>
            <a href="mailto:info@dsestate.com">Email: info@dsestate.com</a>
        </div>
    </footer>
</body>
</html>