<?php
session_start();
include 'db.php';

$error="";

if(!isset($_SESSION['temp_user_id'])){
header("Location: index.php");
exit();
}

if($_SERVER["REQUEST_METHOD"]=="POST"){

$entered_otp = trim($_POST['otp']);
$user_id = $_SESSION['temp_user_id'];

$sql="SELECT * FROM users WHERE id='$user_id'";
$result=mysqli_query($conn,$sql);

$row=mysqli_fetch_assoc($result);

$db_otp = trim($row['otp']);

if($entered_otp === $db_otp){

$_SESSION['user_id']=$user_id;
$_SESSION['role']=$row['role'];

unset($_SESSION['temp_user_id']);

if($row['role']=="admin"){
header("Location: admin_dashboard.php");
}else{
header("Location: user_dashboard.php");
}

exit();

}else{
$error="Invalid OTP";
}
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Verify OTP</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Poppins;
}

body{
height:100vh;
background:linear-gradient(135deg,#667eea,#764ba2);
display:flex;
justify-content:center;
align-items:center;
}

.card{
background:white;
padding:40px;
border-radius:12px;
width:350px;
box-shadow:0 10px 25px rgba(0,0,0,0.2);
text-align:center;
}

h2{
margin-bottom:20px;
}

input{
width:100%;
padding:12px;
margin:10px 0;
border:1px solid #ccc;
border-radius:6px;
}

button{
width:100%;
padding:12px;
background:#667eea;
color:white;
border:none;
border-radius:6px;
font-size:16px;
cursor:pointer;
transition:0.3s;
}

button:hover{
background:#5a67d8;
}

.error{
color:red;
margin-bottom:10px;
}

</style>

</head>

<body>

<div class="card">

<h2>Verify OTP</h2>

<?php if($error!="") echo "<div class='error'>$error</div>"; ?>

<form method="POST">

<input type="text" name="otp" placeholder="Enter OTP" required>

<button type="submit">Verify OTP</button>

</form>

</div>

</body>
</html>