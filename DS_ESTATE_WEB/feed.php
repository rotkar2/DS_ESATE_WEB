<?php
session_start();
$conn = new mysqli('localhost', 'root', '5053', 'ds_estate');

// Έλεγχος σύνδεσης
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Εκτέλεση του ερωτήματος για ανάκτηση των listings
$sql = "SELECT * FROM listings";
$result = $conn->query($sql);

// Έλεγχος εκτέλεσης ερωτήματος
if ($result === false) {
    die("Error executing query: " . $conn->error);
}

// Αποθήκευση αποτελεσμάτων σε πίνακα
$listings = [];
while ($row = $result->fetch_assoc()) {
    $listings[] = $row;
}

// Κλείσιμο σύνδεσης
$conn->close();
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed</title>
    <link rel="stylesheet" href="apiar.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="feed.php">Feed</a></li>
            <li><a href="create_listing.php">Create Listing</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <div>
        <div class="container">
            <h1>Διαθέσιμα Ακίνητα</h1>
            <?php if (empty($listings)): ?>
                <p>Δεν υπάρχουν διαθέσιμα ακίνητα.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($listings as $listing): ?>
                        <li>
                            <img src="<?php echo htmlspecialchars($listing['photo']); ?>" alt="Φωτογραφία ακινήτου">
                            <h2><?php echo htmlspecialchars($listing['title']); ?></h2>
                            <p>Περιοχή: <?php echo isset($listing['area']) ? htmlspecialchars($listing['area']) : 'Μη διαθέσιμο'; ?></p>
                            <p>Πλήθος δωματίων: <?php echo htmlspecialchars($listing['rooms']); ?></p>
                            <p>Τιμή ανά διανυκτέρευση: <?php echo htmlspecialchars($listing['price_per_night']); ?>€</p>
                            <form action="book.php" method="post">
                                <input type="hidden" name="listing_id" value="<?php echo htmlspecialchars($listing['id']); ?>">
                                <button type="submit">Κράτηση</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
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