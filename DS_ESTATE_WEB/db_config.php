<?php
$servername = "localhost";
$username = "root";
$password = "5053";
$dbname = "ds_estate";

// Δημιουργία σύνδεσης
$conn = new mysqli($servername, $username, $password, $dbname);

// Έλεγχος σύνδεσης
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>