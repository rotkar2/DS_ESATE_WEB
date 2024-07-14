<?php
session_start();
$conn = new mysqli('localhost', 'root', '5053', 'ds_estate');

// Έλεγχος σύνδεσης
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        // Login logic
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        // Fetch user from database
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
        if ($stmt === false) {
            die('Error in prepare: ' . $conn->error);
        }
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            die('Error in execute: ' . $stmt->error);
        }
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashed_password = $row['password'];
            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $row['id'];
                header("Location: feed.php");
                exit;
            } else {
                $errors[] = "Λάθος κωδικός πρόσβασης.";
            }
        } else {
            $errors[] = "Το όνομα χρήστη δεν βρέθηκε.";
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
    <title>Login/Register</title>
    <link rel="stylesheet" href="login_apiar.css">
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
    <div class="container">
        <h1>Σύνδεση / Εγγραφή</h1>

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

        <form id="login-form" method="post">
            <h2>Σύνδεση</h2>
            <label for="login-username">Όνομα Χρήστη</label>
            <input type="text" id="login-username" name="username" required>
            <label for="login-password">Κωδικός Πρόσβασης</label>
            <input type="password" id="login-password" name="password" required>
            <button type="submit" name="login">Σύνδεση</button>
        </form>

        <form id="register-form" method="post">
            <h2>Εγγραφή</h2>
            <label for="register-first-name">Όνομα</label>
            <input type="text" id="register-first-name" name="first_name" required>
            <label for="register-last-name">Επώνυμο</label>
            <input type="text" id="register-last-name" name="last_name" required>
            <label for="register-username">Όνομα Χρήστη</label>
            <input type="text" id="register-username" name="username" required>
            <label for="register-password">Κωδικός Πρόσβασης</label>
            <input type="password" id="register-password" name="password" required>
            <label for="register-email">Email</label>
            <input type="email" id="register-email" name="email" required>
            <button type="submit" name="register">Εγγραφή</button>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginLink = document.getElementById('login-link');
            const logoutLink = document.getElementById('logout-link');

            // Έλεγχος αν ο χρήστης είναι συνδεδεμένος
            const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;

            if (isLoggedIn) {
                loginLink.style.display = 'none';
                logoutLink.style.display = 'inline';
            } else {
                loginLink.style.display = 'inline';
                logoutLink.style.display = 'none';
            }
        });
    </script>
</body>
</html>