<?php
$conn = mysqli_connect("localhost", "root", "", "gov_portal");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>