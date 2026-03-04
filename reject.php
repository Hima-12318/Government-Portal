<?php
include 'db.php';
$id=$_GET['id'];
mysqli_query($conn,"UPDATE applications SET status='Rejected' WHERE id=$id");
header("Location: admin_dashboard.php");
?>